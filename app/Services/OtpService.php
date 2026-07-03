<?php

namespace App\Services;

use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    protected SpeedSmsService $sms;

    protected int $expireMinutes = 10;

    protected ?string $lastError = null;

    protected int $otpLength = 6;

    protected int $otpExpiry = 10; // minutes

    public function __construct(SpeedSmsService $sms)
    {
        $this->sms = $sms;
    }

    public function sendPhoneOtp(string $phone, string $purpose): bool
    {
        $this->lastError = null;

        try {
            $otp = $this->generate($phone, 'phone', $purpose); // ✅ saves to DB

            $appName = config('app.name');
            $message = "{$appName}: Your verification code is {$otp}. Valid for {$this->expireMinutes} minutes. Do not share this code.";

            $result = $this->sms->send($phone, $message); // ✅ correct property name

            // if (!$result) {
            //     $this->lastError = $this->sms->getLastError() ?? 'SMS gateway failed';
            //     return false;
            // }

            return true;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();

            return false;
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
    // ─────────────────────────────────────────────
    //  Generate & store OTP
    // ─────────────────────────────────────────────

    public function generate(string $identifier, string $type, string $purpose): string
    {
        // Invalidate old OTPs for this identifier + purpose
        OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'identifier' => $identifier,
            'type' => $type,
            'otp' => $otp,
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes($this->expireMinutes),
        ]);

        return $otp;
    }

    // ─────────────────────────────────────────────
    //  Verify OTP
    // ─────────────────────────────────────────────

    public function verify(string $identifier, string $type, string $purpose, string $otp): bool
    {
        $record = OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (! $record || ! $record->isValid($otp)) {
            return false;
        }

        $record->markUsed();

        return true;
    }

    // ─────────────────────────────────────────────
    //  Send via Email
    // ─────────────────────────────────────────────

    public function sendEmailOtp(string $email, string $purpose): bool
    {
        $otp = $this->generate($email, 'email', $purpose);

        $subject = match ($purpose) {
            'register' => 'Verify Your Email — '.config('app.name'),
            'reset_password' => 'Reset Your Password — '.config('app.name'),
            default => 'Your OTP Code — '.config('app.name'),
        };

        try {
            Mail::send('emails.otp', [
                'otp' => $otp,
                'purpose' => $purpose,
                'expires' => $this->expireMinutes,
            ], function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });

            return true;
        } catch (\Throwable $e) {
            Log::error('Email OTP failed', ['email' => $email, 'error' => $e->getMessage()]);

            return false;
        }
    }

    // ─────────────────────────────────────────────
    //  Send via SMS (Cambodia / SpeedSMS)
    // ─────────────────────────────────────────────

    // public function sendPhoneOtp(string $phone, string $purpose): bool
    // {
    //     $otp = $this->generate($phone, 'phone', $purpose);

    //     $appName = config('app.name');
    //     $message = "{$appName}: Your verification code is {$otp}. Valid for {$this->expireMinutes} minutes. Do not share this code.";

    //     return $this->sms->send($phone, $message);
    // }

    // ─────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────

    /**
     * Check MX record to validate that the email domain is real.
     */
    public function isRealEmailDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);
        if (empty($domain)) {
            return false;
        }

        // Check for MX records
        if (checkdnsrr($domain, 'MX')) {
            return true;
        }

        // Fallback: check A record (some small domains skip MX)
        if (checkdnsrr($domain, 'A')) {
            return true;
        }

        return false;
    }

    /**
     * Validate Cambodian phone number.
     * Supports: 0[1-9]x, +855[1-9]x, 855[1-9]x
     * Networks: 010,011,012,015,016,017,069,070,071,077,078,081,085,086,087,088,089,092,093,095,096,097,098,099
     */
    public function isValidCambodianPhone(string $phone): bool
    {
        $cleaned = preg_replace('/\D/', '', $phone);

        // Strip country code if present
        if (str_starts_with($cleaned, '855')) {
            $cleaned = '0'.substr($cleaned, 3);
        }

        // Must start with 0, be 9 or 10 digits
        if (! preg_match('/^0\d{8,9}$/', $cleaned)) {
            return false;
        }

        // Cambodian valid prefixes (network operator prefixes)
        $validPrefixes = [
            // Cellcard
            '011',
            '012',
            '017',
            '061',
            '076',
            '077',
            '078',
            '079',
            // Smart
            '010',
            '015',
            '016',
            '069',
            '070',
            '093',
            '098',
            // Metfone
            '071',
            '097',
            // CooTel
            '038',
            // Seatel
            '018',
            // qb
            '013',
            '080',
            '083',
            '085',
            '089',
            '092',
            '095',
            // Yes Telecom
            '096',
            // Common short prefix
            '086',
            '087',
            '088',
            // Others
            '099',
        ];

        $prefix3 = substr($cleaned, 0, 3);
        $prefix4 = substr($cleaned, 0, 4);

        foreach ($validPrefixes as $p) {
            if ($prefix3 === $p || $prefix4 === $p) {
                return true;
            }
        }

        return false;
    }
}
