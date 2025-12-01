<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Log;

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

        // Send SMS
        $this->sendSms($phone, $otpCode);

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
        // For now, just log the OTP
        // In production, integrate with Unifonic or Twilio
        Log::info("OTP for {$phone}: {$otpCode}");

        // TODO: Implement SMS provider integration
        // Example for Unifonic:
        // $this->sendViaUnifonicprovider($phone, $otpCode);

        // Example for Twilio:
        // $this->sendViaTwilio($phone, $otpCode);
    }

    /**
     * Send OTP via Unifonic (placeholder)
     */
    protected function sendViaUnifonic(string $phone, string $otpCode): void
    {
        // Implementation example:
        // $apiKey = config('services.unifonic.api_key');
        // $senderId = config('services.unifonic.sender_id');
        //
        // $message = "Your verification code is: {$otpCode}. Valid for 5 minutes.";
        //
        // Http::asForm()->post('https://api.unifonic.com/rest/SMS/messages', [
        //     'AppSid' => $apiKey,
        //     'SenderID' => $senderId,
        //     'Recipient' => $phone,
        //     'Body' => $message,
        // ]);
    }

    /**
     * Send OTP via Twilio (placeholder)
     */
    protected function sendViaTwilio(string $phone, string $otpCode): void
    {
        // Implementation example:
        // $accountSid = config('services.twilio.account_sid');
        // $authToken = config('services.twilio.auth_token');
        // $fromNumber = config('services.twilio.from_number');
        //
        // $message = "Your verification code is: {$otpCode}. Valid for 5 minutes.";
        //
        // $client = new \Twilio\Rest\Client($accountSid, $authToken);
        // $client->messages->create($phone, [
        //     'from' => $fromNumber,
        //     'body' => $message
        // ]);
    }
}
