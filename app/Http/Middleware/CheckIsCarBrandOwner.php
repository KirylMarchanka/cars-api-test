<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckIsCarBrandOwner
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $brand = $request->route()->parameter('brand');

        if (User::getUserByAccessToken($request->bearerToken())->id != $brand->created_by) {
            return response()->json([
                'message' => 'You can\'t work with this car brand'
            ], 403);
        }

        return $next($request);
    }
}
