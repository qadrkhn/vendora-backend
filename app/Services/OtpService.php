<?php

// app/Services/OtpService.php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendOtpJob;
use Carbon\Carbon;

class OtpService
{
    public string $queue = 'otp';

    protected int $otpExpiryMinutes = 10;

    public function generateAndSend(User $user): void
    {
        $otp = rand(100000, 999999);

        $user->update([
            'email_otp' => $otp,
            'email_verified' => false,
            'email_otp_expires_at' => now()->addMinutes($this->otpExpiryMinutes),
        ]);

        dispatch(new SendOtpJob($user->email, $otp));
    }

    public function verify(User $user, string $otp): bool
    {
        if ($user->email_otp !== $otp) {
            return false;
        }

        if (!$user->email_otp_expires_at || now()->greaterThan($user->email_otp_expires_at)) {
            return false;
        }

        return true;
    }

    public function canResend(User $user): bool
    {
        // resend only once every 60 seconds
        return !$user->email_otp_expires_at || now()->diffInSeconds($user->email_otp_expires_at, false) < ($this->otpExpiryMinutes * 60 - 60);
    }
}
