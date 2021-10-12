<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckIsCarModelOwner
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
        $model = $request->route()->parameter('model');

        if (User::getUserByAccessToken($request->bearerToken())->id != $model->created_by) {
            return response()->json([
                'message' => 'You can\'t work with this car model'
            ], 403);
        }

        return $next($request);
    }
}
