<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define rate limit for verifying OTP
        RateLimiter::for('verify-otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email'));
        });

        // Define rate limit for resending OTP
        RateLimiter::for('resend-otp', function (Request $request) {
            return Limit::perMinute(2)->by($request->ip() . '|' . $request->input('email'));
        });
    }
}
