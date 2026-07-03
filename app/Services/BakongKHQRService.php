<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * BakongKHQRService — Python-backed implementation.
 *
 * WHY THIS EXISTS / WHAT CHANGED:
 *
 * The previous version of this class used the PHP `KHQR\BakongKHQR` SDK
 * (KHQR\Models\IndividualInfo + BakongKHQR::generateIndividual()) to build
 * the KHQR payload directly in PHP.
 *
 * Controlled testing (June 2026) proved that SDK produces a QR string that
 * Wing accepts but ABA and ACLEDA both reject as "invalid QR code" — for
 * the exact same Bakong account, same merchant fields, same amount. A
 * string built instead via the `bakong_khqr` Python package (different
 * author/implementation, called through scripts/generate_bakong_qr.py)
 * for the identical account WAS accepted by both ABA and ACLEDA.
 *
 * That isolates the bug to the PHP SDK's payload construction — something
 * in how it encodes the merchant account template, additional data, or
 * expiration is spec-deviant in a way only stricter bank-side validators
 * (ABA/ACLEDA) catch, while looser ones (Wing) don't. Rather than reverse-
 * engineer and patch a third-party PHP package internally, this class now
 * shells out to the proven-working Python generator instead, keeping the
 * exact same public method names/return shape so nothing else in the app
 * (CheckoutController, the Blade view) needs to change.
 *
 * REQUIREMENTS:
 *   - A Python venv at base_path('.venv') with the `bakong_khqr` package
 *     installed (pip install bakong-khqr inside that venv).
 *   - scripts/generate_bakong_qr.py present at the project root (the same
 *     script already used for manual testing — see chat history).
 *
 * If either is missing, generateKHQR() returns a clear success=false error
 * rather than silently falling back to the broken PHP SDK path.
 */
class BakongKHQRService
{
    protected string $bakongId;

    protected string $merchantName;

    protected string $merchantCity;

    protected int $currency;

    protected string $apiToken;

    protected string $apiBase = 'https://api-bakong.nbc.gov.kh';

    protected array $merchantConfig = [];

    public const DEFAULT_TTL_SECONDS = 30 * 60; // 30 minutes

    public function __construct()
    {
        $this->bakongId = config('bakong.account_id', '');
        $this->merchantName = config('bakong.merchant_name', 'My Shop');
        $this->merchantCity = config('bakong.merchant_city', 'PHNOMPENH');
        $this->currency = (int) config('bakong.currency', 840);
        $this->apiToken = config('bakong.api_token', '');
    }

    public function setMerchantConfig($bakong): static
    {
        $currency = $bakong->currency ?? config('bakong.currency', 840);
        if (is_string($currency)) {
            $currency = strtoupper($currency) === 'USD' ? 840 : 116;
        }

        $this->merchantConfig = [
            'account_id' => $bakong->bakongAccountID,
            'merchant_name' => $bakong->merchantName ?? config('bakong.merchant_name'),
            'merchant_city' => $bakong->merchantCity ?? config('bakong.merchant_city'),
            'currency' => (int) $currency,
        ];

        return $this;
    }

    /**
     * Generate a dynamic (amount-bound) KHQR via the Python generator.
     *
     * Same call shape as before: $params = ['amount' => float, 'ref' => string,
     * 'ttl_seconds' => int (optional), 'store_label' => string (optional),
     * 'terminal_label' => string (optional)].
     */
    public function generateKHQR(array $params): array
    {
        $config = array_merge([
            'account_id' => config('bakong.account_id'),
            'merchant_name' => config('bakong.merchant_name'),
            'merchant_city' => config('bakong.merchant_city'),
            'currency' => $this->currency,
        ], $this->merchantConfig);

        $this->merchantConfig = [];

        if (empty($config['account_id'])) {
            return [
                'success' => false,
                'error' => 'Bakong account ID is not configured.',
            ];
        }

        $python = base_path('.venv/bin/python');
        $script = base_path('scripts/generate_bakong_qr.py');

        if (! is_file($python) || ! is_file($script)) {
            Log::error('BakongKHQRService: Python generator prerequisites missing.', [
                'python' => $python,
                'script' => $script,
            ]);

            return [
                'success' => false,
                'error' => 'KHQR generator is not set up on this server (missing .venv or script).',
            ];
        }

        $amount = max(0, (float) ($params['amount'] ?? 0));
        $currencyCode = (int) ($config['currency'] ?? $this->currency);
        $currency = $currencyCode === 116 ? 'KHR' : 'USD';
        $ref = (string) ($params['ref'] ?? '');
        $ttlSeconds = (int) ($params['ttl_seconds'] ?? self::DEFAULT_TTL_SECONDS);

        $process = new Process([
            $python,
            $script,
            '--bank-account',
            (string) $config['account_id'],
            '--merchant-name',
            (string) $config['merchant_name'],
            '--merchant-city',
            (string) $config['merchant_city'],
            '--amount',
            number_format($amount, 2, '.', ''),
            '--currency',
            $currency,
            '--store-label',
            (string) ($params['store_label'] ?? 'Online Store'),
            '--bill-number',
            $ref,
            '--terminal-label',
            (string) ($params['terminal_label'] ?? 'Online'),
            '--expiration',
            (string) $ttlSeconds,
        ]);

        $process->setTimeout(15);
        $process->run();

        if (! $process->isSuccessful()) {
            Log::error('BakongKHQRService: Python generator process failed.', [
                'error' => $process->getErrorOutput(),
                'output' => $process->getOutput(),
            ]);

            return [
                'success' => false,
                'error' => 'KHQR generation failed.',
            ];
        }

        $decoded = json_decode(trim($process->getOutput()), true);

        if (! is_array($decoded) || empty($decoded['qr']) || empty($decoded['md5'])) {
            Log::error('BakongKHQRService: Python generator returned invalid JSON.', [
                'output' => $process->getOutput(),
            ]);

            return [
                'success' => false,
                'error' => 'KHQR generation returned an unexpected response.',
            ];
        }

        return [
            'success' => true,
            'qr_string' => (string) $decoded['qr'],
            'md5' => (string) $decoded['md5'],
            'error' => null,
        ];
    }

    /**
     * Kept for interface compatibility with the earlier A/B test. Now simply
     * delegates to generateKHQR() — the "merchant vs individual QR type"
     * experiment was already ruled out as the cause (both failed on
     * ABA/ACLEDA when built via the PHP SDK), so there's no separate
     * merchant code path to maintain here. If true MerchantInfo-shaped KHQR
     * generation is needed later, it should go through the Python package
     * (which appears to support it — check `bakong_khqr`'s docs) rather than
     * reintroducing the PHP SDK.
     */
    public function generateKHQRMerchant(array $params): array
    {
        return $this->generateKHQR($params);
    }

    public function checkByMd5(string $md5): bool
    {
        return $this->checkByMd5Verbose($md5)['paid'];
    }

    /**
     * Same check as checkByMd5() but returns full diagnostic detail instead
     * of collapsing everything to a bool. Use this when debugging "payment
     * completed but order never updated" — it distinguishes between:
     *   - genuinely not paid yet (responseCode != 0, no error)
     *   - the API call itself failing (bad/expired token, wrong scope, 4xx/5xx)
     *   - a network/exception failure
     * checkByMd5()/isPaid() collapse all three to `false`, which is exactly
     * why this distinction can otherwise hide silently in logs.
     */
    public function checkByMd5Verbose(string $md5): array
    {
        if (empty($this->apiToken)) {
            Log::warning('KHQR checkByMd5: no API token configured', ['md5' => $md5]);

            return ['paid' => false, 'http_status' => null, 'body' => null, 'error' => 'No BAKONG_API_TOKEN configured.'];
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->timeout(8)
                ->post("{$this->apiBase}/v1/check_transaction_by_md5", [
                    'md5' => $md5,
                ]);

            $body = $response->json();

            if ($response->failed()) {
                // This is the case most likely to be silently hiding the real
                // problem: a 401/403 here means the token is invalid, expired,
                // or not authorized for this account — NOT "not paid yet."
                Log::warning('KHQR check_transaction_by_md5 HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'md5' => $md5,
                ]);

                return [
                    'paid' => false,
                    'http_status' => $response->status(),
                    'body' => $body,
                    'error' => 'HTTP '.$response->status().' from Bakong API — check token validity/scope.',
                ];
            }

            $paid = isset($body['responseCode'])
                && $body['responseCode'] === 0
                && ! empty($body['data']);

            Log::info('KHQR check_transaction_by_md5 response', [
                'md5' => $md5,
                'http_status' => $response->status(),
                'responseCode' => $body['responseCode'] ?? null,
                'errorCode' => $body['errorCode'] ?? null,
                'paid' => $paid,
            ]);

            return [
                'paid' => $paid,
                'http_status' => $response->status(),
                'body' => $body,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('KHQR checkByMd5 exception', ['error' => $e->getMessage(), 'md5' => $md5]);

            return ['paid' => false, 'http_status' => null, 'body' => null, 'error' => $e->getMessage()];
        }
    }

    public function isPaid(string $md5): bool
    {
        return $this->checkByMd5($md5);
    }
}
