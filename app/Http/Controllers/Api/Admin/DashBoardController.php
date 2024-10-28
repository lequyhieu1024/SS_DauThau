<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    protected $projectRepository;
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
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
    public function projectByFundingSource() {
        $data = $this->projectRepository->getProjectPercentageByFundingSource();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    // Lấy tỷ lệ dự án trong ngoài nước
    public function projectByIsDomestic() {
        $data = $this->projectRepository->getDomesticPercentage();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectBySubmissionMethod(){
        $data = $this->projectRepository->getProjectPercentageBySubmissionMethod();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectBySelectionMethod(){
        $data = $this->projectRepository->getProjectPercentageBySelectionMethod();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectByTendererAndInvestor(){
        $data = $this->projectRepository->getProjectPercentageByTendererInvestor();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function averageProjectDurationByIndustry(){
        $data = $this->projectRepository->getAverageProjectDurationByIndustry();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }

    public function projectPercentageByOrganizationType(){
        $data = $this->projectRepository->getProjectPercentageByOrganizationType();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }
}
