<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\{LoginRequest, SignupRequest};
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Signup new user
     *
     * @param SignupRequest $request
     * @return JsonResponse
     */
    public function signup(SignupRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::validate($request->validated())) {
            return response()->json([
                'message' => 'Incorrect email or password'
            ], 401);
        }

        $user = User::query()->where('email', $request->validated()['email'])->first();

        return response()->json([
            'message' => 'Successful login',
            'data' => [
                'token' => $user->createToken('login_token')->plainTextToken
            ]
        ]);
    }
}
