<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Repositories\BidBondRepository;
use App\Repositories\BiddingResultRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\EvaluateRepository;
use App\Repositories\IndustryRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\StaffRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $projectRepository;
    protected $enterpriseRepository;
    protected $industryRepository;
    protected $staffRepository;
    protected $bidBondRepository;
    protected $biddingResultRepository;
    protected $evaluateRepository;



    public function __construct(ProjectRepository $projectRepository, EnterpriseRepository $enterpriseRepository, IndustryRepository $industryRepository, StaffRepository $staffRepository, BidBondRepository $bidBondRepository, 
    BiddingResultRepository $biddingResultRepository, EvaluateRepository $evaluateRepository)
    {
        //        $this->middleware(['permission:dashboard'])->only('projectByIndustry');
        //        $this->middleware(['permission:dashboard'])->only('projectByFundingSource');
        //        $this->middleware(['permission:dashboard'])->only('projectByIsDomestic');
        //        $this->middleware(['permission:dashboard'])->only('projectBySubmissionMethod');
        //        $this->middleware(['permission:dashboard'])->only('projectBySelectionMethod');
        //        $this->middleware(['permission:dashboard'])->only('projectByTendererAndInvestor');
        //        $this->middleware(['permission:dashboard'])->only('averageProjectDurationByIndustry');
        //        $this->middleware(['permission:dashboard'])->only('enterpriseByOrganizationType');
        //        $this->middleware(['permission:dashboard'])->only('topTenderersByProjectCount');
        //        $this->middleware(['permission:dashboard'])->only('topTenderersByProjectTotalAmount');
        //        $this->middleware(['permission:dashboard'])->only('topInvestorsByProjectPartial');
        //        $this->middleware(['permission:dashboard'])->only('topInvestorsByProjectFull');
        //        $this->middleware(['permission:dashboard'])->only('topInvestorsByProjectTotalAmount');
        //        $this->middleware(['permission:dashboard'])->only('topEnterprisesHaveCompletedProjectsByIndustry');
        //        $this->middleware(['permission:dashboard'])->only('topEnterprisesHaveCompletedProjectsByFundingSource');
        //        $this->middleware(['permission:dashboard'])->only('timeJoiningWebsiteOfEnterprise');
        //        $this->middleware(['permission:dashboard'])->only('projectsStatusPerMonth');
        //        $this->middleware(['permission:dashboard'])->only('top10IndustryHasTheMostProject');
        //        $this->middleware(['permission:dashboard'])->only('top10IndustryHasTheMostEnterprise');

        $this->projectRepository = $projectRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->industryRepository = $industryRepository;
        $this->staffRepository = $staffRepository;
        $this->bidBondRepository = $bidBondRepository;
        $this->biddingResultRepository = $biddingResultRepository;
        $this->evaluateRepository = $evaluateRepository;
    }

    // Lấy tỷ lệ dự án theo nghành nghề
    public function projectByIndustry()
    {
        $data = $this->projectRepository->getProjectCountByIndustry();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data,
        ], 200);
    }

    // Lấy tỷ lệ dự án theo nguồn vốn
    public function projectByFundingSource()
    {
        $data = $this->projectRepository->getProjectPercentageByFundingSource();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    // Lấy tỷ lệ dự án trong ngoài nước
    public function projectByIsDomestic()
    {
        $data = $this->projectRepository->getDomesticPercentage();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectBySubmissionMethod()
    {
        $data = $this->projectRepository->getProjectPercentageBySubmissionMethod();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectBySelectionMethod()
    {
        $data = $this->projectRepository->getProjectPercentageBySelectionMethod();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectByTendererAndInvestor()
    {
        $data = $this->projectRepository->getProjectPercentageByTendererInvestor();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function averageProjectDurationByIndustry()
    {
        $data = $this->projectRepository->getAverageProjectDurationByIndustry();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function enterpriseByOrganizationType()
    {
        $data = $this->projectRepository->getEnterpriseByOrganizationType();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function topTenderersByProjectCount()
    {
        $data = $this->projectRepository->getTopTenderersByProjectCount();
        return response()->json([
            'result' => true,
            'message' => '10 đơn vị mời thầu có tổng gói thầu nhiều nhất theo số lượng',
            'data' =>  $data
        ], 200);
    }

    public function topTenderersByProjectTotalAmount()
    {
        $data = $this->projectRepository->getTopTenderersByProjectTotalAmount();
        return response()->json([
            'result' => true,
            'message' => '10 đơn vị mời thầu có tổng gói thầu nhiều nhất theo giá',
            'data' =>  $data
        ], 200);
    }

    public function topInvestorsByProjectPartial()
    {
        $data = $this->projectRepository->getTopInvestorsByProjectPartial();
        return response()->json([
            'result' => true,
            'message' => '10 đơn vị trúng thầu nhiều nhất theo từng phần',
            'data' =>  $data
        ], 200);
    }

    public function topInvestorsByProjectFull()
    {
        $data = $this->projectRepository->getTopInvestorsByProjectFull();
        return response()->json([
            'result' => true,
            'message' => '10 đơn vị trúng thầu nhiều nhất theo trọn gói',
            'data' =>  $data
        ], 200);
    }

    public function topInvestorsByProjectTotalAmount()
    {
        $data = $this->projectRepository->getTopInvestorsByProjectTotalAmount();
        return response()->json([
            'result' => true,
            'message' => '10 đơn vị trúng thầu nhiều nhất theo giá',
            'data' =>  $data
        ], 200);
    }

    public function topEnterprisesHaveCompletedProjectsByIndustry(Request $request)
    {
        return $this->enterpriseRepository->topEnterprisesHaveCompletedProjectsByIndustry($request->id);
    }

    public function topEnterprisesHaveCompletedProjectsByFundingSource(Request $request)
    {
        return $this->enterpriseRepository->topEnterprisesHaveCompletedProjectsByFundingSource($request->id);
    }

    public function timeJoiningWebsiteOfEnterprise(Request $request)
    {
        $year = $request->input('year') ?? Carbon::now()->year;
        return response()->json([
            'result' => true,
            'message' => "Biểu đồ thể hiện số lượng doanh nghiệp tham gia hệ thống theo từng tháng trong năm $year",
            'data' =>  $this->enterpriseRepository->timeJoiningWebsite($year)
        ], 200);
    }

    public function projectsStatusPerMonth(Request $request)
    {
        $year = $request->input('year') ?? Carbon::now()->year;
        return response()->json([
            'result' => true,
            'message' => "Biểu đồ thể hiện số lượng dự án hoàn thành, số lượng dự án được phê duyệt, số lượng dự án mở thầu theo từng tháng trong năm $year",
            'data' =>  $this->projectRepository->projectsStatusPerMonth($year)
        ], 200);
    }

    public function top10IndustryHasTheMostProject()
    {
        return response()->json([
            'result' => true,
            'message' => "Biểu đồ thể hiện số lượng dự án theo ngành nghề",
            'data' =>  $this->industryRepository->top10IndustryHasTheMostProjects($this->industryRepository->getNameAndIdsActive())
        ], 200);
    }

    public function top10IndustryHasTheMostEnterprise()
    {
        return response()->json([
            'result' => true,
            'message' => "Biểu đồ thể hiện số lượng doanh nghiệp theo ngành nghề",
            'data' =>  $this->industryRepository->top10IndustryHasTheMostEnterprises($this->industryRepository->getNameAndIdsActive())
        ], 200);
    }

    public function countProjects()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->projectRepository->countProjects()
        ], 200);
    }

    public function countEnterprises()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->enterpriseRepository->countEnterprises()
        ], 200);
    }

    public function countIndustries()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->industryRepository->countIndustries()
        ], 200);
    }

    public function countStaff()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->staffRepository->countStaff()
        ], 200);
    }

    public function countBidBond()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->bidBondRepository->countBidBond()
        ], 200);
    }

    public function countBiddingResult()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->biddingResultRepository->countBiddingResult()
        ], 200);
    }

    public function countEvaluate()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy thành công",
            'data' => $this->evaluateRepository->countEvaluate()
        ], 200);
    }
}
