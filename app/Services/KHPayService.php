<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str;
// use Symfony\Component\Process\Process;

// class KHPayService
// {
//     /**
//      * Your Bakong ID (e.g. non_chhayna@bkrt or chhayna@aclb)
//      */
//     protected string $bakongId;

//     protected string $merchantName;

//     protected string $merchantCity;

//     protected string $currency;

//     protected string $md5BaseUrl;

//     public function __construct()
//     {
//         $this->bakongId = config('services.khpay.bakong_id', 'non_chhayna@bkrt');
//         $this->merchantName = config('services.khpay.merchant_name', 'Ashahi Store');
//         $this->merchantCity = config('services.khpay.merchant_city', 'PHNOMPENH');
//         $this->currency = config('services.khpay.currency', '840'); // 840 = USD, 116 = KHR
//         $this->md5BaseUrl = config('services.khpay.md5_url', 'https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5');
//     }

//     /**
//      * Basic configuration guard used by checkout UI.
//      */
//     public function isConfigured(): bool
//     {
//         return ! empty($this->bakongId) && ! empty($this->md5BaseUrl);
//     }

//     // -------------------------------------------------------------------------
//     // KHQR GENERATION
//     // -------------------------------------------------------------------------

//     /**
//      * Generate a KHQR payload for checkout.
//      *
//      * Supports:
//      * - generateKHQR(float $amount, string $ref)
//      * - generateKHQR(array $payload) where payload includes amount/ref keys
//      */
//     public function generateKHQR(float|array $amountOrPayload, ?string $ref = null): array
//     {
//         // Support both old signature (float $amount, string $ref)
//         // and new caller style (array payload with amount/ref keys).
//         if (is_array($amountOrPayload)) {
//             $amount = (float) ($amountOrPayload['amount'] ?? 0);
//             $ref = (string) ($amountOrPayload['ref'] ?? ('ORD-'.strtoupper(Str::random(10))));
//         } else {
//             $amount = $amountOrPayload;
//             $ref = (string) ($ref ?? ('ORD-'.strtoupper(Str::random(10))));
//         }

//         if ($amount <= 0) {
//             return [
//                 'success' => false,
//                 'error' => 'Invalid payment amount',
//             ];
//         }

//         $amountFormatted = number_format($amount, 2, '.', '');

//         // Prefer official-format generation via local python KHQR library.
//         [$qrString, $md5Hash] = $this->generateViaPython($amountFormatted, $ref);

//         // Fallback to local builder if python prerequisites are unavailable.
//         if (! $qrString) {
//             $qrString = $this->buildKHQRString($amountFormatted, $ref);
//             $qrString .= '6304';
//             $qrString .= strtoupper($this->crc16($qrString));
//             $md5Hash = md5($qrString);
//         }

//         if (! $qrString || ! $md5Hash) {
//             return [
//                 'success' => false,
//                 'error' => 'Unable to generate KHQR payload',
//             ];
//         }

//         Log::info('KHQR Generated', [
//             'ref' => $ref,
//             'amount' => $amountFormatted,
//             'md5' => $md5Hash,
//         ]);

//         return [
//             'success' => true,
//             'qr_string' => $qrString,
//             'md5' => $md5Hash,
//             'amount' => $amountFormatted,
//             'ref' => $ref,
//             'data' => [
//                 'qr_code' => $qrString,
//                 'md5' => $md5Hash,
//                 'transaction_id' => $ref,
//             ],
//         ];
//     }

//     protected function generateViaPython(string $amount, string $ref): array
//     {
//         $python = base_path('.venv/bin/python');
//         $script = base_path('scripts/generate_bakong_qr.py');

//         if (! is_file($python) || ! is_file($script)) {
//             return ['', ''];
//         }

//         $currency = $this->currency === '116' ? 'KHR' : 'USD';

//         $process = new Process([
//             $python,
//             $script,
//             '--bank-account',
//             (string) $this->bakongId,
//             '--merchant-name',
//             (string) $this->merchantName,
//             '--merchant-city',
//             (string) $this->merchantCity,
//             '--amount',
//             $amount,
//             '--currency',
//             $currency,
//             '--store-label',
//             'KKS Store',
//             '--bill-number',
//             $ref,
//             '--terminal-label',
//             'Online',
//             '--expiration',
//             '1',
//         ]);

//         $process->run();

//         if (! $process->isSuccessful()) {
//             Log::warning('KHPay python KHQR generator failed', [
//                 'error' => $process->getErrorOutput(),
//             ]);

//             return ['', ''];
//         }

//         $payload = json_decode(trim($process->getOutput()), true);

//         if (! is_array($payload) || empty($payload['qr']) || empty($payload['md5'])) {
//             return ['', ''];
//         }

//         return [(string) $payload['qr'], (string) $payload['md5']];
//     }

//     /**
//      * Build the raw KHQR/EMVCo string (without CRC).
//      */
//     protected function buildKHQRString(string $amount, string $ref): string
//     {
//         // TLV helper
//         $tlv = fn (string $tag, string $value): string => $tag.str_pad(strlen($value), 2, '0', STR_PAD_LEFT).$value;

//         // --- Root fields ---
//         $payload = $tlv('00', '01');           // Payload Format Indicator
//         $payload .= $tlv('01', '12');           // Point of Initiation: 11=static, 12=dynamic

//         // --- Merchant Account (Tag 29 = Bakong) ---
//         $bakonTag = $tlv('00', 'bakong.khqr');
//         $bakonTag .= $tlv('01', $this->bakongId);
//         $bakonTag .= $tlv('08', $ref);        // Bill number / reference
//         $payload .= $tlv('29', $bakonTag);

//         $payload .= $tlv('52', '5999');         // MCC: 5999 = misc retail
//         $payload .= $tlv('53', $this->currency);
//         $payload .= $tlv('54', $amount);        // Transaction amount
//         $payload .= $tlv('58', 'KH');           // Country code
//         $payload .= $tlv('59', $this->normalizeName($this->merchantName));
//         $payload .= $tlv('60', $this->merchantCity);

//         // Additional data field — bill reference (Tag 62 → sub-tag 05)
//         $additionalData = $tlv('05', $ref);
//         $payload .= $tlv('62', $additionalData);

//         return $payload;
//     }

//     /**
//      * Normalize merchant name to uppercase ASCII (KHQR spec requires this).
//      */
//     protected function normalizeName(string $name): string
//     {
//         return strtoupper(substr(preg_replace('/[^A-Za-z0-9 ]/', '', $name), 0, 25));
//     }

//     // -------------------------------------------------------------------------
//     // PAYMENT STATUS CHECK (MD5 method — no API key required)
//     // -------------------------------------------------------------------------

//     /**
//      * Check payment status using the Bakong MD5 endpoint.
//      *
//      * @param  string  $md5  The MD5 hash of the QR string
//      * @return array ['paid' => bool, 'status' => 'PAID|PENDING|FAILED', 'data' => [...]]
//      */
//     public function checkPaymentStatus(string $md5): array
//     {
//         try {
//             $data = $this->requestMd5Status($md5);
//             [$status, $paid] = $this->mapPaymentStatus($data);
//             $message = 'Waiting for payment confirmation.';

//             // Fallback to the shared Bakong checker used by manual KHQR flow.
//             if (! $paid) {
//                 $bakongResult = app(BakongKhqrService::class)->checkTransactionByMd5($md5, $this->bakongId);
//                 if (($bakongResult['success'] ?? false) && ($bakongResult['paid'] ?? false)) {
//                     $paid = true;
//                     $status = 'PAID';
//                     $message = (string) ($bakongResult['message'] ?? 'Payment confirmed by Bakong.');
//                     $data = is_array($bakongResult['response'] ?? null)
//                         ? $bakongResult['response']
//                         : (is_array($bakongResult['raw'] ?? null) ? $bakongResult['raw'] : $data);
//                 } elseif (($bakongResult['success'] ?? false) && ! empty($bakongResult['status'])) {
//                     $status = strtoupper((string) $bakongResult['status']);
//                     $message = (string) ($bakongResult['message'] ?? $message);
//                 } elseif (! ($bakongResult['success'] ?? false)) {
//                     $status = 'UNAVAILABLE';
//                     $message = (string) ($bakongResult['message'] ?? 'Automatic payment verification is not configured.');
//                 }
//             }

//             return $this->statusResult(
//                 $paid,
//                 $status,
//                 is_array($data['data'] ?? null) ? $data['data'] : $data,
//                 $message
//             );
//         } catch (\Exception $e) {
//             Log::error('KHQR MD5 check exception', ['md5' => $md5, 'error' => $e->getMessage()]);

//             return $this->statusResult(false, 'ERROR', [], 'Could not verify payment right now.');
//         }
//     }

//     protected function requestMd5Status(string $md5): array
//     {
//         $payloadWithId = ['md5' => $md5, 'bakongid' => $this->bakongId];
//         $payloadNoId = ['md5' => $md5];

//         $requests = [
//             fn () => Http::timeout(10)->acceptJson()->post($this->md5BaseUrl, $payloadWithId),
//             fn () => Http::timeout(10)->acceptJson()->post($this->md5BaseUrl, $payloadNoId),
//             fn () => Http::timeout(10)->acceptJson()->get($this->md5BaseUrl, $payloadWithId),
//             fn () => Http::timeout(10)->acceptJson()->get($this->md5BaseUrl, $payloadNoId),
//         ];

//         $lastPayload = [];

//         foreach ($requests as $attempt => $request) {
//             try {
//                 $response = $request();
//                 if ($response->failed()) {
//                     Log::info('KHQR MD5 check response failed', [
//                         'attempt' => $attempt + 1,
//                         'status' => $response->status(),
//                     ]);

//                     continue;
//                 }

//                 $decoded = $response->json();
//                 if (! is_array($decoded)) {
//                     Log::info('KHQR MD5 check response invalid JSON', [
//                         'attempt' => $attempt + 1,
//                         'body' => $response->body(),
//                     ]);

//                     continue;
//                 }

//                 $lastPayload = $decoded;
//                 [$status, $paid] = $this->mapPaymentStatus($decoded);

//                 Log::info('KHQR MD5 check response parsed', [
//                     'attempt' => $attempt + 1,
//                     'status' => $status,
//                     'paid' => $paid,
//                     'responseCode' => $decoded['responseCode'] ?? null,
//                     'errorCode' => $decoded['errorCode'] ?? null,
//                     'responseMessage' => $decoded['responseMessage'] ?? null,
//                 ]);

//                 // Only short-circuit on confirmed payment.
//                 if ($paid) {
//                     return $decoded;
//                 }
//             } catch (\Throwable $e) {
//                 Log::warning('KHQR MD5 check attempt failed', [
//                     'attempt' => $attempt + 1,
//                     'error' => $e->getMessage(),
//                 ]);
//             }
//         }

//         return $lastPayload;
//     }

//     protected function mapPaymentStatus(array $payload): array
//     {
//         $responseCode = isset($payload['responseCode']) ? (int) $payload['responseCode'] : null;
//         $errorCode = isset($payload['errorCode']) ? (int) $payload['errorCode'] : null;
//         $rawStatus = strtoupper(trim((string) ($payload['status'] ?? data_get($payload, 'data.status', ''))));
//         $hasHash = ! empty(data_get($payload, 'data.hash'))
//             || ! empty(data_get($payload, 'hash'))
//             || ! empty(data_get($payload, 'data.0.hash'));
//         $responseMessage = strtoupper(trim((string) ($payload['responseMessage'] ?? '')));

//         if (
//             $responseCode === 0
//             || $hasHash
//             || str_contains($responseMessage, 'SUCCESS')
//             || in_array($rawStatus, ['PAID', 'SUCCESS', 'COMPLETED'], true)
//         ) {
//             return ['PAID', true];
//         }

//         if ($errorCode === 1 || in_array($rawStatus, ['UNPAID', 'PENDING'], true)) {
//             return ['PENDING', false];
//         }

//         if ($errorCode === 3 || in_array($rawStatus, ['FAILED', 'CANCELLED', 'EXPIRED'], true)) {
//             return ['FAILED', false];
//         }

//         return ['PENDING', false];
//     }

//     protected function statusResult(bool $paid, string $status, array $data, string $message = ''): array
//     {
//         return compact('paid', 'status', 'data', 'message');
//     }

//     // -------------------------------------------------------------------------
//     // WEBHOOK SIGNATURE VERIFICATION (for when you upgrade to merchant API)
//     // -------------------------------------------------------------------------

//     /**
//      * Verify an incoming webhook signature from KHPay merchant API.
//      * Uses HMAC-SHA256 signed with your webhook secret.
//      *
//      * @param  string  $payload  Raw request body
//      * @param  string  $signature  Header value (X-KHPay-Signature)
//      */
//     public function verifyWebhookSignature(string $payload, string $signature): bool
//     {
//         $secret = config('services.khpay.webhook_secret', '');

//         if (empty($secret)) {
//             Log::warning('KHQR webhook secret not configured');

//             return false;
//         }

//         $expected = hash_hmac('sha256', $payload, $secret);

//         return hash_equals($expected, $signature);
//     }

//     // -------------------------------------------------------------------------
//     // CRC-16 / CCITT (KHQR spec)
//     // -------------------------------------------------------------------------

//     protected function crc16(string $data): string
//     {
//         $crc = 0xFFFF;

//         for ($i = 0; $i < strlen($data); $i++) {
//             $crc ^= ord($data[$i]) << 8;
//             for ($j = 0; $j < 8; $j++) {
//                 if ($crc & 0x8000) {
//                     $crc = ($crc << 1) ^ 0x1021;
//                 } else {
//                     $crc <<= 1;
//                 }
//                 $crc &= 0xFFFF;
//             }
//         }

//         return sprintf('%04X', $crc);
//     }
// }
