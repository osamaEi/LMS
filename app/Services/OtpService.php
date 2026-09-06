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
        if ($this->isTestPhone($this->phoneRaw ?? '')) {
            return $this->testCode();
        }

        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Phone currently being processed, used to pin the test OTP code.
     */
    protected ?string $phoneRaw = null;

    /**
     * Normalize a phone to its Saudi MSISDN form (9665XXXXXXXX) for comparison.
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '966' . substr($digits, 1);
        } elseif (str_starts_with($digits, '5')) {
            $digits = '966' . $digits;
        }

        return $digits;
    }

    /**
     * Is this one of the configured testing phone numbers?
     */
    public function isTestPhone(string $phone): bool
    {
        $configured = (string) config('services.oursms.test_phones', '');

        if (trim($configured) === '') {
            return false;
        }

        $target = $this->normalizePhone($phone);

        foreach (explode(',', $configured) as $candidate) {
            if ($candidate !== '' && $this->normalizePhone($candidate) === $target && $target !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * The fixed OTP accepted for testing phone numbers.
     */
    protected function testCode(): string
    {
        return (string) config('services.oursms.test_code', '123456');
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
        $this->phoneRaw = $phone;
        $otpCode = $this->generateOtp();

        // Create OTP record
        $otp = OtpVerification::create([
            'phone' => $phone,
            'otp' => $otpCode,
            'type' => $type,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);

        try {
            $providerReference = $this->sendSms($phone, $otpCode);
            $otp->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_reference' => $providerReference,
            ]);
        } catch (\Throwable $e) {
            $otp->update([
                'status' => 'failed',
                'failure_reason' => mb_substr($e->getMessage(), 0, 1000),
            ]);
            throw $e;
        }

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verify(string $phone, string $otpCode, string $type = 'registration'): bool
    {
        // Testing numbers: the fixed code always passes, regardless of
        // expiry, attempts, or whether an OTP was ever sent.
        if ($this->isTestPhone($phone) && hash_equals($this->testCode(), $otpCode)) {
            $otp = OtpVerification::where('phone', $phone)
                ->where('type', $type)
                ->whereNull('verified_at')
                ->latest()
                ->first();

            $otp?->markAsVerified() ?? OtpVerification::create([
                'phone' => $phone,
                'otp' => $otpCode,
                'type' => $type,
                'status' => 'sent',
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
                'sent_at' => now(),
                'verified_at' => now(),
            ]);

            return true;
        }

        $otp = OtpVerification::where('phone', $phone)
            ->where('type', $type)
            ->where('status', 'sent')
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
    protected function sendSms(string $phone, string $otpCode): ?string
    {
        // Testing numbers never hit the provider; the code is fixed anyway.
        if ($this->isTestPhone($phone)) {
            Log::info("Test OTP for {$phone}: {$otpCode}");
            return 'test-bypass';
        }

        $apiKey = config('services.oursms.api_key');
        $senderId = config('services.oursms.sender_id');

        if (!$apiKey || !$senderId) {
            if (app()->environment('local', 'testing')) {
                Log::info("OTP for {$phone}: {$otpCode}");
                return 'local-log';
            }

            throw new RuntimeException('OurSMS credentials are not configured.');
        }

        $destination = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($destination, '0')) {
            $destination = '966' . substr($destination, 1);
        } elseif (str_starts_with($destination, '5')) {
            $destination = '966' . $destination;
        }

        $response = Http::baseUrl(rtrim(config('services.oursms.base_url'), '/'))
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

        return $response->json('jobId')
            ?? $response->json('job_id')
            ?? $response->json('messages.0.msgId')
            ?? $response->json('messages.0.id');
    }
}
