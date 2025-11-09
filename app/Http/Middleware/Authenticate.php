<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * For API routes, we don't redirect, we return JSON error responses.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}

