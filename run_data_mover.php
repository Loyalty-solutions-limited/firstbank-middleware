<?php
 
/**
* run_data_mover.php
*
* Persistent daemon. Runs forever, draining qualified_transactions
* and pushing batches to the PERX staging endpoint.
*
* Status values:
*   0 = pending
*   1 = processing (claimed by this daemon, currently being sent)
*   2 = done (successfully sent and confirmed)
*
* To test manually:  php run_data_mover.php
* To run properly:   managed by Supervisor
*/
 
// ─── Bootstrap Laravel ───────────────────────────────────────────────────────
require __DIR__ . '/vendor/autoload.php';
 
$app = require_once __DIR__ . '/bootstrap/app.php';
 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
// ─────────────────────────────────────────────────────────────────────────────
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 
// ─── Config ──────────────────────────────────────────────────────────────────
define('PERX_ENDPOINT',   'https://firstbankstagedatascripts-dqe3anetczeqctce.francecentral-01.azurewebsites.net/staging/stage_data.php');
define('BATCH_SIZE',      5000);
define('SLEEP_WHEN_IDLE', 5);  // seconds to wait when no pending rows
define('SLEEP_ON_ERROR',  3);  // seconds to wait after a failed push
// ─────────────────────────────────────────────────────────────────────────────
 
function pushToPERX(string $url, array $payload): array
{
    $json = json_encode($payload);
 
    // Compress the JSON payload with Gzip
    $compressedJson = gzencode($json, 9);
 
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $compressedJson,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Encoding: gzip',
            'Content-Length: ' . strlen($compressedJson),
        ],
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
 
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
 
    if ($error) {
        throw new \RuntimeException("cURL error: $error");
    }
 
    if ($httpCode !== 200) {
        throw new \RuntimeException("HTTP $httpCode from PERX endpoint");
    }
 
    $decoded = json_decode($response, true);
 
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException("Invalid JSON response: $response");
    }
 
    return $decoded;
}
 
function logLine(string $message): void
{
    $ts = date('Y-m-d H:i:s');
    echo "[$ts] $message\n";
    Log::info("[DataMover] $message");
}
 
// ─── Main loop ───────────────────────────────────────────────────────────────
logLine("Data mover started. Batch size: " . BATCH_SIZE);
 
while (true) {
 
    try {
 
        // ── Step 1: Atomically claim a batch ─────────────────────────────────
        // Update FIRST before selecting — this prevents any second instance
        // or restart from picking up the same rows simultaneously.
        $claimed = DB::table('qualified_transactions')
            ->where('status', 0)
            ->limit(BATCH_SIZE)
            ->update(['status' => 1]);
 
        if ($claimed === 0) {
            logLine("No pending rows. Sleeping " . SLEEP_WHEN_IDLE . "s...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }
 
        // ── Step 2: Fetch exactly the rows we just claimed ───────────────────
        $rows = DB::table('qualified_transactions')
            ->where('status', 1)
            ->limit(BATCH_SIZE)
            ->get()
            ->unique('transaction_reference');
 
        if ($rows->isEmpty()) {
            logLine("Claimed rows but fetch returned empty. Sleeping...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }
 
        $ids = $rows->pluck('id')->toArray();
 
        // ── Step 3: Build payload ─────────────────────────────────────────────
        $payload = $rows->map(fn($t) => [
            'Membership_ID'            => $t->member_reference,
            'Acid'                     => $t->account_number,
            'Transaction_Date'         => $t->transaction_date,
            'Transaction_Type_code'    => $t->transaction_type,
            'Transaction_channel_code' => $t->channel,
            'Transaction_amount'       => $t->amount,
            'Branch_code'              => $t->branch_code,
            'Transaction_ID'           => $t->transaction_reference,
            'Product_Code'             => $t->product_code,
            'Product_Quantity'         => $t->quantity,
            'id'                       => $t->id,
        ])->values()->toArray();
 
        logLine("Pushing " . count($ids) . " records to PERX...");
 
        // ── Step 4: Push to staging endpoint ─────────────────────────────────
        $resp = pushToPERX(PERX_ENDPOINT, $payload);
 
        if (($resp['status'] ?? '') === 'ok') {
 
            // ✅ Confirmed receipt — mark as fully done
            DB::table('qualified_transactions')
                ->whereIn('id', $ids)
                ->update(['status' => 2]);
 
            logLine("SUCCESS — " . count($ids) . " records sent and marked done. Inserted: " . ($resp['inserted'] ?? 'unknown'));
 
        } else {
 
            // Endpoint returned 200 but not 'ok' — reset rows so they retry
            DB::table('qualified_transactions')
                ->whereIn('id', $ids)
                ->update(['status' => 0]);
 
            logLine("WARNING — unexpected response, rows reset to pending: " . json_encode($resp));
            sleep(SLEEP_ON_ERROR);
        }
 
    } catch (\Exception $e) {
 
        logLine("ERROR — " . $e->getMessage());
 
        // Reset any rows stuck in 'processing' back to pending
        // so they are not permanently lost
        DB::table('qualified_transactions')
            ->where('status', 1)
            ->update(['status' => 0]);
 
        sleep(SLEEP_ON_ERROR);
    }
 
    // No sleep here when rows were found — immediately process next batch
}