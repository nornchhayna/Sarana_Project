<?php

/**
 * check-bakong-account.php
 *
 * Calls Bakong's official "Check Bakong Account" endpoint to verify
 * whether a given account ID is real and registered on the network.
 * This is the authoritative source of truth — if this says the account
 * doesn't exist, no amount of QR/CRC fixing will make scanning work.
 *
 * Usage:
 *   php check-bakong-account.php "2615451@abaa"
 *
 * Or just edit $accountId below and run: php check-bakong-account.php
 */

// ── Config ───────────────────────────────────────────────────────────────
// Paste your BAKONG_API_TOKEN value here (same one from your .env).
$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMTFjN2QzNGRmYzdhNGFmNiJ9LCJpYXQiOjE3NzgyMTc1ODIsImV4cCI6MTc4NTk5MzU4Mn0.kFY3o4qKc7w9OFUXmApCTCt9RMZRWVk_kvbaS_bJAXg';

// Production Bakong API base URL. (There's also a sandbox/SIT URL,
// https://sit-api-bakong.nbc.gov.kh, but that won't reflect real accounts.)
$baseUrl = 'https://api-bakong.nbc.gov.kh';

// Account ID to check — from CLI arg if given, else fallback below.
$accountId = $argv[1] ?? 'non_chhayna@bkrt';

// ── Request ──────────────────────────────────────────────────────────────
$url = $baseUrl.'/v1/check_bakong_account';

/**
 * Different SDKs/docs disagree on the exact field name casing
 * (accountid / accountID / accountId). Rather than guess once and fail,
 * try each variant against the live API until one is accepted.
 */
$fieldNameVariants = ['accountID', 'accountId', 'accountid'];

function callCheckAccount(string $url, string $token, string $fieldName, string $accountId): array
{
    $payload = json_encode([$fieldName => $accountId]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer '.$token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return compact('payload', 'response', 'httpCode', 'curlError');
}

echo "──────────────────────────────────────────\n";
echo "Checking account: {$accountId}\n";
echo "Endpoint: {$url}\n";
echo "──────────────────────────────────────────\n";

$succeeded = false;

foreach ($fieldNameVariants as $fieldName) {
    echo "\n→ Trying field name \"{$fieldName}\"...\n";

    $result = callCheckAccount($url, $token, $fieldName, $accountId);

    if ($result['curlError']) {

        echo "   cURL ERROR: {$result['curlError']}\n";
        echo "   (If you're outside Cambodia, the Bakong API may block your IP. See README notes about Bakong Relay / RBK tokens as a workaround.)\n";

        continue;
    }

    echo "  Sent: {$result['payload']}\n";
    echo "  HTTP status: {$result['httpCode']}\n";
    echo "  Raw response: {$result['response']}\n";

    $decoded = json_decode($result['response'], true);

    if (! is_array($decoded)) {
        echo "  Could not parse JSON response.\n";

        continue;
    }

    // The live API uses camelCase keys: responseCode, responseMessage, errorCode.
    $responseCode = $decoded['responseCode'] ?? $decoded['responsecode'] ?? null;
    $responseMsg = $decoded['responseMessage'] ?? $decoded['responsemessage'] ?? '(none)';
    $errorCode = $decoded['errorCode'] ?? $decoded['errorcode'] ?? null;

    if ($responseCode === 14 || ($errorCode === 14)) {
        echo "  → Still \"Missing required fields\" with this field name, trying next variant.\n";

        continue;
    }

    echo "\n══════════════════════════════════════════\n";
    if ($responseCode === 0) {
        echo "✅ RESULT (using field \"{$fieldName}\"): This account ID EXISTS on the Bakong network.\n";
        echo "   The account ID itself is fine — if scanning still fails, look elsewhere\n";
        echo "   (amount limits, expired QR, wrong currency, bank-side restrictions).\n";
        $succeeded = true;
    } elseif ($responseCode === 1) {
        echo "❌ RESULT (using field \"{$fieldName}\"): This account ID does NOT exist on the Bakong network.\n";
        echo "   This confirms the account ID is wrong or not in the expected format.\n";
        echo "   Message from Bakong: {$responseMsg}\n";
        $succeeded = true;
    } else {
        echo '⚠️  Unexpected response. errorCode='.json_encode($errorCode).
             ' responseCode='.json_encode($responseCode)." message={$responseMsg}\n";
        if (stripos((string) $responseMsg, 'Unauthorized') !== false) {
            echo "   This looks like a token problem (expired/invalid). Bakong tokens typically need renewal every ~90 days.\n";
        }
        $succeeded = true; // got a real response, just not 0/1 — stop trying other field names
    }
    echo "══════════════════════════════════════════\n";
    break;
}

if (! $succeeded) {
    echo "\n⚠️  None of the field name variants were accepted — all returned 'missing required fields'\n";
    echo "   or failed outright. Double check the token is valid and the endpoint URL is current\n";
    echo "   (Bakong has updated their API base URLs/paths before — worth checking https://api-bakong.nbc.gov.kh/document\n";
    echo "   for the latest spec if this keeps failing).\n";
}
