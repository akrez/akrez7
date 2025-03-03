<?php

use App\Http\Middleware\LogRequest;
use App\Http\Middleware\User\PermissionMiddleware;
use App\Http\Middleware\User\RoleMiddleware;
use App\Http\Middleware\User\RoleOrPermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
        // $middleware->prepend(LogRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
