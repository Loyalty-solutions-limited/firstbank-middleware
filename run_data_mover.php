<?php

/**
 * run_data_mover.php
 *
 * Persistent daemon. Runs forever, draining qualified_transactions
 * and pushing batches to the PERX staging endpoint.
 *
 * Status values:
 *   0 = pending       (waiting to be processed)
 *   1 = processing    (claimed by this daemon, currently being sent)
 *   2 = done          (successfully sent and confirmed, never touch again)
 *
 * To test manually:  php run_data_mover.php
 * To run properly:   managed by Supervisor / systemd
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ─── Config ───────────────────────────────────────────────────────────────────
define('PERX_ENDPOINT',       env('PERX_ENDPOINT', 'https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php'));
define('BATCH_SIZE',          5000);
define('SLEEP_WHEN_IDLE',     5);    // seconds to wait when no pending rows
define('SLEEP_ON_ERROR',      10);   // seconds to wait after a failed push
define('STUCK_LOOP_MINUTES',  3);    // rows stuck at status=1 for 3+ minutes during running = reset
define('STUCK_HOURS_HARD',    24);   // rows stuck at status=1 for 24+ hours = force reset regardless
// ─────────────────────────────────────────────────────────────────────────────

function pushToPERX(string $url, array $payload): array
{
    $json           = json_encode($payload);
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
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_CONNECTTIMEOUT => 15,
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

// ─── STARTUP RESETS ───────────────────────────────────────────────────────────
// Runs ONCE when the script boots. Since the script is just starting,
// nothing is currently in flight. Any status=1 row is abandoned from
// a previous run and must be retried.
// ─────────────────────────────────────────────────────────────────────────────

// Reset ALL status=1 rows unconditionally on startup
$startupStuck = DB::table('qualified_transactions')
    ->where('status', 1)
    ->count();

if ($startupStuck > 0) {
    DB::table('qualified_transactions')
        ->where('status', 1)
        ->update(['status' => 0]);
    logLine("STARTUP — reset $startupStuck abandoned rows back to pending.");
} else {
    logLine("STARTUP — no abandoned rows found. Clean start.");
}

logLine("Data mover started. Batch size: " . BATCH_SIZE);

// ─── Main loop ────────────────────────────────────────────────────────────────
$loopCounter = 0; // used to run the 24hr hard reset check periodically

while (true) {

    try {

        $loopCounter++;

        // ── Hard reset: every 100 loops check for rows stuck over 24 hours ───
        // This is the safety net for rows that somehow slipped through
        // the startup reset or got stuck during a very long running push.
        // Runs every 100 loops (~every 8-10 minutes) to avoid hammering the DB.
        if ($loopCounter % 100 === 0) {
            $hardStuck = DB::table('qualified_transactions')
                ->where('status', 1)
                ->where('updated_at', '<', now()->subHours(STUCK_HOURS_HARD))
                ->count();

            if ($hardStuck > 0) {
                DB::table('qualified_transactions')
                    ->where('status', 1)
                    ->where('updated_at', '<', now()->subHours(STUCK_HOURS_HARD))
                    ->update(['status' => 0]);
                logLine("HARD RESET — reset $hardStuck rows stuck for over 24 hours back to pending.");
            }
        }

        // ── Step 1: Atomically claim a batch of status=0 rows ────────────────
        // Update FIRST before fetching — prevents two instances
        // from ever claiming the same rows simultaneously.
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

            // ✅ Confirmed — mark as fully done, never touched again
            DB::table('qualified_transactions')
                ->whereIn('id', $ids)
                ->update(['status' => 2]);

            logLine("SUCCESS — " . count($ids) . " sent and marked done. Inserted: " . ($resp['inserted'] ?? 'unknown'));

        } else {

            // Endpoint responded but not with ok — reset this batch to retry
            DB::table('qualified_transactions')
                ->whereIn('id', $ids)
                ->update(['status' => 0]);

            logLine("WARNING — unexpected response, batch reset to pending: " . json_encode($resp));
            sleep(SLEEP_ON_ERROR);
        }

    } catch (\Exception $e) {

        logLine("ERROR — " . $e->getMessage());

        // Reset only the rows THIS iteration claimed
        // Never reset the entire table — only what we own right now
        try {
            if (!empty($ids)) {
                DB::table('qualified_transactions')
                    ->whereIn('id', $ids)
                    ->where('status', 1)
                    ->update(['status' => 0]);
                logLine("ERROR RECOVERY — reset " . count($ids) . " rows from this batch back to pending.");
            }
        } catch (\Exception $dbError) {
            logLine("CRITICAL — could not reset rows, DB error: " . $dbError->getMessage());
        }

        // Clear ids so the next loop does not accidentally reference this batch
        $ids = [];

        sleep(SLEEP_ON_ERROR);
    }
}
