<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'User registered successfully');
    }
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Hi ' . $user->name . ', welcome back');
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'You have successfully logged out and the token was deleted');

    }
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse(null, 'Logged out from all devices');
    }
}
