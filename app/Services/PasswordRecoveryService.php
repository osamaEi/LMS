<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordRecoveryService
{
    public function findAccount(string $identifier): ?User
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifier)->first();
        }

        $digits = preg_replace('/\D/', '', $identifier);
        $digits = preg_replace('/^(00966|966)/', '', $digits);
        $local = ltrim($digits, '0');
        if (! preg_match('/^5\d{8}$/', $local)) {
            return null;
        }

        return User::whereIn('phone', ['0'.$local, $local, '966'.$local, '+966'.$local, '00966'.$local])->first();
    }

    public function reset(User $user, string $code, string $password): bool
    {
        return DB::transaction(function () use ($user, $code, $password) {
            $user = User::whereKey($user->id)->lockForUpdate()->first();
            if (! $user || ! $user->phone) {
                return false;
            }

            // Reset codes must have actually been sent, even for test accounts.
            $otp = OtpVerification::where('phone', $user->phone)
                ->where('type', 'password_reset')->where('status', 'sent')
                ->whereNull('verified_at')->orderByDesc('id')->lockForUpdate()->first();
            if (! $otp || $otp->isExpired() || $otp->maxAttemptsReached()) {
                return false;
            }
            if (! hash_equals((string) $otp->otp, $code)) {
                $otp->incrementAttempts();
                return false;
            }

            $otp->markAsVerified();
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            $user->tokens()->delete();
            if (config('session.driver') === 'database') {
                DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $user->id)->delete();
            }

            return true;
        });
    }
}
