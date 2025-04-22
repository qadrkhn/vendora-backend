<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // custom logic to send 401 to api access without tokens
        // or just to home page in case of web
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                abort(response()->json(['message' => 'Unauthenticated.'], 401));
            }
            return route('home');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }
                return response()->view('errors.401', [], 401);
            }
        });
    })->create();
