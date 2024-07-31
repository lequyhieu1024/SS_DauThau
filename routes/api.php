<?php

use App\Http\Controllers\Api\BiddingFieldController;
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
        return response()->json(['csrfToken' => csrf_token()]);
    });
});
// check login
Route::get('not-yet-authenticated', [AuthController::class, 'notYetAuthenticated'])->name('not-yet-authenticated');

Route::group(['prefix' => 'auth'], function () {
    // API không cần đăng nhập
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Api gửi email khi bấm quên mật khẩu
    Route::post('send-email', [AuthController::class, 'forgotPasswordApi']);

    // API cần đăng nhập
    Route::group(['middleware' => ['auth.jwt']], function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
//API cần đăng nhập
Route::group(['prefix' => 'admin', 'middleware' => ['auth.jwt']], function () {
    // system
    Route::resource('system', SystemController::class);
    // Roles
    Route::resource('role', RoleController::class);
    //Staff
    Route::resource('staff', StaffController::class);
    // cấm tài khoản
    Route::post('staff/ban/{id}', [StaffController::class, 'banStaff']);

    Route::get('bidding-fields/all-ids', [BiddingFieldController::class, 'getAllIds']);
    Route::resource('bidding-fields', BiddingFieldController::class)->except(['show', 'update', 'destroy']);
    Route::get('bidding-fields/{id}', [BiddingFieldController::class, 'show']);
    Route::patch('bidding-fields/{id}', [BiddingFieldController::class, 'update']);
    Route::delete('bidding-fields/{id}', [BiddingFieldController::class, 'destroy']);
});
