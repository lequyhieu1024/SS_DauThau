<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingResultFormRequest;
use App\Http\Resources\BiddingResultCollection;
use App\Http\Resources\BiddingResultResource;
use App\Repositories\BiddingResultRepository;
use App\Repositories\BidDocumentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiddingResultController extends Controller
{
    protected $biddingResultRepository;
    protected $bidDocumentRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(BiddingResultRepository $biddingResultRepository, ProjectRepository $projectRepository, UserRepository $userRepository, BidDocumentRepository $bidDocumentRepository)
    {

        $this->middleware(['permission:list_bidding_result'])->only('index');
        $this->middleware(['permission:create_bidding_result'])->only('store');
        $this->middleware(['permission:update_bidding_result'])->only('update');
        $this->middleware(['permission:detail_bidding_result'])->only('show');


        // permission here
        $this->biddingResultRepository = $biddingResultRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->bidDocumentRepository = $bidDocumentRepository;
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
            $data = $request->all();
            $bidding_document = $this->bidDocumentRepository->findOrFail($data['bid_document_id']);
            $data['project_id'] = $bidding_document['project_id'];
            $data['enterprise_id'] = $bidding_document['enterprise_id'];
            $this->biddingResultRepository->create($data);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Lưu dữ liệu kết quả đấu thầu thành công.',
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
        return response([
            'result' => true,
            'message' => 'Lấy thông tin kết quả đấu thầu thành công',
            'data' => new BiddingResultResource($this->biddingResultRepository->findOrFail($id))
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BiddingResultFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $bidding_document = $this->bidDocumentRepository->findOrFail($data['bid_document_id']);
            $data['project_id'] = $bidding_document['project_id'];
            $data['enterprise_id'] = $bidding_document['enterprise_id'];
            $this->biddingResultRepository->update($data, $id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Cập nhật dữ liệu kết quả đấu thầu thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
