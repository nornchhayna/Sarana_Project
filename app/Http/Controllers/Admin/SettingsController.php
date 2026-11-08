<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchantbakongkhqr;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Redirect the base settings route to the general tab.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.settings.general');
    }

    public function general(): View
    {
        return view('admin.settings.general', [
            'settings' => Setting::group('general'),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'max:2048'],
            'timezone' => ['required', 'string', 'timezone'],
            'site_description' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::set('site_name', $validated['site_name'], 'general');
        Setting::set('timezone', $validated['timezone'], 'general');
        if (isset($validated['site_description'])) {
            Setting::set('site_description', $validated['site_description'], 'general');
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $path, 'general', 'file');
        }

        return back()->with('status', 'General settings updated successfully.');
    }

    public function store(): View
    {
        return view('admin.settings.store', [
            'settings' => Setting::group('store'),
        ]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_email' => ['required', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:30'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'store_telegram' => ['nullable', 'string', 'max:255'],
            'store_facebook' => ['nullable', 'string', 'max:255'],
            'store_tiktok' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', 'store');
        }

        // Keep legacy contact keys synced
        if (! empty($validated['store_email'])) {
            Setting::set('contact_email', $validated['store_email'], 'contact');
        }
        if (! empty($validated['store_phone'])) {
            Setting::set('contact_phone', $validated['store_phone'], 'contact');
        }

        return back()->with('status', 'Store contact information updated successfully.');
    }

    public function payment(): View
    {
        $settings = Setting::group('payment');
        $merchant = Merchantbakongkhqr::first();

        // Default from Merchantbakongkhqr or config if not yet customized in Setting
        if (empty($settings['bakong_account_id'])) {
            $settings['bakong_account_id'] = $merchant?->bakongAccountID ?? config('bakong.account_id', 'non_chhayna@bkrt');
        }
        if (empty($settings['bakong_merchant_name'])) {
            $settings['bakong_merchant_name'] = $merchant?->merchantName ?? config('bakong.merchant_name', 'Ashahi Store');
        }
        if (empty($settings['bakong_merchant_city'])) {
            $settings['bakong_merchant_city'] = $merchant?->merchantCity ?? config('bakong.merchant_city', 'Phnom Penh');
        }
        if (empty($settings['currency'])) {
            $settings['currency'] = $merchant?->currency ?? 'USD';
        }
        if (! isset($settings['khqr_enabled'])) {
            $settings['khqr_enabled'] = true;
        }
        if (! isset($settings['cod_enabled'])) {
            $settings['cod_enabled'] = true;
        }
        if (empty($settings['bakong_api_token'])) {
            $settings['bakong_api_token'] = config('bakong.api_token') ?: env('BAKONG_API_TOKEN', '');
        }

        return view('admin.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'in:USD,KHR'],
            'khqr_enabled' => ['sometimes', 'boolean'],
            'bakong_account_id' => ['required', 'string', 'max:100'],
            'bakong_merchant_name' => ['required', 'string', 'max:100'],
            'bakong_merchant_city' => ['required', 'string', 'max:100'],
            'bakong_api_token' => ['nullable', 'string'],
            'cod_enabled' => ['sometimes', 'boolean'],
            'cod_instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        $khqrEnabled = $request->boolean('khqr_enabled');
        $codEnabled = $request->boolean('cod_enabled');

        Setting::set('currency', $validated['currency'], 'payment');
        Setting::set('khqr_enabled', $khqrEnabled, 'payment', 'boolean');
        Setting::set('bakong_account_id', $validated['bakong_account_id'], 'payment');
        Setting::set('bakong_merchant_name', $validated['bakong_merchant_name'], 'payment');
        Setting::set('bakong_merchant_city', $validated['bakong_merchant_city'], 'payment');
        Setting::set('cod_enabled', $codEnabled, 'payment', 'boolean');
        Setting::set('cod_instructions', $validated['cod_instructions'] ?? '', 'payment');

        if (! empty($validated['bakong_api_token'])) {
            Setting::set('bakong_api_token', $validated['bakong_api_token'], 'payment');

            // Update .env BAKONG_API_TOKEN safely
            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $envContent = File::get($envPath);
                if (preg_match('/^BAKONG_API_TOKEN=.*/m', $envContent)) {
                    $envContent = preg_replace('/^BAKONG_API_TOKEN=.*/m', 'BAKONG_API_TOKEN=' . $validated['bakong_api_token'], $envContent);
                } else {
                    $envContent .= "\nBAKONG_API_TOKEN=" . $validated['bakong_api_token'];
                }
                File::put($envPath, $envContent);
            }

            config(['bakong.api_token' => $validated['bakong_api_token']]);
        }

        // Sync to legacy Merchantbakongkhqr table
        Merchantbakongkhqr::updateOrCreate(
            ['id' => 1],
            [
                'bakongAccountID' => $validated['bakong_account_id'],
                'merchantName' => $validated['bakong_merchant_name'],
                'merchantCity' => $validated['bakong_merchant_city'],
                'currency' => $validated['currency'],
                'countryCode' => 'KH',
            ]
        );

        return back()->with('status', 'Payment settings and Bakong KHQR credentials updated successfully.');
    }

    public function shipping(): View
    {
        $settings = Setting::group('shipping');
        if (! isset($settings['shipping_enabled'])) {
            $settings['shipping_enabled'] = true;
        }

        return view('admin.settings.shipping', compact('settings'));
    }

    public function updateShipping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flat_shipping_rate' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0'],
            'shipping_enabled' => ['sometimes', 'boolean'],
            'shipping_estimated_days' => ['nullable', 'string', 'max:150'],
        ]);

        Setting::set('flat_shipping_rate', (float) $validated['flat_shipping_rate'], 'shipping', 'float');
        Setting::set('free_shipping_threshold', (float) $validated['free_shipping_threshold'], 'shipping', 'float');
        Setting::set('shipping_enabled', $request->boolean('shipping_enabled'), 'shipping', 'boolean');
        Setting::set('shipping_estimated_days', $validated['shipping_estimated_days'] ?? '', 'shipping');

        return back()->with('status', 'Shipping settings updated successfully.');
    }
}
