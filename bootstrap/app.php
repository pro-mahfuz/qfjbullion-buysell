<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )

    ->withMiddleware(function (Middleware $middleware) {

        // Trust HTTPS proxy / load balancer / nginx
        $middleware->trustProxies(
            at: '*',
            headers:
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        // Custom middleware aliases
        $middleware->alias([
            'acl' => \App\Http\Middleware\UserACLMiddleware::class,
            'otpcheckMiddleware' => \App\Http\Middleware\otpcheckMiddleware::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (AuthenticationException $e, Request $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);

            }

        });

    })

    ->create();