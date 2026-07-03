<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeedSmsService
{
    // SpeedSMS API for Cambodia (hosted in Vietnam but works regionally)
    // Docs: https://speedsms.vn/api-doc/
    protected string $baseUrl = 'https://api.speedsms.vn/index.php';

    protected string $token;

    protected string $sender;

    protected int $type;

    public function __construct()
    {
        $this->token = config('speedsms.token', '');
        $this->sender = config('speedsms.sender', '');
        $this->type = (int) config('speedsms.type', 2);
    }

    /**
     * Send SMS. Returns ['success' => bool, 'error' => string|null]
     */
    public function send(string $phone, string $message): bool
    {
        $phone = $this->normalizePhone($phone);

        if (empty($this->token)) {
            Log::error('SpeedSMS: SPEEDSMS_TOKEN is empty in .env');

            return false;
        }

        $payload = [
            'to' => [$phone],
            'content' => $message,
            'sms_type' => $this->type,
            'sender' => $this->sender,
        ];

        Log::info('SpeedSMS sending', [
            'phone' => $phone,
            'type' => $this->type,
            'sender' => $this->sender,
            'token' => substr($this->token, 0, 6).'***', // partial for safety
        ]);

        try {
            $response = Http::withBasicAuth($this->token, ':')
                ->timeout(15)
                ->post($this->baseUrl.'/sms/send', $payload);

            $status = $response->status();
            $body = $response->body();
            $json = $response->json();

            Log::info('SpeedSMS response', [
                'http_status' => $status,
                'body' => $body,
                'json' => $json,
            ]);

            // SpeedSMS returns: {"status":"success","code":"00",...} on success
            if ($json && isset($json['status']) && $json['status'] === 'success') {
                return true;
            }

            // Log the exact error code from SpeedSMS
            $errorCode = $json['code'] ?? 'unknown';
            $errorMsg = $json['message'] ?? $body;

            Log::warning('SpeedSMS failed', [
                'phone' => $phone,
                'error_code' => $errorCode,
                'error_msg' => $errorMsg,
            ]);

            return false;
        } catch (ConnectionException $e) {
            Log::error('SpeedSMS connection error', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('SpeedSMS unexpected error', [
                'phone' => $phone,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Normalize any Cambodian phone format to E.164 (+855xxxxxxxxx)
     *
     * Accepts:
     *   0xx xxx xxxx   → +855xx xxx xxxx
     *   855xxxxxxxxx   → +855xxxxxxxxx
     *   +855xxxxxxxxx  → +855xxxxxxxxx  (already correct)
     */
    public function normalizePhone(string $phone): string
    {
        // Strip all non-digit characters except leading +
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        if (str_starts_with($cleaned, '855')) {
            return '+'.$cleaned;
        }

        if (str_starts_with($cleaned, '0')) {
            return '+855'.substr($cleaned, 1);
        }

        // Already just the local digits without leading 0
        return '+855'.$cleaned;
    }

    /**
     * Validate Cambodian phone number.
     * Accepts 0xx format or +855xx / 855xx.
     */
    public function isValidCambodianPhone(string $phone): bool
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        // Strip country code
        if (str_starts_with($cleaned, '855')) {
            $cleaned = '0'.substr($cleaned, 3);
        }

        // Must start with 0 and be 9-10 digits total
        if (! preg_match('/^0\d{8,9}$/', $cleaned)) {
            return false;
        }

        // Valid Cambodian network prefixes
        $validPrefixes = [
            '010',
            '011',
            '012',
            '013',
            '015',
            '016',
            '017',
            '018',
            '038',
            '061',
            '069',
            '070',
            '071',
            '076',
            '077',
            '078',
            '079',
            '080',
            '081',
            '083',
            '085',
            '086',
            '087',
            '088',
            '089',
            '092',
            '093',
            '095',
            '096',
            '097',
            '098',
            '099',
        ];

        $prefix = substr($cleaned, 0, 3);

        return in_array($prefix, $validPrefixes, true);
    }
}
