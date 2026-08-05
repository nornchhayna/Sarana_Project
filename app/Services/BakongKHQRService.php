<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * BakongKHQRService — ការអនុវត្តដោយប្រើ Python។
 *
 * ហេតុអ្វីបានជាមានការផ្លាស់ប្តូរនេះ?
 *
 * កំណែមុននៃ Class នេះបានប្រើ PHP `KHQR\BakongKHQR` SDK
 * (KHQR\Models\IndividualInfo + BakongKHQR::generateIndividual())
 * ដើម្បីបង្កើត KHQR Payload ដោយផ្ទាល់នៅក្នុង PHP។
 *
 * ការធ្វើតេស្តយ៉ាងម៉ត់ចត់ (ខែមិថុនា ឆ្នាំ២០២៦) បានបញ្ជាក់ថា
 * SDK នេះបង្កើត QR String ដែល Wing អាចស្កេន និងទូទាត់បាន
 * ប៉ុន្តែ ABA និង ACLEDA បដិសេធថា "QR Code មិនត្រឹមត្រូវ"
 * ទោះបីប្រើគណនី Bakong ដូចគ្នា, ព័ត៌មាន Merchant ដូចគ្នា,
 * និងចំនួនទឹកប្រាក់ដូចគ្នាក៏ដោយ។
 *
 * ផ្ទុយទៅវិញ QR String ដែលបង្កើតដោយ Python package
 * `bakong_khqr` (ជាការអនុវត្តផ្សេង ដែលហៅតាម
 * scripts/generate_bakong_qr.py) សម្រាប់គណនីដូចគ្នា
 * ត្រូវបាន ABA និង ACLEDA ទទួលស្គាល់ និងអាចប្រើប្រាស់បាន។
 *
 * លទ្ធផលនេះបង្ហាញថា បញ្ហាស្ថិតនៅក្នុងការបង្កើត Payload
 * របស់ PHP SDK។ អាចមានផ្នែកណាមួយនៃការកូដ
 * (ដូចជា Merchant Account Template,
 * Additional Data ឬ Expiration)
 * ដែលមិនអនុលោមតាមស្តង់ដារ KHQR ទាំងស្រុង។
 *
 * Wing មានការត្រួតពិនិត្យមិនតឹងរ៉ឹង ដូច្នេះអាចទទួលយកបាន
 * ខណៈដែល ABA និង ACLEDA មានការត្រួតពិនិត្យតឹងរ៉ឹងជាង
 * ហើយរកឃើញភាពមិនត្រឹមត្រូវនោះ។
 *
 * ជំនួសឱ្យការវិភាគ និងកែប្រែ PHP SDK របស់ភាគីទីបី
 * Class នេះត្រូវបានផ្លាស់ប្តូរឱ្យហៅ Python Generator
 * ដែលបានបញ្ជាក់ថាដំណើរការបានត្រឹមត្រូវ។
 *
 * Method សាធារណៈ (Public Methods) និងទម្រង់ទិន្នន័យ
 * (Return Structure) នៅតែដដែល ដូច្នេះផ្នែកផ្សេងៗនៃប្រព័ន្ធ
 * ដូចជា CheckoutController និង Blade View
 * មិនចាំបាច់កែប្រែអ្វីទាំងអស់។
 *
 * តម្រូវការ (Requirements):
 *   - ត្រូវមាន Python Virtual Environment (.venv)
 *     នៅ base_path('.venv')
 *   - ត្រូវបានដំឡើង package `bakong_khqr`
 *     (ប្រើ pip install bakong-khqr នៅក្នុង venv)
 *   - ត្រូវមានឯកសារ
 *     scripts/generate_bakong_qr.py
 *     នៅ Project Root
 *     (ជា Script ដែលបានប្រើសម្រាប់ការធ្វើតេស្តរួចហើយ)
 *
 * ប្រសិនបើខ្វះ Requirement ណាមួយ
 * generateKHQR() នឹងត្រឡប់
 * success = false
 * ជាមួយសារបង្ហាញកំហុសច្បាស់លាស់
 * ជំនួសឱ្យការប្រើ PHP SDK ចាស់ដែលមានបញ្ហាដោយស្វ័យប្រវត្តិ។
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
     * បង្កើត Dynamic KHQR (ភ្ជាប់ជាមួយចំនួនទឹកប្រាក់ជាក់លាក់)
     * ដោយប្រើ Python Generator។
     *
     * រចនាសម្ព័ន្ធនៃការហៅ (Call Shape) នៅដដែលដូចកំណែមុន៖
     *
     * $params = [
     *     'amount' => float,          // ចំនួនទឹកប្រាក់ដែលត្រូវទូទាត់
     *     'ref' => string,            // លេខយោងប្រតិបត្តិការ (Reference)
     *     'ttl_seconds' => int,       // រយៈពេលសុពលភាពរបស់ QR (ជាជម្រើស)
     *     'store_label' => string,    // ឈ្មោះហាង ឬអាជីវកម្ម (ជាជម្រើស)
     *     'terminal_label' => string, // លេខសម្គាល់ Terminal/POS (ជាជម្រើស)
     * ];
     *
     * Method នេះត្រឡប់ទិន្នន័យ (Return Structure)
     * ដូចគ្នានឹងកំណែមុន ដើម្បីធានាថាផ្នែកផ្សេងៗនៃប្រព័ន្ធ
     * អាចដំណើរការបានដោយមិនចាំបាច់កែប្រែ។
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
     * Method នេះត្រូវបានរក្សាទុក ដើម្បីរក្សាភាពឆបគ្នា (Interface Compatibility)
     * ជាមួយការអនុវត្តកាលពីមុន និងការធ្វើតេស្ត A/B។
     *
     * បច្ចុប្បន្ន Method នេះគ្រាន់តែហៅទៅកាន់ generateKHQR() ប៉ុណ្ណោះ។
     *
     * មូលហេតុគឺ ការធ្វើតេស្ត "Merchant QR និង Individual QR"
     * ត្រូវបានបញ្ជាក់រួចហើយថា មិនមែនជាមូលហេតុនៃបញ្ហានោះទេ។
     *
     * ទាំង Merchant QR និង Individual QR ដែលបង្កើតដោយ PHP SDK
     * សុទ្ធតែបរាជ័យ (Invalid QR Code)
     * នៅពេលស្កេនដោយ ABA និង ACLEDA។
     *
     * ដូច្នេះ មិនមានតម្រូវការក្នុងការរក្សាទុក Logic
     * សម្រាប់ Merchant QR ដាច់ដោយឡែកទៀតឡើយ។
     *
     * ប្រសិនបើនៅពេលអនាគត ត្រូវការបង្កើត KHQR
     * ដែលមានទម្រង់ MerchantInfo ពិតប្រាកដ
     * គួរតែប្រើ Python Package `bakong_khqr`
     * (ដែលទំនងជាគាំទ្រមុខងារនេះ — សូមពិនិត្យឯកសារ Documentation
     * របស់ `bakong_khqr`)
     * ជំនួសឱ្យការនាំ PHP SDK ដែលមានបញ្ហាមកប្រើឡើងវិញ។
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
                // ករណីនេះគឺជាករណីដែលទំនងជាលាក់បាំងបញ្ហាពិតប្រាកដដោយមិនបង្ហាញឱ្យឃើញ។
                // ប្រសិនបើទទួលបានស្ថានភាព 401 ឬ 403 វាមានន័យថា
                // Access Token មិនត្រឹមត្រូវ (Invalid),
                // ផុតសុពលភាព (Expired),
                // ឬមិនមានសិទ្ធិ (Unauthorized) ក្នុងការចូលប្រើគណនីនេះ។
                //
                // វាមិនមានន័យថា "អតិថិជនមិនទាន់បានទូទាត់ប្រាក់" នោះទេ។
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
