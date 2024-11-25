<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackComplaintRequest;
use App\Http\Resources\FeedbackComplaintCollection;
use App\Http\Resources\FeedbackComplaintResource;
use App\Repositories\FeedbackComplaintRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackComplaintController extends Controller
{
    protected $feedbackComplaintRepository;
    public function __construct(FeedbackComplaintRepository $feedbackComplaintRepository)
    {
        $this->middleware(['permission:list_feedback_complaint'])->only(['index']);
        $this->middleware(['permission:create_feedback_complaint'])->only('store');
        $this->middleware(['permission:update_feedback_complaint'])->only(['update']);
        $this->middleware(['permission:detail_feedback_complaint'])->only('show');
        $this->middleware(['permission:destroy_feedback_complaint'])->only('destroy');
        $this->feedbackComplaintRepository = $feedbackComplaintRepository;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/feedback-complaints",
     *     tags={"Feedback complaints"},
     *     summary="Get all Feedback complaints",
     *     description="Get all Feedback complaints",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="size",
     *         in="query",
     *         description="Size items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         description="Content",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Feedback complaints successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Get Feedback complaints successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="feedbackComplaints",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="project_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="content",
     *                             type="string",
     *                             example="content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="response_content",
     *                             type="string",
     *                             example="response_content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="complaint_by",
     *                             type="integer",
     *                             example=2
     *                         ),
     *                         @OA\Property(
     *                             property="responded_by",
     *                             type="integer",
     *                             example=3
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="string",
     *                             example="pending"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(
     *                         property="currentPage",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="pageSize",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="totalItems",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="totalPages",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="hasNextPage",
     *                         type="boolean",
     *                         example=false
     *                     ),
     *                     @OA\Property(
     *                         property="hasPreviousPage",
     *                         type="boolean",
     *                         example=false
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $feedbackComplaints = $this->feedbackComplaintRepository->filter($request->all());
        $data = new FeedbackComplaintCollection($feedbackComplaints);
        return response()->json([
            'result' => true,
            'message' => 'Danh sách phản hồi khiếu nại.',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/feedback-complaints",
     *     tags={"Feedback complaints"},
     *     summary="Create a new Feedback complaint",
     *     description="Create a new Feedback complaint",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 example="Feedback complaint 1"
     *             ),
     *             @OA\Property(
     *                 property="project_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Feedback complaint created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Feedback complaint created successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="project_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="content",
     *                             type="string",
     *                             example="content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="response_content",
     *                             type="string",
     *                             example="response_content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="complaint_by",
     *                             type="integer",
     *                             example=2
     *                         ),
     *                         @OA\Property(
     *                             property="responded_by",
     *                             type="integer",
     *                             example=3
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="string",
     *                             example="pending"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *             )
     *         )
     *     )
     * )
     */
    public function store(FeedbackComplaintRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $feedbackComplaint = $this->feedbackComplaintRepository->create($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới phản hồi khiếu nại.',
                'data' => $feedbackComplaint,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo phản hồi khiếu nại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/feedback-complaints/{id}",
     *     tags={"Feedback complaints"},
     *     summary="Get Feedback complaint by ID",
     *     description="Get Feedback complaint by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Feedback complaint",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback complaint retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Feedback complaint retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="project_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="content",
     *                             type="string",
     *                             example="content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="response_content",
     *                             type="string",
     *                             example="response_content 1"
     *                         ),
     *                         @OA\Property(
     *                             property="complaint_by",
     *                             type="integer",
     *                             example=2
     *                         ),
     *                         @OA\Property(
     *                             property="responded_by",
     *                             type="integer",
     *                             example=3
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="string",
     *                             example="pending"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $feedbackComplaint = $this->feedbackComplaintRepository->find($id);
        $data = new FeedbackComplaintResource($feedbackComplaint);

        if (!$feedbackComplaint) {
            return response()->json([
                'result' => false,
                'message' => 'Phản hồi khiếu nại không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin phản hồi khiếu nại thành công.',
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/feedback-complaints/{id}",
     *     tags={"Feedback complaints"},
     *     summary="Update Feedback complaint by ID",
     *     description="Update Feedback complaint by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Feedback complaint",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response_content",
     *                 type="string",
     *                 example="response content 1"
     *             ),
     *             @OA\Property(
     *                 property="project_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback complaint updated successfully"
     *     )
     * )
     */
    public function update(FeedbackComplaintRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->feedbackComplaintRepository->update($data, $id);

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật nguồn tài trợ thành công.',
                'data' => new FeedbackComplaintResource($this->feedbackComplaintRepository->findOrFail($id)),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật nguồn tài trợ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/feedback-complaints/{id}",
     *     tags={"Feedback complaints"},
     *     summary="Delete Feedback complaint by ID",
     *     description="Delete Feedback complaint by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Feedback complaint",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback complaint deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $feedbackComplaint = $this->feedbackComplaintRepository->delete($id);

            if (!$feedbackComplaint) {
                return response()->json([
                    'result' => false,
                    'message' => 'Phản hồi khiếu nại không tồn tại.',
                ], 404);
            }
            return response()->json([
                'result' => true,
                'message' => 'Xóa phản hồi khiếu nại thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa phản hồi khiếu nại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
