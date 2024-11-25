<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReputationCollection;
use App\Repositories\ReputationRepository;
use Illuminate\Http\Request;

class ReputationController extends Controller
{
    protected $reputationRepository;

    public function __construct(ReputationRepository $reputationRepository)
    {
        $this->middleware(['permission:list_reputation'])->only(['index']);
        $this->reputationRepository = $reputationRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response([
            'result' => true,
            'message' => "Lấy danh sách đánh giá uy tín doanh nghiệp thành công thầu thành công",
            'data' => new ReputationCollection($this->reputationRepository->filter($request->all()))
        ], 200);
    }
}
