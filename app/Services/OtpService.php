<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OtpService
{
    /**
     * Generate a 6-digit OTP
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to phone number
     */
    public function send(string $phone, string $type = 'registration'): OtpVerification
    {
        // Delete any existing unverified OTPs for this phone and type
        OtpVerification::where('phone', $phone)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->delete();

        // Generate new OTP
        $otpCode = $this->generateOtp();

        // Create OTP record
        $otp = OtpVerification::create([
            'phone' => $phone,
            'otp' => $otpCode,
            'type' => $type,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);

        try {
            $this->sendSms($phone, $otpCode);
        } catch (\Throwable $e) {
            $otp->delete();
            throw $e;
        }

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verify(string $phone, string $otpCode, string $type = 'registration'): bool
    {
        $otp = OtpVerification::where('phone', $phone)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        // Check if expired
        if ($otp->isExpired()) {
            return false;
        }

        // Check if max attempts reached
        if ($otp->maxAttemptsReached()) {
            return false;
        }

        // Check if OTP matches
        if ($otp->otp !== $otpCode) {
            $otp->incrementAttempts();
            return false;
        }

        // Mark as verified
        $otp->markAsVerified();

        return true;
    }

    /**
     * Check if phone has verified OTP
     */
    public function isVerified(string $phone, string $type = 'registration'): bool
    {
        return OtpVerification::where('phone', $phone)
            ->where('type', $type)
            ->whereNotNull('verified_at')
            ->where('created_at', '>=', now()->subHour())
            ->exists();
    }

    /**
     * Send SMS via configured provider
     */
    protected function sendSms(string $phone, string $otpCode): void
    {
        $apiKey = config('services.oursms.api_key');
        $senderId = config('services.oursms.sender_id');

        if (!$apiKey || !$senderId) {
            if (app()->environment('local', 'testing')) {
                Log::info("OTP for {$phone}: {$otpCode}");
                return;
            }

            throw new RuntimeException('OurSMS credentials are not configured.');
        }

        $destination = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($destination, '0')) {
            $destination = '966' . substr($destination, 1);
        } elseif (str_starts_with($destination, '5')) {
            $destination = '966' . $destination;
        }

        Http::baseUrl(rtrim(config('services.oursms.base_url'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->timeout((int) config('services.oursms.timeout', 10))
            ->retry(2, 250)
            ->post('/msgs/sms', [
                'src' => $senderId,
                'dests' => [$destination],
                'body' => "رمز التحقق الخاص بك هو: {$otpCode}. صالح لمدة 5 دقائق.",
                'msgClass' => 'transactional',
                'secure' => true,
            ])
            ->throw();
    }
}
