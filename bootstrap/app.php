<?php

use App\Http\Middleware\IdentifyTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add global middleware
        $middleware->append([
            \App\Http\Middleware\CorsMiddleware::class,
        ]);

        // Add middleware aliases (keep your existing ones + add new ones)
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'identify_tenant' => IdentifyTenant::class,
            'check_tenant_payment' => \App\Http\Middleware\CheckTenantPayment::class,
            // Add JWT and API middleware aliases
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class,
            'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);

        // Configure API middleware group - TEMPORARILY COMMENT OUT ApiResponseMiddleware
        $middleware->api(append: [
            \App\Http\Middleware\CorsMiddleware::class,
            // \App\Http\Middleware\ApiResponseMiddleware::class,  // ← COMMENTED OUT FOR DEBUGGING
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
