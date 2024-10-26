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
        $data = $this->projectRepository->getProjectPercentageByIndustry();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data,
        ], 200);
    }

    public function projectByFundingSource() {
        $data = $this->projectRepository->getProjectPercentageByFundingSource();
        return response()->json([
            'result' => true,
            'message' => 'Lấy thành công',
            'data' =>  $data
        ], 200);
    }
}
