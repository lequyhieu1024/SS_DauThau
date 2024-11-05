<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectComparisonController extends Controller
{
    protected $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/compare-projects/compare-bar-chart-total-amount",
     *     tags={"Project Comparison"},
     *     summary="Compare projects by total amount",
     *     description="Retrieve bar chart data comparing total amount of projects",
     *          security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="project_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={{}})
     *         )
     *     )
     * )
     */
    public function compareBarChartTotalAmount(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectIds = $request->input('project_ids');
        $data = $this->projectRepository->getBarChartDataTotalAmount($projectIds);

        return response()->json([
            'result' => true,
            'message' => 'Lấy dữ liệu biểu đồ cột so sánh tổng số tiền thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/compare-projects/comparing-construction-time",
     *     tags={"Project Comparison"},
     *     summary="Compare projects by construction time",
     *     description="Retrieve bar chart data comparing construction time of projects",
     *          security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="project_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={{}})
     *         )
     *     )
     * )
     */
    public function compareBarChartConstructionTime(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectIds = $request->input('project_ids');
        $data = $this->projectRepository->getBarChartDataComparingConstructionTime($projectIds);

        return response()->json([
            'result' => true,
            'message' => 'Lấy dữ liệu biểu đồ cột so sánh thời gian thực hiện dự án thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/compare-projects/comparing-did-submission-time",
     *     tags={"Project Comparison"},
     *     summary="Compare projects by bid submission time",
     *     description="Retrieve bar chart data comparing bid submission time of projects",
     *          security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="project_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={{}})
     *         )
     *     )
     * )
     */
    public function compareBarChartBidSubmissionTime(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectIds = $request->input('project_ids');
        $data = $this->projectRepository->getBarChartDataComparingBidSubmissionTime($projectIds);

        return response()->json([
            'result' => true,
            'message' => 'Lấy dữ liệu biểu đồ cột so sánh thời gian mở thầu thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/compare-projects/compare-pie-chart-total-amount",
     *     tags={"Project Comparison"},
     *     summary="Compare projects by total amount (Pie Chart)",
     *     description="Retrieve pie chart data comparing total amount of projects",
     *          security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="project_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={{}})
     *         )
     *     )
     * )
     */
    public function comparePieChartTotalAmount(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectIds = $request->input('project_ids');
        $data = $this->projectRepository->getPieChartDataAmountAndTotalAmount($projectIds);

        return response()->json([
            'result' => true,
            'message' => 'Lấy dữ liệu biểu đồ tròn so sánh tỷ lệ giá trị gói thầu thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/compare-projects/compare-bidder-count",
     *     tags={"Project Comparison"},
     *     summary="Compare projects by bidder count",
     *     description="Retrieve bar chart data comparing bidder count of projects",
     *          security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="project_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="result", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={{}})
     *         )
     *     )
     * )
     */
    public function compareBarChartBidderCount(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectIds = $request->input('project_ids');
        $data = $this->projectRepository->getBarChartDataBidderCount($projectIds);

        return response()->json([
            'result' => true,
            'message' => 'Lấy dữ liệu biểu đồ cột so sánh số lượng doanh nghiệp tham gia đấu thầu thành công',
            'data' => $data,
        ], 200);
    }

    public function getDetailProjectByIds(Request $request)
    {
        $rules = [
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $projectIds = $request->input('project_ids');

        $projects = $this->projectRepository->findWhereIn('id', $projectIds);

        if ($projects->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy dự án nào.',
                'data' => [],
            ], 404);
        }

        return response([
            'result' => true,
            'message' => "Lấy dữ liệu chi tiết của các dự án thành công",
            'data' => ProjectResource::collection($projects),
        ], 200);
    }

}
