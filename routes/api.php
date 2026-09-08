<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/login', 'Api\\V1Controller@login');
    Route::get('/feed', 'Api\\V1Controller@feed');
    Route::get('/posts/{post}', 'Api\\V1Controller@post');
    Route::get('/users', 'Api\\V1Controller@users');
    Route::get('/users/{user}', 'Api\\V1Controller@user');
    Route::middleware('api.token')->group(function () {
        Route::get('/me', 'Api\\V1Controller@me');
        Route::post('/logout', 'Api\\V1Controller@logout');
        Route::post('/posts', 'Api\\V1Controller@createPost');
    });
});
