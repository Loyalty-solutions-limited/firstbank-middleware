<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

define('PERX_ENDPOINT',   'https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php');
define('BATCH_SIZE', 5000);
define('SLEEP_WHEN_IDLE', 5);
define('SLEEP_ON_ERROR', 10); //increased to give VPN time to recover
define('MAX_PROCESSING_MINUTES', 5); // reset stuck rows after 5 minutes


function pushToPERX(string $url, array $payload): array
{
    $json  = json_encode($payload);

    $compressedJson = gzencode($json , 9);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $compressedJson,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Encoding: gzip',
            'Content-Length: ' . strlen($compressedJson),
        ],
        CURLOPT_TIMEOUT=> 120, // increased from 60
        CURLOPT_CONNECTTIMEOUT => 15, // increased from 10
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
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

// ── Startup: reset any rows stuck at status=1 from a previous crashed run ────
// This handles the case where the script died mid-batch last time
$stuckCount = DB::table('qualified_transactions')->where('status', 1)->count();
if ($stuckCount > 0) {
    DB::table('qualified_transactions')->where('status', 1)->update(['status' => 0]);
    logLine("Startup: reset $stuckCount stuck rows from previous run back to pending.");
}
logLine("Data mover started. Batch size: " . BATCH_SIZE);

while (true) {

    try {
        // ── Reset any rows stuck at status=1 for too long ────────────────────
        // Catches cases where a previous loop iteration died mid-push
        $resetCount = DB::table('qualified_transactions')->where('status', 1)->where('updated_at', '<', now()
            ->subMinutes(MAX_PROCESSING_MINUTES))->update(['status' => 0]);

        if ($resetCount > 0) {
                logLine("Reset $resetCount rows stuck in processing back to pending.");
        }

        // ── Step 1: Atomically claim a batch ─────────────────────────────────
        $claimed = DB::table('qualified_transactions')->where('status', 0)->limit(BATCH_SIZE)->update(['status' => 1]);

        if ($claimed === 0) {
            logLine("No pending rows. Sleeping " . SLEEP_WHEN_IDLE . "s...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }
        // ── Step 2: Fetch exactly the rows we just claimed ───────────────────
        $rows = DB::table('qualified_transactions')->where('status', 1)->limit(BATCH_SIZE)->get()->unique('transaction_reference');

        if ($rows->isEmpty()) {
            logLine("Claimed rows but fetch returned empty. Sleeping...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }
        $ids = $rows->pluck('id')->toArray();

        // ── Step 3: Build payload ─────────────────────────────────────────────
        $payload = $rows->map(fn($t) => [
            'Membership_ID' => $t->member_reference,
            'Acid' => $t->account_number,
            'Transaction_Date' => $t->transaction_date,
            'Transaction_Type_code' => $t->transaction_type,
            'Transaction_channel_code' => $t->channel,
            'Transaction_amount' => $t->amount,
            'Branch_code' => $t->branch_code,
            'Transaction_ID' => $t->transaction_reference,
            'Product_Code' => $t->product_code,
            'Product_Quantity' => $t->quantity,
            'id' => $t->id,
        ])->values()->toArray();

        logLine("Pushing " . count($ids) . " records to PERX...");

        // ── Step 4: Push to staging endpoint ─────────────────────────────────
        $resp = pushToPERX(PERX_ENDPOINT, $payload);

        if (($resp['status'] ?? '') === 'ok') {
            DB::table('qualified_transactions')->whereIn('id', $ids)->update(['status' => 2]);

            logLine("SUCCESS — " . count($ids) . " sent. Inserted: " . ($resp['inserted'] ?? 'unknown'));
        } else {
            // Unexpected response — reset to retry
            DB::table('qualified_transactions')->whereIn('id', $ids)->update(['status' => 0]);
            logLine("WARNING — unexpected response, rows reset to pending: " . json_encode($resp));
            sleep(SLEEP_ON_ERROR);
        }
    } catch (\Exception $e) {
        logLine("ERROR — " . $e->getMessage());
        // Reset stuck rows back to pending
        try {
            DB::table('qualified_transactions')->where('status', 1)->update(['status' => 0]);
            logLine("Rows reset to pending after error.");
        } catch (\Exception $dbError) {
            // DB itself is down — log and wait
            logLine("CRITICAL — could not reset rows, DB error: " . $dbError->getMessage());
        }
        sleep(SLEEP_ON_ERROR);
    }
}
