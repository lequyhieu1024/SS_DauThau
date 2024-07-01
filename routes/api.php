<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\SystemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// get token
Route::group(['middleware' => 'web'], function () {
    Route::get('/csrf-token', function () {
        return csrf_token();
        //or return $request->session()->token();
    });
});
Route::group(['prefix' => 'auth'], function () {
    // API không cần đăng nhập
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Api gửi email khi bấm quên mật khẩu
    Route::post('send-email', [AuthController::class, 'forgotPasswordApi']);

    // API cần đăng nhập
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::get('logout', [AuthController::class, 'logout']);
    });
});
// 'middleware' => ['auth:sanctum']
//API cần đăng nhập
Route::group(['prefix' => 'admin','middleware' => ['auth:sanctum']], function () {

    // system
    Route::resource('system', SystemController::class);
    // Roles
    Route::resource('role', RoleController::class);
    //Staff
    Route::resource('staff', StaffController::class);
});
