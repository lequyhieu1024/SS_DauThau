<?php

namespace App\Http\Controllers\Api;

use App\Enums\BidDocumentStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BidDocumentFormRequest;
use App\Http\Resources\BidDocument\BidDocumentCollection;
use App\Http\Resources\BidDocument\BidDocumentResource;
use App\Repositories\BidDocumentRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class BidDocumentController extends Controller
{

    protected $bidDocumentRepository;
    protected $projectRepository;

    public function __construct(BidDocumentRepository $bidDocumentRepository, ProjectRepository $projectRepository)
    {
//        $this->middleware(['permission:list_bid_document'])->only('index');
//        $this->middleware(['permission:create_bid_document'])->only(['store']);
//        $this->middleware(['permission:update_bid_document'])->only(['update']);
//        $this->middleware(['permission:detail_bid_document'])->only('show');
//        $this->middleware(['permission:destroy_bid_document'])->only('destroy');
        $this->bidDocumentRepository = $bidDocumentRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bidDocuments = $this->bidDocumentRepository->filter($request->all());
        $data = new BidDocumentCollection($bidDocuments);

        if ($bidDocuments->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hồ sơ dự thầu',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách hồ sơ dự thầu thành công',
            'data' => $data,
        ], 200);
    }

    public function checkBidParticipation($projectId): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $enterpriseId = $user->enterprise->id ?? null;

        if ($enterpriseId) {
            $bidDocument = $this->bidDocumentRepository->findByProjectAndEnterprise($projectId, $enterpriseId);

            if ($bidDocument) {
                return response()->json([
                    'result' => true,
                    'message' => 'Doanh nghiệp đã tham gia đấu thầu dự án này.',
                    'data' => [
                        'can_create' => false,
                        'bid_document_status' => BidDocumentStatus::from($bidDocument->status)->label()
                    ]
                ], 200);
            }

            return response()->json([
                'result' => true,
                'message' => 'Doanh nghiệp có thể tham gia đấu thầu dự án này.',
                'data' => ['can_create' => true]
            ], 200);
        }

        if ($user->can('create_bid_document')) {
            return response()->json([
                'result' => true,
                'message' => 'Người dùng có thể tạo mới hồ sơ dự thầu.',
                'data' => ['can_create' => true]
            ], 200);
        }

        return response()->json([
            'result' => false,
            'message' => 'Người dùng không có quyền tạo hồ sơ dự thầu.',
            'data' => ['can_create' => false]
        ], 403);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/bid-documents",
     *     summary="Create a new bid document",
     *     tags={"Bid Documents"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="project_id", type="integer", example=14),
     *             @OA\Property(property="bid_bond_id", type="integer", example=1),
     *             @OA\Property(property="bid_price", type="integer", example=111111.00),
     *             @OA\Property(property="implementation_time", type="string", format="date-time", example="2024-12-31 00:00:00"),
     *             @OA\Property(property="validity_period", type="string", nullable=true, example=null),
     *             @OA\Property(property="note", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bid document created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tạo hồ sơ dự thầu thành công"),
     *             @OA\Property(property="data", type="string", example="{}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dự án không ở trạng thái cho phép gửi hồ sơ dự thầu.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi tạo hồ sơ dự thầu"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function store(BidDocumentFormRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        $data['submission_date'] = now();


        $project = $this->projectRepository->find($data['project_id']);

        if ($project->status != ProjectStatus::RECEIVED->value) {
            return response()->json([
                'result' => false,
                'message' => 'Dự án không ở trạng thái cho phép gửi hồ sơ dự thầu.',
            ], 400);
        }

        if ($data['submission_date'] < $project->bid_submission_start) {
            return response()->json([
                'result' => false,
                'message' => "Dự án sẽ nhận hồ sơ từ ngày {$project->bid_submission_start} đến ngày {$project->bid_submission_end}.",

            ], 400);
        }

        if ($data['submission_date'] > $project->bid_submission_end) {
            return response()->json([
                'result' => false,
                'message' => "Dự án đã dừng nhận hồ sơ từ ngày {$project->bid_submission_end}.",
            ], 400);
        }

        // Check if a bid document already exists for the given project_id and enterprise_id
        if ($this->bidDocumentRepository->findByProjectAndEnterprise($data['project_id'], $data['enterprise_id'])) {
            return response()->json([
                'result' => false,
                'message' => 'Bạn đã tham gia đấu thầu dự án này rồi, vui lòng cập hồ sơ dự thầu.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $bidDocument = $this->bidDocumentRepository->create($data);
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Tạo hồ sơ dự thầu thành công',
                'data' => $bidDocument,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo hồ sơ dự thầu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $bidDocument = $this->bidDocumentRepository->findOrFail($id);
            return response()->json([
                'result' => true,
                'message' => 'Lấy thông tin hồ sơ dự thầu thành công',
                'data' => new BidDocumentResource($bidDocument)
//                'data' => $bidDocument
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hồ sơ dự thầu này'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi lấy thông tin hồ sơ dự thầu',
                'errors' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BidDocumentFormRequest $request, string $id)
    {
//        try {
//            $bidDocument = $this->bidDocumentRepository->findOrFail($id);
//
//            $data = $request->only(['bid_price', 'implementation_time', 'note']);
//
//            // Kiểm tra vai trò của người dùng
//            if (auth()->user()->hasRole(['admin', 'staff', 'chuyenvien'])) {
//                $data['status'] = $request->input('status');
//            }
//
//            $bidDocument->update($data);
//
//            return response()->json([
//                'result' => true,
//                'message' => 'Cập nhật hồ sơ dự thầu thành công',
//                'data' => new BidDocumentResource($bidDocument)
//            ], 200);
//        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
//            return response()->json([
//                'result' => false,
//                'message' => 'Không tìm thấy hồ sơ dự thầu này'
//            ], 404);
//        } catch (\Throwable $e) {
//            return response()->json([
//                'result' => false,
//                'message' => 'Lỗi khi cập nhật hồ sơ dự thầu',
//                'errors' => $e->getMessage()
//            ], 500);
//        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approveBidDocument(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'status' => 'required|in:2,3,4',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái hồ sơ dự thầu',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $bidDocument = $this->bidDocumentRepository->findOrFail($id);

            if (!auth()->user()->can('update_bid_document_status')) {
                return response()->json([
                    'result' => false,
                    'message' => 'Bạn không có quyền cập nhật trạng thái hồ sơ dự thầu',
                ], 403);
            }

            $bidDocument->update(['status' => $request->input('status')]);

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật trạng thái hồ sơ dự thầu thành công',
                'data' => new BidDocumentResource($bidDocument)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hồ sơ dự thầu này'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái hồ sơ dự thầu',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
