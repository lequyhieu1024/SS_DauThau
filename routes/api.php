<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\BiddingTypeController;
use App\Http\Controllers\Api\EvaluationCriteriaController;
use App\Http\Controllers\Api\FundingSourceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\BiddingFieldController;
use App\Http\Controllers\Api\BusinessActivityTypeController;
use App\Http\Controllers\Api\SelectionMethodController;

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
    // doanh nhghieejp
    Route::resource('enterprises', EnterpriseController::class);
    Route::put('enterprises/{enterprise}/changeActive', [EnterpriseController::class, 'changeActive']);
    // cấm tài khoản
    Route::post('enterprises/ban/{id}', [EnterpriseController::class, 'banEnterprise']);

    // Funding Sources
    Route::resource('funding-sources', FundingSourceController::class)->except('update');
    Route::patch('funding-sources/{id}', [FundingSourceController::class, 'update']);
    Route::patch('funding-sources/{id}/toggle-status', [FundingSourceController::class, 'toggleActiveStatus']);

    // Bidding Fields
    Route::get('bidding-fields/all-ids', [BiddingFieldController::class, 'getAllIds']);
    Route::resource('bidding-fields', BiddingFieldController::class)->except(['show', 'update', 'destroy']);
    Route::get('bidding-fields/{id}', [BiddingFieldController::class, 'show']);
    Route::patch('bidding-fields/{id}', [BiddingFieldController::class, 'update']);
    Route::patch('bidding-fields/{id}/toggle-status', [BiddingFieldController::class, 'toggleActiveStatus']);
    Route::delete('bidding-fields/{id}', [BiddingFieldController::class, 'destroy']);

    // Bidding Types
    Route::resource('bidding-types', BiddingTypeController::class)->except('update');
    Route::patch('bidding-types/{id}', [BiddingTypeController::class, 'update']);
    Route::patch('bidding-types/{id}/toggle-status', [BiddingTypeController::class, 'toggleActiveStatus']);

    /// Business Activity Types
    Route::get('business-activity-types/all-ids', [BusinessActivityTypeController::class, 'getAllIds']);
    Route::resource('business-activity-types', BusinessActivityTypeController::class)->except([
        'show',
        'update',
        'destroy'
    ]);
    Route::get('business-activity-types/{id}', [BusinessActivityTypeController::class, 'show']);
    Route::patch('business-activity-types/{id}', [BusinessActivityTypeController::class, 'update']);
    Route::patch(
        'business-activity-types/{id}/toggle-status',
        [BusinessActivityTypeController::class, 'toggleActiveStatus']
    );
    Route::delete('business-activity-types/{id}', [BusinessActivityTypeController::class, 'destroy']);

    // Industries
    Route::resource('industries', IndustryController::class)->except([
        'show',
        'update',
        'destroy'
    ]);
    Route::get('list-industries', [IndustryController::class, 'getListIndustries']);
    Route::get('industries/{id}', [IndustryController::class, 'show']);
    Route::patch('industries/{id}', [IndustryController::class, 'update']);
    Route::patch(
        'industries/{id}/toggle-status',
        [IndustryController::class, 'toggleActiveStatus']
    );
    Route::delete('industries/{id}', [IndustryController::class, 'destroy']);

    // Activity Logs
    Route::resource('activity-logs', ActivityLogController::class);

    // Selection Methods
    Route::resource('selection-methods', SelectionMethodController::class)->except('update');
    Route::patch('selection-methods/{id}', [SelectionMethodController::class, 'update']);
    Route::patch('selection-methods/{id}/toggle-status', [SelectionMethodController::class, 'toggleActiveStatus']);

    // Evaluation citeria - Tieu chi danh gia
    Route::resource('evaluation-criterias', EvaluationCriteriaController::class);
    Route::put('evaluation-criterias/{evaluation_criteria}/changeActive',
        [EvaluationCriteriaController::class, 'changeActive']);

    // Attachments
    Route::post('attachments', [AttachmentController::class, 'store']);
    Route::get('documents/{filename}', [AttachmentController::class, 'serveDocumentFile']);
});
