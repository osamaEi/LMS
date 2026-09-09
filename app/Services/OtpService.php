<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
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

        // Strip an international access prefix: 00966… or a stray 966 kept
        // alongside the local trunk zero (96605…).
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        while (str_starts_with($digits, '9660')) {
            $digits = '966' . substr($digits, 4);
        }

        if (str_starts_with($digits, '966')) {
            return $digits;
        }

        // Local forms: 05XXXXXXXX (trunk zero) or 5XXXXXXXX (bare).
        if (str_starts_with($digits, '0')) {
            return '966' . substr($digits, 1);
        }

        if (str_starts_with($digits, '5')) {
            return '966' . $digits;
        }

        return $digits;
    }

    /**
     * A destination OurSMS will accept: a Saudi mobile MSISDN, 9665XXXXXXXX.
     */
    protected function isValidSaudiMsisdn(string $msisdn): bool
    {
        return (bool) preg_match('/^9665\d{8}$/', $msisdn);
    }

    /**
     * Is this one of the configured testing phone numbers?
     */
    public function isTestPhone(string $phone): bool
    {
        $target = $this->normalizePhone($phone);

        if ($target === '') {
            return false;
        }

        $configured = (string) config('services.oursms.test_phones', '');

        foreach (explode(',', $configured) as $candidate) {
            if (trim($candidate) !== '' && $this->normalizePhone($candidate) === $target) {
                return true;
            }
        }

        // The phone may belong to an account flagged for testing by national id.
        return $this->phoneBelongsToTestAccount($target);
    }

    /**
     * Do any of the configured testing national ids own this phone number?
     */
    protected function phoneBelongsToTestAccount(string $normalisedPhone): bool
    {
        $ids = $this->testNationalIds();

        if ($ids === []) {
            return false;
        }

        try {
            $phones = User::whereIn('national_id', $ids)->pluck('phone');
        } catch (\Throwable $e) {
            // Never let the bypass lookup break a real send.
            Log::warning('Test national id lookup failed', ['error' => $e->getMessage()]);

            return false;
        }

        foreach ($phones as $accountPhone) {
            if ($accountPhone !== null && $this->normalizePhone($accountPhone) === $normalisedPhone) {
                return true;
            }
        }

        return false;
    }

    /**
     * National ids configured to bypass the real SMS, as a clean list.
     */
    public function testNationalIds(): array
    {
        $configured = (string) config('services.oursms.test_national_ids', '');

        return array_values(array_filter(array_map(
            static fn ($id) => preg_replace('/\D+/', '', (string) $id),
            explode(',', $configured)
        ), static fn ($id) => $id !== ''));
    }

    /**
     * Is this national id flagged for OTP bypass?
     */
    public function isTestNationalId(?string $nationalId): bool
    {
        if ($nationalId === null) {
            return false;
        }

        $target = preg_replace('/\D+/', '', $nationalId);

        return $target !== '' && in_array($target, $this->testNationalIds(), true);
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
        // Registration accepts the fixed code for every phone. Configured test
        // numbers retain their existing bypass for other OTP types.
        if (($type === 'registration' && hash_equals('123456', $otpCode))
            || ($this->isTestPhone($phone) && hash_equals($this->testCode(), $otpCode))) {
            $otp = OtpVerification::where('phone', $phone)
                ->where('type', $type)
                ->whereNull('verified_at')
                ->latest()
                ->first();

            if ($otp) {
                $otp->markAsVerified();
            } else {
                OtpVerification::create([
                    'phone' => $phone,
                    'otp' => $otpCode,
                    'type' => $type,
                    'status' => 'sent',
                    'expires_at' => now()->addMinutes(5),
                    'attempts' => 0,
                    'sent_at' => now(),
                    'verified_at' => now(),
                ]);
            }

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
            ->where('verified_at', '>=', now()->subHour())
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

        $destination = $this->normalizePhone($phone);

        // OurSMS answers an unroutable destination with a generic HTTP 400
        // ("Prefix not supported"), so catch it here where we can say which
        // number was at fault.
        if (!$this->isValidSaudiMsisdn($destination)) {
            throw new RuntimeException(
                "Invalid Saudi mobile number for SMS: '{$phone}' (normalised to '{$destination}')."
            );
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
