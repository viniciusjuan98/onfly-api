<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException as IlluminateAuthenticationException;
use App\Exceptions\AuthenticationException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as JWTTokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (TokenInvalidException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $exception = AuthenticationException::tokenInvalid();
                return response()->json($exception->toJsonResponse(), $exception->statusCode);
            }
        });

        $exceptions->render(function (JWTTokenExpiredException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $exception = AuthenticationException::tokenExpired();
                return response()->json($exception->toJsonResponse(), $exception->statusCode);
            }
        });

        $exceptions->render(function (JWTException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $exception = AuthenticationException::tokenNotProvided();
                return response()->json($exception->toJsonResponse(), $exception->statusCode);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json($e->toJsonResponse(), $e->statusCode);
            }
        });

        $exceptions->render(function (IlluminateAuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $exception = AuthenticationException::unauthorized();
                return response()->json($exception->toJsonResponse(), $exception->statusCode);
            }
        });
    })->create();
