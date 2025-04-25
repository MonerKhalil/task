<?php

use App\Helpers\MyApp;
use App\Http\Controllers\Apis\AuthController;
use App\Http\Controllers\Apis\PostController;
use App\Http\Controllers\Apis\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function (){
    Route::middleware(["guest:api"])->post("auth/login",[AuthController::class,"login"]);

    Route::middleware(["auth:api"])->group(function (){
        Route::delete("auth/logout",[AuthController::class,"logout"]);
        MyApp::main()->crudProcess->routesCrud("users", UserController::class);
        MyApp::main()->crudProcess->routesCrud("posts", PostController::class);
        Route::get("show/my/posts",[PostController::class,"showMyPosts"]);
        Route::prefix("posts/post/{post_id}")->controller(PostController::class)->group(function (){
            Route::delete("delete","destroyPost");
            Route::post("add/comment","addComment");
        });
    });
});
