<?php

/**
 * renew-bakong-token.php
 *
 * Your current BAKONG_API_TOKEN is being rejected with HTTP 401
 * "Unauthorized, not yet requested for token or code invalid" — this means
 * every payment-confirmation poll has been silently failing, regardless of
 * whether the customer actually paid. This script gets you a fresh token.
 *
 * Bakong tokens are tied to the email you registered with Bakong's Open API
 * program. Run this from inside your Laravel project so the KHQR SDK
 * autoloads correctly:
 *
 *   php renew-bakong-token.php your-registered-email@example.com
 *
 * Copy the printed token into BAKONG_API_TOKEN in your .env, then run:
 *   php artisan config:clear
 */

require __DIR__.'/vendor/autoload.php'; // adjust path if running from elsewhere

use KHQR\BakongKHQR;

$email = $argv[1] ?? null;

if (! $email) {
    echo "Usage: php renew-bakong-token.php your-registered-email@example.com\n";
    exit(1);
}

echo "Requesting a fresh Bakong API token for: {$email}\n";
echo "──────────────────────────────────────────\n";

try {
    $result = BakongKHQR::renewToken($email);

    echo "Raw result:\n";
    print_r($result);
    echo "──────────────────────────────────────────\n";

    if (($result['responseCode'] ?? null) === 0 && ! empty($result['data']['token'])) {
        echo "✅ New token issued:\n\n";
        echo $result['data']['token']."\n\n";
        echo "Next steps:\n";
        echo "1. Copy the token above into .env as BAKONG_API_TOKEN=<token>\n";
        echo "2. Run: php artisan config:clear\n";
        echo "3. Re-test a payment — the polling should now confirm correctly.\n\n";
        echo "Note: Bakong tokens typically expire periodically (historically ~90 days).\n";
        echo "Consider scheduling this renewal (e.g. a monthly Laravel scheduled command)\n";
        echo "so this doesn't silently break again.\n";
    } elseif (($result['responseCode'] ?? null) === 1 && ($result['errorCode'] ?? null) === 10) {
        echo "❌ This email is not registered with Bakong's Open API program yet.\n";
        echo "   You need to register at https://bakong.nbc.gov.kh (Open API / KHQR developer\n";
        echo "   section) using this email before a token can be issued.\n";
    } else {
        echo "⚠️  Unexpected response — see raw result above.\n";
    }
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}
