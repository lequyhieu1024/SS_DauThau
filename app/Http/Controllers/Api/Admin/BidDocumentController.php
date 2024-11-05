<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\BidDocumentStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BidDocumentFormRequest;
use App\Http\Resources\BidDocument\BidDocumentCollection;
use App\Http\Resources\BidDocument\BidDocumentResource;
use App\Repositories\BidDocumentRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class BidDocumentController extends Controller
{

    protected BidDocumentRepository $bidDocumentRepository;
    protected ProjectRepository $projectRepository;

    public function __construct(BidDocumentRepository $bidDocumentRepository, ProjectRepository $projectRepository)
    {
//        $this->middleware(['permission:list_bid_document'])->only('index');
//        $this->middleware(['permission:create_bid_document'])->only(['store']);
//        $this->middleware(['permission:update_bid_document_status'])->only(['approveBidDocument']);
//        $this->middleware(['permission:detail_bid_document'])->only('show');
//        $this->middleware(['permission:destroy_bid_document'])->only('destroy');
        $this->bidDocumentRepository = $bidDocumentRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/bid-documents",
     *     summary="Get list of bid documents",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
     *          @OA\Parameter(
     *          name="size",
     *          in="query",
     *          description="Number of records per page",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=10
     *          )
     *      ),
     *       @OA\Parameter(
     *       name="page",
     *       in="query",
     *       description="Page number",
     *       required=false,
     *       @OA\Schema(
     *       type="integer",
     *       default=1
     *       )
     *     ),
     *      @OA\Parameter(
     *          name="project_id",
     *          in="query",
     *          description="Filter by project id",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="enterprise_id",
     *          in="query",
     *          description="Filter by enterprise id",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by status",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *     enum={1, 2, 3, 4}
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="start_date",
     *          in="query",
     *          description="Filter by start date (e.g., 2023-01-01)",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="end_date",
     *          in="query",
     *          description="Filter by end date (e.g., 2023-12-31)",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy danh sách hồ sơ dự thầu thành công"),
     *             @OA\Property(property="data", type="object", example="{}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không tìm thấy hồ sơ dự thầu")
     *         )
     *     )
     * )
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
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


    /**
     * @OA\Get(
     *     path="/api/admin/bid-documents/check-bid-participation/{projectId}",
     *     summary="Check bid participation",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="projectId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Doanh nghiệp đã tham gia đấu thầu dự án này."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function checkBidParticipation($projectId): \Illuminate\Http\JsonResponse
    {
        $user = JWTAuth::user();
        $enterpriseId = $user->enterprise->id ?? null;

        if ($enterpriseId) {
            $bidDocument = $this->bidDocumentRepository->findByProjectAndEnterprise($projectId, $enterpriseId);

            if ($bidDocument) {
                return response()->json([
                    'result' => true,
                    'message' => 'Doanh nghiệp đã tham gia đấu thầu dự án này.',
                    'data' => [
                        'can_create' => false,
                        'bid_document' => [
                            'id' => $bidDocument->id,
                            'project' => [
                                'id' => $bidDocument->project->id,
                                'name' => $bidDocument->project->name,
                            ],
                            'status' => [
                                'id' => $bidDocument->status,
                                'label' => BidDocumentStatus::from($bidDocument->status)->label()
                            ],
                        ],

                    ]
                ], 200);
            }

            return response()->json([
                'result' => true,
                'message' => 'Doanh nghiệp có thể tham gia đấu thầu dự án này.',
                'data' => ['can_create' => true]
            ], 200);
        }


        return response()->json([
            'result' => true,
            'message' => 'Người dùng có thể tạo mới hồ sơ dự thầu.',
            'data' => ['can_create' => true]
        ], 200);


    }


    /**
     * @OA\Post(
     *     path="/api/admin/bid-documents",
     *     summary="Create a new bid document",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
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
     * @OA\Get(
     *     path="/api/admin/bid-documents/{id}",
     *     summary="Get bid document details",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy thông tin hồ sơ dự thầu thành công"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không tìm thấy hồ sơ dự thầu này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi lấy thông tin hồ sơ dự thầu"),
     *             @OA\Property(property="errors", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $bidDocument = $this->bidDocumentRepository->findOrFail($id);
            return response()->json([
                'result' => true,
                'message' => 'Lấy thông tin hồ sơ dự thầu thành công',
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
                'message' => 'Lỗi khi lấy thông tin hồ sơ dự thầu',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/bid-documents/{id}",
     *     summary="Delete a bid document",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Xóa hồ sơ dự thầu thành công")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không tìm thấy hồ sơ dự thầu này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi xóa hồ sơ dự thầu"),
     *             @OA\Property(property="errors", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $bidDocument = $this->bidDocumentRepository->findOrFail($id);
            $bidDocument->delete();
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa hồ sơ dự thầu thành công',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hồ sơ dự thầu này'
            ], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa hồ sơ dự thầu',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch (
     *     path="/api/admin/bid-documents/{id}/approve",
     *     summary="Approve a bid document",
     *     tags={"Bid Documents"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 enum={2, 3, 4},
     *                 example=2
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cập nhật trạng thái hồ sơ dự thầu thành công"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi cập nhật trạng thái hồ sơ dự thầu"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bạn không có quyền cập nhật trạng thái hồ sơ dự thầu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không tìm thấy hồ sơ dự thầu này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi cập nhật trạng thái hồ sơ dự thầu"),
     *             @OA\Property(property="errors", type="string", example="Error message")
     *         )
     *     )
     * )
     */
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

            if (!JWTAuth::user()->can('update_bid_document_status')) {
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
