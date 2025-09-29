<?php

use App\Http\Middleware\LogRequest;
use App\Http\Middleware\User\PermissionMiddleware;
use App\Http\Middleware\User\RoleMiddleware;
use App\Http\Middleware\User\RoleOrPermissionMiddleware;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // $middleware->redirectGuestsTo(function (Request $request) {});
        // $middleware->prepend(LogRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception, Request $request) {
            if ($request->expectsJson()) {
                $response = new ApiResponse;

                if ($exception instanceof AuthenticationException) {
                    return $response->status(401)
                        ->message($exception->getMessage())
                        ->toResponse($request);
                }
                if ($exception instanceof NotFoundHttpException) {
                    $response->status(404);
                    if ($exception->getPrevious() and $exception->getPrevious() instanceof ModelNotFoundException) {
                        //
                    } else {
                        $response->message($exception->getMessage());
                    }

                    return $response->toResponse($request);
                }
                if ($exception instanceof ModelNotFoundException) {
                    return $response->status(404)
                        ->toResponse($request);
                }
                if ($exception instanceof UnauthorizedException) {
                    return $response->status(401)
                        ->toResponse($request);
                }
                if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                    return $response->status(403)
                        ->toResponse($request);
                }
                if ($exception instanceof MethodNotAllowedHttpException) {
                    return $response->status(403)
                        ->toResponse($request);
                }
                if ($exception instanceof ValidationException) {
                    return $response->status($exception->status)
                        ->message($exception->getMessage())
                        ->errors($exception->validator->messages())
                        ->toResponse($request);
                }
                if ($exception instanceof ThrottleRequestsException) {
                    return $response->status(429)
                        ->toResponse($request);
                }
                if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpException) {
                    return $response->status($exception->getStatusCode())
                        ->message(app()->isProduction() ? null : $exception->getMessage())
                        ->toResponse($request);
                }

                return $response->status(500)
                    ->data(app()->isProduction() ? [] : $exception->getTrace())
                    ->toResponse($request);
            }
        });
    })->create();
