<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Repositories\EnterpriseRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    protected $projectRepository;
    protected $enterpriseRepository;
    public function __construct(ProjectRepository $projectRepository, EnterpriseRepository $enterpriseRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->enterpriseRepository = $enterpriseRepository;
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
}
