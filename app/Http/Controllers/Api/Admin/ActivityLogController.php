<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLog\ActivityLogCollection;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Repositories\ActivityLogRepository;

class ActivityLogController extends Controller
{
    protected $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->middleware(['permission:list_activity_log'])->only('index');
        $this->middleware(['permission:detail_activity_log'])->only('show');


        $this->activityLogRepository = $activityLogRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/activity-logs",
     *     tags={"Activity Log"},
     *     summary="Get a list of activity logs",
     *     description="Retrieve a list of activity logs with optional filters",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="size",
     *         in="query",
     *         description="Number of records per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *      @OA\Parameter(
     *      name="page",
     *      in="query",
     *      description="Page number",
     *      required=false,
     *      @OA\Schema(
     *      type="integer",
     *      default=1
     *      )
     *    ),
     *     @OA\Parameter(
     *         name="log_name",
     *         in="query",
     *         description="Filter by log name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="event",
     *         in="query",
     *         description="Filter by event",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"Tạo mới", "Cập nhật", "Xóa"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="action_performer",
     *         in="query",
     *         description="Filter by action performer",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Filter by description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by start date (e.g., 2023-01-01)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by end date (e.g., 2023-12-31)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of activity logs retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *     example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No activity logs found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {

        $data = $this->activityLogRepository->filter(request()->all());

        $result = new ActivityLogCollection($data);

        if ($result->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hoạt động',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách hoạt động thành công',
            'data' => $result,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/activity-logs/{id}",
     *     tags={"Activity Log"},
     *     summary="Get activity log by ID",
     *     description="Retrieve a specific activity log by its ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the activity log",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="ip",
     *         in="query",
     *         description="IP address to filter the activity log",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity log retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *     property="data",
     *     type="object",
     *     example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Activity log not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $activityLog = $this->activityLogRepository->find($id);

        $result = new ActivityLogResource($activityLog);
        if (!$activityLog) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy hoạt động',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy chi tiết hoạt động thành công',
            'data' => $result,
        ], 200);
    }

}
