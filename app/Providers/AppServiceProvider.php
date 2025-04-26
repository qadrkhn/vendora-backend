<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Passport
        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Macros
        Response::macro('error', function (
            string $message = 'Something went wrong.',
            int $status = 500,
            mixed $error = null
        ) {
            return response()->json([
                'message' => $message,
                'error' => app()->isLocal() ? $error : null,
            ], $status);
        });

        Response::macro('success', function (
            $data,
            int $status = 200,
            string $message = 'OK'
        ) {
            return response()->json([
                'message' => $message,
                'data' => $data,
            ], $status);
        });

    }
}
