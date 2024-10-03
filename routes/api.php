<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BidBondController;
use App\Http\Controllers\Api\BiddingFieldController;
use App\Http\Controllers\Api\BiddingTypeController;
use App\Http\Controllers\Api\BidDocumentController;
use App\Http\Controllers\Api\BusinessActivityTypeController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\EvaluationCriteriaController;
use App\Http\Controllers\Api\FundingSourceController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\ProcurementCategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SelectionMethodController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

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
    Route::get('list-staffs', [StaffController::class, 'getNameAndIds']);
    Route::post('staff/ban/{id}', [StaffController::class, 'banStaff']);
    // doanh nhghieejp
    Route::resource('enterprises', EnterpriseController::class);
    Route::put('enterprises/{enterprise}/changeActive', [EnterpriseController::class, 'changeActive']);
    // cấm tài khoản
    Route::post('enterprises/ban/{id}', [EnterpriseController::class, 'banEnterprise']);
    Route::get('list-enterprises', [EnterpriseController::class, 'getnameAndIds']);
    // Funding Sources
    Route::resource('funding-sources', FundingSourceController::class)->except('update');
    Route::patch('funding-sources/{id}', [FundingSourceController::class, 'update']);
    Route::patch('funding-sources/{id}/toggle-status', [FundingSourceController::class, 'toggleActiveStatus']);
    Route::get('list-funding-sources', [FundingSourceController::class, 'getnameAndIds']);

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
    Route::get('list-industries', [IndustryController::class, 'getNameAndIds']);
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
    Route::get('list-selection-methods', [SelectionMethodController::class, 'getNameAndIds']);
    // Evaluation citeria - Tieu chi danh gia
    Route::resource('evaluation-criterias', EvaluationCriteriaController::class);
    Route::put('evaluation-criterias/{evaluation_criteria}/changeActive',
        [EvaluationCriteriaController::class, 'changeActive']);

    // Procurement Categories / Lĩnh vực mua sắm công
    Route::resource('procurement-categories', ProcurementCategoryController::class);
    Route::put('procurement-categories/{procurement_category}/changeActive',
        [ProcurementCategoryController::class, 'changeActive']);
    Route::get('list-procurement-categories', [ProcurementCategoryController::class, 'getNameAndIds']);

    // Project - Dự án
    Route::resource('projects', ProjectController::class);
    Route::get('list-projects', [ProjectController::class, 'getNameAndIds']);
    Route::put('projects/{project}/approve', [ProjectController::class, 'approveProject']);
    // Banner
    Route::resource('banners', BannerController::class)->except('update');
    Route::patch('banners/{id}', [BannerController::class, 'update']);
    Route::patch('banners/{id}/toggle-status', [BannerController::class, 'toggleActiveStatus']);
    Route::put('evaluation-criterias/{evaluation_criteria}/changeActive',
        [EvaluationCriteriaController::class, 'changeActive']);

    // Attachments
    Route::post('attachments', [AttachmentController::class, 'store']);

    // Bid bonds
    Route::resource('bid-bonds', BidBondController::class)->except('update');
    Route::patch('bid-bonds/{id}', [BidBondController::class, 'update']);

    // Bid documents
    Route::resource('bid-documents', BidDocumentController::class);
    Route::get('bid-documents/check-bid-participation/{projectId}', [BidDocumentController::class, 'checkBidParticipation']);
    Route::patch('bid-documents/approve/{id}', [BidDocumentController::class, 'approveBidDocument']);

});
