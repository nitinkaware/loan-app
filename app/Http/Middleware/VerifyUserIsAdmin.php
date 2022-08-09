<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'You are not authorized to access this resource.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
