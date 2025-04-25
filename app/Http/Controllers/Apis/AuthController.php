<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::query()->where('email',$credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'],$user->password)) {
            return $this->responseError("Incorrect email address or password", 401);
        }

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
