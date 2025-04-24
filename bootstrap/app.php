<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \App\Http\Middleware\AttachAccessTokenFromCookie::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                abort(response()->json(['message' => 'Unauthenticated.'], 401));
            }
            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 401: Token expired/invalid (Passport fallback)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated. Invalid or expired token.',
                ], 401)->withCookie(cookie()->forget('access_token'));
            }

            return redirect()->route('login');
        });

        // 403: Access forbidden
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }
                return response()->view('errors.401', [], 401);
            }
        });

        // 429: Too many requests (Throttle)
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many requests. Please try again later.',
                    'hint' => asset('429_Response.png'), // Optional fun hint image
                ], 429);
            }

            return response()->view('errors.429', [], 429);
        });

    })->create();
