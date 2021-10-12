<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AccessTokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken() || !PersonalAccessToken::findToken($request->bearerToken())) {
            return response()->json([
                'success' => false,
                'message' => 'Token not valid'
            ], 403);
        }

        return $next($request);
    }
}
