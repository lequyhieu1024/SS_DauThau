<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BidBondFormRequest;
use App\Http\Resources\BidBondCollection;
use App\Http\Resources\BidBondResource;
use App\Repositories\BidBondRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rmunate\Utilities\SpellNumber;

class BidBondController extends Controller
{
    protected $bidBondRepository;
    protected $projectRepository;
    public function __construct(BidBondRepository $bidBondRepository, ProjectRepository $projectRepository)
    {
        $this->bidBondRepository = $bidBondRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/bid-bonds",
     *     tags={"Bid Bonds"},
     *     summary="Get all Bid Bonds",
     *     description="Get all Bid Bonds",
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
     *         name="bond_number",
     *         in="query",
     *         description="bond_number",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Bid Bonds successfully",
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
     *                 example="Get Bid Bonds successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="bidBonds",
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
     *                             property="enterprise_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="bond_number",
     *                             type="string",
     *                             example="bd1"
     *                         ),
     *                         @OA\Property(
     *                             property="bond_amount",
     *                             type="integer",
     *                             example=1000000
     *                         ),
     *                          @OA\Property(
     *                             property="bond_amount_in_words",
     *                             type="string",
     *                             example="một triệu"
     *                         ),
     *                         @OA\Property(
     *                             property="bond_type",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="issue_date",
     *                             type="string",
     *                             example="2021-09-01"
     *                         ),
     *                         @OA\Property(
     *                             property="expiry_date",
     *                             type="string",
     *                             example="2021-09-02"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="description"
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
        $bidBonds = $this->bidBondRepository->filter($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách bảo lãnh dự thầu thành công.',
            'data' =>  new BidBondCollection($bidBonds)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/bid-bonds",
     *     tags={"Bid Bonds"},
     *     summary="Create a new bid bond",
     *     description="Create a new bid bond",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"project_id", "enterprise_id", "bond_number", "bond_amount", "bond_type", "expiry_date"},
     *             @OA\Property(
     *                 property="project_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="enterprise_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="bond_number",
     *                 type="string",
     *                 example="bb1"
     *             ),
     *             @OA\Property(
     *                 property="bond_amount",
     *                 type="number",
     *                 format="float",
     *                 example=1200000.00
     *             ),
     *             @OA\Property(
     *                 property="bond_type",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="expiry_date",
     *                 type="string",
     *                 format="date",
     *                 example="2024-09-21"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="abc"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bid bond created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="bidBonds",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="project_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="enterprise_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="bond_number",
     *                         type="string",
     *                         example="bd1"
     *                     ),
     *                     @OA\Property(
     *                         property="bond_amount",
     *                         type="number",
     *                         format="float",
     *                         example=1000000.00
     *                     ),
     *                     @OA\Property(
     *                         property="bond_amount_in_words",
     *                         type="string",
     *                         example="một triệu"
     *                     ),
     *                     @OA\Property(
     *                         property="bond_type",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="issue_date",
     *                         type="string",
     *                         format="date",
     *                         example="2021-09-01"
     *                     ),
     *                     @OA\Property(
     *                         property="expiry_date",
     *                         type="string",
     *                         format="date",
     *                         example="2021-09-02"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="description"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2021-09-01T00:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2021-09-01T00:00:00.000000Z"
     *                     ),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(BidBondFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $issueDate = $this->projectRepository->find($request->project_id)->submission_deadline;
            $expiryValidation = $this->validateExpiryDate($request->expiry_date, $issueDate);
            if (!$expiryValidation['result']) {
                return response()->json($expiryValidation, 422);
            }

            $data['bond_amount_in_words'] = SpellNumber::value($request->bond_amount)->toLetters();
            $data['issue_date'] = $issueDate;
            $bidBond = $this->bidBondRepository->create($data);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo bảo lãnh dự thầu thành công.",
                "data" => $bidBond
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Tạo bảo lãnh dự thầu không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/bid-bonds/{id}",
     *     tags={"Bid Bonds"},
     *     summary="Get bid bond by ID",
     *     description="Get bid bond by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bid bond",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="bid bond retrieved successfully",
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
     *                 example="bid bond retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="project_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="enterprise_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="bond_number",
     *                         type="string",
     *                         example="bd1"
     *                     ),
     *                     @OA\Property(
     *                         property="bond_amount",
     *                         type="number",
     *                         format="float",
     *                         example=1000000.00
     *                     ),
     *                     @OA\Property(
     *                         property="bond_amount_in_words",
     *                         type="string",
     *                         example="một triệu"
     *                     ),
     *                     @OA\Property(
     *                         property="bond_type",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="issue_date",
     *                         type="string",
     *                         format="date",
     *                         example="2021-09-01"
     *                     ),
     *                     @OA\Property(
     *                         property="expiry_date",
     *                         type="string",
     *                         format="date",
     *                         example="2021-09-02"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="description"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2021-09-01T00:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2021-09-01T00:00:00.000000Z"
     *                     ),
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $bidBond = $this->bidBondRepository->find($id);

        if (!$bidBond) {
            return response()->json([
                'result' => false,
                'message' => 'Bảo lãnh dự thầu không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin bảo lãnh dự thầu thành công.',
            'data' => new BidBondResource($bidBond)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/bid-bonds/{id}",
     *     tags={"Bid Bonds"},
     *     summary="Update bid bond by ID",
     *     description="Update bid bond by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bid bond",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="project_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="enterprise_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="bond_number",
     *                 type="string",
     *                 example="bb1"
     *             ),
     *             @OA\Property(
     *                 property="bond_amount",
     *                 type="number",
     *                 format="float",
     *                 example=1200000.00
     *             ),
     *             @OA\Property(
     *                 property="bond_type",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="expiry_date",
     *                 type="string",
     *                 format="date",
     *                 example="2024-09-21"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="abc"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="bid bond updated successfully"
     *     )
     * )
     */
    public function update(BidBondFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $issueDate = $this->projectRepository->find($request->project_id)->submission_deadline;
            $expiryValidation = $this->validateExpiryDate($request->expiry_date, $issueDate);
            if (!$expiryValidation['result']) {
                return response()->json($expiryValidation, 422);
            }

            $data['bond_amount_in_words'] = SpellNumber::value($request->bond_amount)->toLetters();
            $data['issue_date'] = $issueDate;
            $bidBond = $this->bidBondRepository->update($data, $id);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật bảo lãnh dự thầu thành công",
                "data" => $bidBond
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Cập nhật bảo lãnh dự thầu không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/bid-bonds/{id}",
     *     tags={"Bid Bonds"},
     *     summary="Delete bid bond by ID",
     *     description="Delete bid bond by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bid bond",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="bid bond deleted successfully"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->bidBondRepository->delete($id);

            return response()->json([
                'result' => true,
                'message' => 'Xóa bảo lãnh dự thầu thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa bảo lãnh dự thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function validateExpiryDate($expiryDate, $submissionDeadline)
    {
        if (strtotime($expiryDate) <= strtotime($submissionDeadline)) {
            return [
                'result' => false,
                'message' => "Ngày hết hạn phải lớn hơn ngày phát hành."
            ];
        }
        return ['result' => true];
    }
}
