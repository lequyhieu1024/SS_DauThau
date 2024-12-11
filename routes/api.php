<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BidBondController;
use App\Http\Controllers\Api\BiddingFieldController;
use App\Http\Controllers\Api\BiddingResultController;
use App\Http\Controllers\Api\BiddingTypeController;
use App\Http\Controllers\Api\BidDocumentController;
use App\Http\Controllers\Api\BusinessActivityTypeController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\EvaluateController;
use App\Http\Controllers\Api\EvaluationCriteriaController;
use App\Http\Controllers\Api\FeedbackComplaintController;
use App\Http\Controllers\Api\FundingSourceController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\InstructController;
use App\Http\Controllers\Api\IntroductionController;
use App\Http\Controllers\Api\PostCatalogController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProcurementCategoryController;
use App\Http\Controllers\Api\ProjectComparisonController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReputationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SelectionMethodController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\WorkProgressController;
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
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);

    Route::post('send-mail-forgot-password', [AuthController::class, 'sendMailForPasswordReset']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Api gửi email khi bấm quên mật khẩu
    Route::post('send-email', [AuthController::class, 'forgotPasswordApi']);

    // API cần đăng nhập
    Route::group(['middleware' => ['auth.jwt']], function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('edit-profile', [AuthController::class, 'editProfile']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
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
    Route::post('staff/ban/{id}', [StaffController::class, 'banStaff']);
    // doanh nhghieejp
    Route::resource('enterprises', EnterpriseController::class);
    // chuyển trạng thái
    Route::put('enterprises/{enterprise}/changeActive', [EnterpriseController::class, 'changeActive']);
    Route::put('enterprises/{enterprise}/move-to-blacklist', [EnterpriseController::class, 'moveToBlacklist']);
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

    // Feedback Complaints
    Route::resource('feedback-complaints', FeedbackComplaintController::class)->except('update');
    Route::patch('feedback-complaints/{id}', [FeedbackComplaintController::class, 'update']);

    // Evaluation citeria - Tieu chi danh gia
    Route::resource('evaluation-criterias', EvaluationCriteriaController::class);
    Route::put(
        'evaluation-criterias/{evaluation_criteria}/changeActive',
        [EvaluationCriteriaController::class, 'changeActive']
    );

    // Procurement Categories / Lĩnh vực mua sắm công
    Route::resource('procurement-categories', ProcurementCategoryController::class);
    Route::put(
        'procurement-categories/{procurement_category}/changeActive',
        [ProcurementCategoryController::class, 'changeActive']
    );

    // Project - Dự án
    Route::resource('projects', ProjectController::class);
    Route::put('projects/{project}/approve', [ProjectController::class, 'approveProject']);
    Route::get("projects/get-list-project/by-staff", [ProjectController::class, 'getProjectByStaff']);
    // Banner
    Route::resource('banners', BannerController::class)->except('update');
    Route::patch('banners/{id}', [BannerController::class, 'update']);
    Route::patch('banners/{id}/toggle-status', [BannerController::class, 'toggleActiveStatus']);

    // Attachments
    Route::resource('attachments', AttachmentController::class);

    // Bid bonds
    Route::resource('bid-bonds', BidBondController::class)->except('update');
    Route::patch('bid-bonds/{id}', [BidBondController::class, 'update']);

    // Bid documents
    Route::resource('bid-documents', BidDocumentController::class);
    Route::get('bid-documents/check-bid-participation/{projectId}', [BidDocumentController::class, 'checkBidParticipation']);
    Route::patch('bid-documents/approve/{id}', [BidDocumentController::class, 'approveBidDocument']);

    // Post catalogs
    Route::resource('post-catalogs', PostCatalogController::class)->except('update');
    Route::patch('post-catalogs/{id}', [PostCatalogController::class, 'update']);
    Route::patch('post-catalogs/{id}/toggle-status', [PostCatalogController::class, 'toggleActiveStatus']);

    // Posts
    Route::resource('posts', PostController::class);

    //support
    Route::resource('supports', SupportController::class);

    Route::resource('bidding-results', BiddingResultController::class);

    Route::resource('employees', EmployeeController::class);

    Route::resource('tasks', TaskController::class);

    Route::resource('evaluates', EvaluateController::class);

    Route::get('reputations', [ReputationController::class, 'index']);

    Route::apiResource('work-progresses', WorkProgressController::class);

    // enterprise chart
    Route::post('charts/enterprises/detail-enterprise-by-ids', [EnterpriseController::class, 'getDetailEnterpriseByIds']);
    Route::post('charts/enterprises/employee-qty-statistic-by-enterprise', [EnterpriseController::class, 'employeeQtyStatisticByEnterprise']);
    Route::get('charts/enterprises/{enterprise}/employee-education-level-statistic-by-enterprise', [EnterpriseController::class, 'employeeEducationLevelStatisticByEnterprise']);
    Route::post('charts/enterprises/employee-salary-statistic-by-enterprise', [EnterpriseController::class, 'employeeSalaryStatisticByEnterprise']);
//    Route::post('charts/enterprises/employee-working-time-statistic-by-enterprise', [EnterpriseController::class, 'employeeWorkingTimeStatisticByEnterprise']);
    Route::post('charts/enterprises/employee-age-statistic-by-enterprise', [EnterpriseController::class, 'employeeAgeStatisticByEnterprise']);
    Route::post('charts/enterprises/employee-project-statistic-by-enterprise', [EnterpriseController::class, 'employeeProjectStatisticByEnterprise']);
    Route::post('charts/enterprises/employee-result-bidding-statistic-by-enterprise', [EnterpriseController::class, 'biddingResultStatisticsByEnterprise']);
    Route::post('charts/enterprises/average-difficulty-level-tasks-by-enterprise', [EnterpriseController::class, 'averageDifficultyLevelTasksByEnterprise']);
    Route::post('charts/enterprises/average-difficulty-level-tasks-by-employee', [EnterpriseController::class, 'averageDifficultyLevelTasksByEmployee']);
    Route::post('charts/enterprises/average-feedback-by-employee', [EnterpriseController::class, 'averageFeedbackByEmployee']);
    Route::post('charts/enterprises/project-completed-by-enterprise', [EnterpriseController::class, 'projectCompletedByEnterprise']);
    Route::post('charts/enterprises/project-won-by-enterprise', [EnterpriseController::class, 'projectWonByEnterprise']);
    Route::post('charts/enterprises/evaluations-statistics-by-enterprise', [EnterpriseController::class, 'evaluationsStatisticsByEnterprise']);
    Route::post('charts/enterprises/reputations-statistics-by-enterprise', [EnterpriseController::class, 'reputationsStatisticsByEnterprise']);

    // Compare Project
    Route::post('compare-projects/detail-project-by-ids', [ProjectComparisonController::class, 'getDetailProjectByIds']);
    Route::post('compare-projects/compare-bar-chart-total-amount', [ProjectComparisonController::class, 'compareBarChartTotalAmount']);
    Route::post('compare-projects/comparing-construction-time', [ProjectComparisonController::class, 'compareBarChartConstructionTime']);
    Route::post('compare-projects/comparing-did-submission-time', [ProjectComparisonController::class, 'compareBarChartBidSubmissionTime']);
    Route::post('compare-projects/compare-pie-chart-total-amount', [ProjectComparisonController::class, 'comparePieChartTotalAmount']);
    Route::post('compare-projects/compare-bidder-count', [ProjectComparisonController::class, 'compareBarChartBidderCount']);
    Route::post('compare-projects/get-difficulty-of-project', [ProjectComparisonController::class, 'compareDifficultyOfProjects']);
    Route::post('compare-projects/get-work-progress-of-project', [ProjectComparisonController::class, 'compareWorkProgressOfProjects']);


    // Introductions
    Route::resource('introductions', IntroductionController::class);
    Route::put('introductions/{introductions}/changeActive', [IntroductionController::class, 'changeActive']);

    // Instructs
    Route::resource('instructs', InstructController::class);
    Route::put('instructs/{instructs}/changeActive', [InstructController::class, 'changeActive']);

    // count
    Route::get('count-projects', [DashboardController::class, 'countProjects']);
    Route::get('count-enterprises', [DashboardController::class, 'countEnterprises']);
    Route::get('count-industries', [DashboardController::class, 'countIndustries']);
    Route::get('count-staff', [DashboardController::class, 'countStaff']);
    Route::get('count-bidBond', [DashboardController::class, 'countBidBond']);
    Route::get('count-biddingResult', [DashboardController::class, 'countBiddingResult']);
    Route::get('count-evaluate', [DashboardController::class, 'countEvaluate']);
    Route::get('count-evaluation-criteria', [DashboardController::class, 'countEvaluationCriteria']);
    Route::get('count-feedback-complaint', [DashboardController::class, 'countFeedbackComplaint']);
    Route::get('count-funding-source', [DashboardController::class, 'countFundingSource']);
    Route::get('count-post-catalog', [DashboardController::class, 'countPostCatalog']);
    Route::get('count-post', [DashboardController::class, 'countPost']);
    Route::get('count-procurement-category', [DashboardController::class, 'countProcurementCategory']);
    Route::get('count-selection-method', [DashboardController::class, 'countSelectionMethod']);
    Route::get('count-support', [DashboardController::class, 'countSupport']);
    Route::get('count-task', [DashboardController::class, 'countTask']);

});

Route::group(['prefix' => 'admin'], function () {
    // general chart
    Route::get('dashboard/charts/project-by-industry', [DashboardController::class, 'projectByIndustry']);
    Route::get('dashboard/charts/project-by-fundingsource', [DashboardController::class, 'projectByFundingSource']);
    Route::get('dashboard/charts/project-by-domestic', [DashboardController::class, 'projectByIsDomestic']);
    Route::get('dashboard/charts/project-by-submission-method', [DashboardController::class, 'projectBySubmissionMethod']);
    Route::get('dashboard/charts/project-by-selection-method', [DashboardController::class, 'projectBySelectionMethod']);
    Route::get('dashboard/charts/project-by-tenderer-investor', [DashboardController::class, 'projectByTendererAndInvestor']);
    Route::get('dashboard/charts/project-by-organization-type', [DashboardController::class, 'enterpriseByOrganizationType']);
    Route::get('dashboard/charts/average-project-duration-by-industry', [DashboardController::class, 'averageProjectDurationByIndustry']);
    Route::get('dashboard/charts/top-tenderers-by-project-count', [DashboardController::class, 'topTenderersByProjectCount']);
    Route::get('dashboard/charts/top-tenderers-by-project-total-amount', [DashboardController::class, 'topTenderersByProjectTotalAmount']);
    Route::get('dashboard/charts/top-investors-by-project-partial', [DashboardController::class, 'topInvestorsByProjectPartial']);
    Route::get('dashboard/charts/top-investors-by-project-full', [DashboardController::class, 'topInvestorsByProjectFull']);
    Route::get('dashboard/charts/top-investors-by-project-total-amount', [DashboardController::class, 'topInvestorsByProjectTotalAmount']);
    Route::post('dashboard/charts/top-enterprises-have-completed-projects-by-industry', [DashboardController::class, 'topEnterprisesHaveCompletedProjectsByIndustry']);
    Route::post('dashboard/charts/top-enterprises-have-completed-projects-by-funding-source', [DashboardController::class, 'topEnterprisesHaveCompletedProjectsByFundingSource']);
    Route::post('dashboard/charts/time-joining-website-of-enterprise', [DashboardController::class, 'timeJoiningWebsiteOfEnterprise']);
    Route::post('dashboard/charts/projects-status-per-month', [DashboardController::class, 'projectsStatusPerMonth']);
    Route::post('dashboard/charts/industry-has-the-most-project', [DashboardController::class, 'top10IndustryHasTheMostProject']);
    Route::post('dashboard/charts/industry-has-the-most-enterprise', [DashboardController::class, 'top10IndustryHasTheMostEnterprise']);

    Route::get('list-projects', [ProjectController::class, 'getNameAndIds']);
    Route::get('list-employees', [EmployeeController::class, 'getNameAndIds']);
    Route::get('list-project-has-bidding-result', [ProjectController::class, 'getNameAndIdProjectHasBidingResult']);
    Route::get('list-procurement-categories', [ProcurementCategoryController::class, 'getNameAndIds']);
    Route::get('list-staffs', [StaffController::class, 'getNameAndIds']);
    Route::get('list-evaluation-criterias', [EvaluationCriteriaController::class, 'getNameAndIds']);
    Route::get('list-selection-methods', [SelectionMethodController::class, 'getNameAndIds']);
    Route::get('list-funding-sources', [FundingSourceController::class, 'getnameAndIds']);
    Route::get('list-enterprises', [EnterpriseController::class, 'getnameAndIds']);
    Route::get('list-industries', [IndustryController::class, 'getNameAndIds']);
    Route::get('list-bid-documents', [BidDocumentController::class, 'getNameAndIds']);
    Route::get('list-bid-bonds', [BidBondController::class, 'getBondNumberAndIds']);

});


// Landing page
Route::get('get-banners', [BannerController::class, 'getBannersLandipage']);
Route::get('get-posts', [PostController::class, 'getPostsLandipage']);
Route::get('get-catalogs', [PostCatalogController::class, 'getCatalogsLandipage']);
Route::get('get-post-by-catalog/{catalog}', [PostController::class, 'getPostsByCatalogLandipage']);
Route::get('get-post/{post}', [PostController::class, 'getPostLandipage']);
Route::get('get-introduction', [IntroductionController::class, 'getIntroductionLandipage']);
Route::get('get-instruct', [InstructController::class, 'getInstructLandipage']);
Route::post('create-support', [SupportController::class, 'createSupportLandipage']);


