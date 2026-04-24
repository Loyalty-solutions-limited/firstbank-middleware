<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

define('PERX_ENDPOINT', env('PERX_ENDPOINT', 'https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php'));

define('CLAIM_SIZE', 5000);          // number of source rows to claim at once
define('SEND_CHUNK_SIZE', 1000);     // number of unique transaction refs to send per request
define('SLEEP_WHEN_IDLE', 5);
define('SLEEP_ON_ERROR', 10);

define('CHUNK_MAX_RETRIES', 3);
define('CHUNK_RETRY_BASE_SLEEP', 3);

define('CONNECT_TIMEOUT', 20);
define('REQUEST_TIMEOUT', 180);

define('LOCK_FILE', '/tmp/firstbankmiddleware_data_mover.lock');

function logLine(string $message): void
{
    $ts = date('Y-m-d H:i:s');
    echo "[$ts] $message\n";
    Log::info("[DataMover] $message");
}

function acquireSingleInstanceLock()
{
    $fp = fopen(LOCK_FILE, 'c');

    if (!$fp) {
        throw new RuntimeException('Could not open lock file: ' . LOCK_FILE);
    }

    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        logLine('Another worker instance is already running. Exiting.');
        exit(0);
    }

    fwrite($fp, getmypid() . PHP_EOL);
    fflush($fp);

    return $fp;
}

function pushToPERX(string $url, array $payload): array
{
    $json = json_encode($payload);

    if ($json === false) {
        throw new RuntimeException('JSON encode failed: ' . json_last_error_msg());
    }

    $compressedJson = gzencode($json, 9);

    if ($compressedJson === false) {
        throw new RuntimeException('gzip compression failed');
    }

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
        CURLOPT_TIMEOUT => REQUEST_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => CONNECT_TIMEOUT,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
        throw new RuntimeException("cURL error: $error");
    }

    if ($httpCode !== 200) {
        throw new RuntimeException("HTTP $httpCode from PERX endpoint. Response: $response");
    }

    $decoded = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException("Invalid JSON response: $response");
    }

    return $decoded;
}

function sendChunkWithRetry(array $chunk, int $chunkIndex, int $totalChunks): array
{
    $attempt = 0;
    $lastError = null;

    while ($attempt < CHUNK_MAX_RETRIES) {
        $attempt++;

        try {
            logLine("Sending chunk {$chunkIndex}/{$totalChunks}, attempt {$attempt}, size " . count($chunk) . "...");
            $resp = pushToPERX(PERX_ENDPOINT, $chunk);

            if (($resp['status'] ?? '') !== 'ok') {
                throw new RuntimeException("Unexpected endpoint response: " . json_encode($resp));
            }

            return $resp;
        } catch (\Throwable $e) {
            $lastError = $e;
            logLine("Chunk {$chunkIndex}/{$totalChunks} failed on attempt {$attempt}: " . $e->getMessage());

            if ($attempt < CHUNK_MAX_RETRIES) {
                sleep(CHUNK_RETRY_BASE_SLEEP * $attempt);
            }
        }
    }

    throw new RuntimeException(
        "Chunk {$chunkIndex}/{$totalChunks} failed after " . CHUNK_MAX_RETRIES .
        " attempts. Last error: " . ($lastError ? $lastError->getMessage() : 'unknown')
    );
}

function resetAllProcessingRowsOnStartup(): int
{
    return DB::table('qualified_transactions')
        ->where('status', 1)
        ->update(['status' => 0]);
}

function resetClaimedRowsByIds(array $ids): int
{
    if (empty($ids)) {
        return 0;
    }

    return DB::table('qualified_transactions')
        ->whereIn('id', $ids)
        ->where('status', 1)
        ->update(['status' => 0]);
}

function markRefsDone(array $refs): int
{
    if (empty($refs)) {
        return 0;
    }

    // mark all duplicates of successful refs as done too
    return DB::table('qualified_transactions')
        ->whereIn('transaction_reference', $refs)
        ->whereIn('status', [0, 1])
        ->update(['status' => 2]);
}

// enforce single worker
$lockHandle = acquireSingleInstanceLock();

// startup recovery
$startupReset = resetAllProcessingRowsOnStartup();
logLine("STARTUP — reset {$startupReset} rows from processing back to pending.");
logLine("Data mover started. Claim size: " . CLAIM_SIZE . ", send chunk size: " . SEND_CHUNK_SIZE);

while (true) {
    $claimedIds = [];

    try {
        // Step 1: select candidate pending rows
        $candidates = DB::table('qualified_transactions')
            ->select([
                'id',
                'member_reference',
                'account_number',
                'transaction_date',
                'transaction_type',
                'channel',
                'amount',
                'branch_code',
                'transaction_reference',
                'product_code',
                'quantity',
            ])
            ->where('status', 0)
            ->whereNotNull('transaction_reference')
            ->where('transaction_reference', '!=', '')
            ->orderBy('id')
            ->limit(CLAIM_SIZE)
            ->get();

        if ($candidates->isEmpty()) {
            logLine("No pending rows. Sleeping " . SLEEP_WHEN_IDLE . "s...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        $candidateIds = $candidates->pluck('id')->toArray();

        // Step 2: claim only those exact IDs
        $claimed = DB::table('qualified_transactions')
            ->whereIn('id', $candidateIds)
            ->where('status', 0)
            ->update(['status' => 1]);

        if ($claimed === 0) {
            logLine("Could not claim rows. Sleeping...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        // Step 3: re-fetch only the exact rows we claimed
        $rows = DB::table('qualified_transactions')
            ->select([
                'id',
                'member_reference',
                'account_number',
                'transaction_date',
                'transaction_type',
                'channel',
                'amount',
                'branch_code',
                'transaction_reference',
                'product_code',
                'quantity',
            ])
            ->whereIn('id', $candidateIds)
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            logLine("Claimed rows but re-fetch returned empty. Sleeping...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        $claimedIds = $rows->pluck('id')->toArray();

        // Step 4: de-duplicate by transaction_reference
        $uniqueMap = [];

        foreach ($rows as $row) {
            $ref = trim((string) $row->transaction_reference);

            if ($ref === '') {
                continue;
            }

            if (!isset($uniqueMap[$ref])) {
                $uniqueMap[$ref] = [
                    'Membership_ID'            => $row->member_reference,
                    'Acid'                     => $row->account_number,
                    'Transaction_Date'         => $row->transaction_date,
                    'Transaction_Type_code'    => $row->transaction_type,
                    'Transaction_channel_code' => $row->channel,
                    'Transaction_amount'       => $row->amount,
                    'Branch_code'              => $row->branch_code,
                    'Transaction_ID'           => $row->transaction_reference,
                    'Product_Code'             => $row->product_code,
                    'Product_Quantity'         => $row->quantity,
                ];
            }
        }

        $uniquePayload = array_values($uniqueMap);

        if (empty($uniquePayload)) {
            $reset = resetClaimedRowsByIds($claimedIds);
            logLine("No valid transaction references found in claimed batch. Reset {$reset} rows.");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        logLine(
            "Claimed " . count($claimedIds) .
            " source rows; " . count($uniquePayload) .
            " unique transaction references ready to send."
        );

        // Step 5: send in chunks and mark successful refs as done
        $chunks = array_chunk($uniquePayload, SEND_CHUNK_SIZE);
        $totalChunks = count($chunks);

        foreach ($chunks as $index => $chunk) {
            $chunkIndex = $index + 1;

            $chunkRefs = array_values(array_filter(array_map(
                fn($item) => $item['Transaction_ID'] ?? null,
                $chunk
            )));

            $resp = sendChunkWithRetry($chunk, $chunkIndex, $totalChunks);

            $doneCount = markRefsDone($chunkRefs);

            logLine(
                "Chunk {$chunkIndex}/{$totalChunks} OK — endpoint inserted " .
                ($resp['inserted'] ?? 'unknown') .
                ", marked {$doneCount} source rows as done."
            );
        }

        logLine("BATCH COMPLETE — claimed " . count($claimedIds) . " source rows successfully.");

    } catch (\Throwable $e) {
        logLine("ERROR — " . $e->getMessage());

        try {
            $reset = resetClaimedRowsByIds($claimedIds);
            if ($reset > 0) {
                logLine("ERROR RECOVERY — reset {$reset} claimed rows back to pending.");
            }
        } catch (\Throwable $dbError) {
            logLine("CRITICAL — failed to reset claimed rows: " . $dbError->getMessage());
        }

        sleep(SLEEP_ON_ERROR);
    }
}
