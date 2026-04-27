<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

define('PERX_ENDPOINT', 'https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php');

define('CLAIM_SIZE', 5000);
define('SEND_CHUNK_SIZE', 100);
define('SLEEP_WHEN_IDLE', 5);
define('SLEEP_ON_ERROR', 10);

define('CHUNK_MAX_RETRIES', 3);
define('CHUNK_RETRY_BASE_SLEEP', 3);

define('CONNECT_TIMEOUT', 20);
define('REQUEST_TIMEOUT', 180);

define('LOCK_FILE', '/tmp/firstbankmiddleware_data_mover.lock');

// Oracle allows a maximum of 1000 expressions in an IN list (ORA-01795).
define('ORACLE_IN_LIMIT', 999);

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

    logLine("Payload sizes — JSON: " . strlen($json) . " bytes, Gzip: " . strlen($compressedJson) . " bytes");

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $compressedJson,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Encoding: gzip',
            'Content-Length: ' . strlen($compressedJson),
            'Expect:',
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

/**
 * Mark all source rows for the given transaction references as done (status=2).
 * Chunked to stay within Oracle's 999-item IN clause limit.
 */
function markRefsDone(array $refs): int
{
    if (empty($refs)) {
        return 0;
    }

    $done = 0;

    foreach (array_chunk($refs, ORACLE_IN_LIMIT) as $chunk) {
        $done += DB::table('qualified_transactions')
            ->whereIn('transaction_reference', $chunk)
            ->whereIn('status', [0, 1])
            ->update(['status' => 2]);
    }

    return $done;
}

/**
 * Extract a column value from a stdClass row in a case-insensitive way.
 * Oracle returns column names in UPPERCASE; MySQL returns them as-is.
 */
function col(object $row, string $name): mixed
{
    $arr = (array) $row;
    return $arr[$name] ?? $arr[strtoupper($name)] ?? null;
}

// enforce single worker
$lockHandle = acquireSingleInstanceLock();

$isOracle = in_array(DB::connection()->getDriverName(), ['oci8', 'oracle']);
logLine("DB driver: " . DB::connection()->getDriverName());

// Startup schema diagnostic — logs every column name, PHP type, and sample value
// from the first row. Use this to confirm column names and types.
$sampleRows = $isOracle
    ? DB::select('SELECT * FROM qualified_transactions WHERE ROWNUM <= 1')
    : DB::select('SELECT * FROM qualified_transactions LIMIT 1');
if (!empty($sampleRows)) {
    $arr = (array) $sampleRows[0];
    $parts = [];
    foreach ($arr as $k => $v) {
        $type = gettype($v);
        $displayVal = is_null($v)
            ? 'NULL'
            : (is_object($v) ? 'OBJ(' . get_class($v) . ')' : substr((string) $v, 0, 25));
        $parts[] = "{$k}({$type})={$displayVal}";
    }
    logLine("SCHEMA — " . implode(' | ', $parts));
} else {
    logLine("SCHEMA — table is empty or inaccessible");
}

logLine("ENDPOINT — " . PERX_ENDPOINT);

// startup recovery
$startupReset = resetAllProcessingRowsOnStartup();
logLine("STARTUP — reset {$startupReset} rows from processing back to pending.");
logLine("Data mover started. Claim size: " . CLAIM_SIZE . ", send chunk size: " . SEND_CHUNK_SIZE);

while (true) {
    try {
        // Step 1: Claim up to CLAIM_SIZE pending rows atomically using Oracle ROWID.
        //
        // We use ROWID (Oracle's internal physical row address) instead of the 'id'
        // column because 'id' was found to be NULL in this Oracle environment — it
        // exists as a column but was never populated during the data import.
        // ROWID is always present, unique, and non-null for every row.
        //
        // The two-level subquery is required in Oracle: ROWNUM is assigned before
        // ORDER BY executes, so limiting must happen in an outer query.
        // Here we don't need a specific ORDER BY — any CLAIM_SIZE pending rows
        // are fine to claim.
        if ($isOracle) {
            $claimed = DB::affectingStatement(
                "UPDATE qualified_transactions
                 SET status = 1
                 WHERE ROWID IN (
                     SELECT row_id FROM (
                         SELECT ROWID AS row_id
                         FROM qualified_transactions
                         WHERE status = 0
                         AND transaction_reference IS NOT NULL
                         AND LENGTH(TRIM(transaction_reference)) > 0
                     ) WHERE ROWNUM <= " . intval(CLAIM_SIZE) . "
                 ) AND status = 0"
            );
        } else {
            $claimed = DB::affectingStatement(
                "UPDATE qualified_transactions
                 SET status = 1
                 WHERE id IN (
                     SELECT id FROM (
                         SELECT id
                         FROM qualified_transactions
                         WHERE status = 0
                         AND transaction_reference IS NOT NULL
                         AND TRIM(transaction_reference) != ''
                         LIMIT " . intval(CLAIM_SIZE) . "
                     ) AS tmp
                 ) AND status = 0"
            );
        }

        if ($claimed === 0) {
            logLine("No pending rows. Sleeping " . SLEEP_WHEN_IDLE . "s...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        logLine("Claimed {$claimed} rows, re-fetching data...");

        // Step 2: Re-fetch the rows we just claimed.
        // We select by status=1. This is safe because we are the only worker
        // (enforced by the lock file). No LIMIT here — avoids the yajra/laravel-oci8
        // ROWNUM wrapper that was causing column values to come back as null.
        $rows = DB::table('qualified_transactions')
            ->select([
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
            ->where('status', 1)
            ->get();

        if ($rows->isEmpty()) {
            logLine("Re-fetch returned empty. Sleeping...");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        // Step 3: De-duplicate by transaction_reference.
        $uniqueMap = [];

        foreach ($rows as $row) {
            $ref = trim((string) col($row, 'transaction_reference'));

            if ($ref === '') {
                continue;
            }

            if (!isset($uniqueMap[$ref])) {
                $uniqueMap[$ref] = [
                    'Membership_ID'            => col($row, 'member_reference'),
                    'Acid'                     => col($row, 'account_number'),
                    'Transaction_Date'         => col($row, 'transaction_date'),
                    'Transaction_Type_code'    => col($row, 'transaction_type'),
                    'Transaction_channel_code' => col($row, 'channel'),
                    'Transaction_amount'       => col($row, 'amount'),
                    'Branch_code'              => col($row, 'branch_code'),
                    'Transaction_ID'           => $ref,
                    'Product_Code'             => col($row, 'product_code'),
                    'Product_Quantity'         => col($row, 'quantity'),
                ];
            }
        }

        $uniquePayload = array_values($uniqueMap);

        if (empty($uniquePayload)) {
            $reset = DB::table('qualified_transactions')->where('status', 1)->update(['status' => 0]);
            logLine("No valid transaction references found in claimed batch. Reset {$reset} rows.");
            sleep(SLEEP_WHEN_IDLE);
            continue;
        }

        logLine(
            "Claimed {$claimed} source rows; " . count($uniquePayload) .
            " unique transaction references ready to send."
        );

        // Step 4: Send in chunks and mark successful refs as done.
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

        logLine("BATCH COMPLETE — claimed {$claimed} source rows successfully.");

    } catch (\Throwable $e) {
        logLine("ERROR — " . $e->getMessage());

        try {
            $reset = DB::table('qualified_transactions')
                ->where('status', 1)
                ->update(['status' => 0]);

            if ($reset > 0) {
                logLine("ERROR RECOVERY — reset {$reset} claimed rows back to pending.");
            }
        } catch (\Throwable $dbError) {
            logLine("CRITICAL — failed to reset claimed rows: " . $dbError->getMessage());
        }

        sleep(SLEEP_ON_ERROR);
    }
}
