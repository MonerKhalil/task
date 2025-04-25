<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserProfileResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials)) {
            return $this->responseError("Incorrect email address or password", 401);
        }

        $user = auth()->user();

        $token = $user->createToken($user->name,["*"])->plainTextToken;

        return $this->responseSuccess([
            'user' => UserProfileResource::make($user),
            'access_token' => $token,
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return $this->responseSuccess([],"Successfully logged out");
    }
}
