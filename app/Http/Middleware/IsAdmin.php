<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Note: This middleware assumes the user is already authenticated
     * by the 'auth:api' middleware applied before it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user() || !auth()->user()->is_admin) {
            throw new AccessDeniedHttpException('Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}

