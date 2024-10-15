<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingResultFormRequest;
use App\Http\Resources\BiddingResultCollection;
use App\Repositories\BiddingResultRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiddingResultController extends Controller
{
    protected $biddingResultRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(BiddingResultRepository $biddingResultRepository, ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        // permission here
        $this->biddingResultRepository = $biddingResultRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $data['enterprise_id'] = $this->userRepository->getEnterpriseId(Auth::user()->id);
        return response([
            'result' => true,
            'message' => 'Lấy danh sách kết quả đấu thầu thành công',
            'data' => new BiddingResultCollection($this->biddingResultRepository->filter($data))
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BiddingResultFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->biddingResultRepository->create($request->all());
            $this->projectRepository->publishResultProject($request->all()['project_id']);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Lựa chọn nhà thầu thành công.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
