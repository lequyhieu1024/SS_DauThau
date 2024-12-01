<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingTypes\BiddingTypeFormRequest;
use App\Http\Requests\BiddingTypes\IndexBiddingTypeRequest;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Resources\BiddingTypeCollection;
use App\Repositories\BiddingTypeRepository;
use Illuminate\Support\Facades\DB;

class BiddingTypeController extends Controller
{
    protected $biddingTypeRepository;

    public function __construct(BiddingTypeRepository $biddingTypeRepository)
    {
        $this->biddingTypeRepository = $biddingTypeRepository;
        $this->middleware(['permission:list_bidding_type'])->only('index');
        $this->middleware(['permission:create_bidding_type'])->only(['store']);
        $this->middleware(['permission:update_bidding_type'])->only(['update', 'toggleActiveStatus']);
        $this->middleware(['permission:detail_bidding_type'])->only('show');
        $this->middleware(['permission:destroy_bidding_type'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/bidding-types",
     *     tags={"Bidding Types"},
     *     summary="Get all bidding types",
     *     description="Get all bidding types",
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
     *         name="name",
     *         in="query",
     *         description="Name of bidding type",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get bidding types successfully",
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
     *                 example="Get bidding types successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="biddingTypes",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="Bidding types 1"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Description of bidding types 1"
     *                         ),
     *                         @OA\Property(
     *                             property="is_active",
     *                             type="boolean",
     *                             example=true
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
    public function index(IndexBiddingTypeRequest $request)
    {
        $biddingTypes = $this->biddingTypeRepository->filter($request->all());

        $data = new BiddingTypeCollection($biddingTypes);

        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách các loại hình đấu thầu thành công',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/bidding-types",
     *     tags={"Bidding Types"},
     *     summary="Create a new bidding type",
     *     description="Create a new bidding type",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "is_active"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Bidding type 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of bidding type 1"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="number",
     *                 example=1
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bidding type created successfully",
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
     *                 example="Bidding type created successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Bidding type 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of bidding type 1"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function store(BiddingTypeFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $biddingType = $this->biddingTypeRepository->createBiddingTpye($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới loại hình đấu thầu thành công.',
                'data' => $biddingType,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo mới loại hình đấu thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/bidding-types/{id}",
     *     tags={"Bidding Types"},
     *     summary="Get bidding type by ID",
     *     description="Get bidding type by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bidding type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bidding type retrieved successfully",
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
     *                 example="Bidding type retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="bidding type 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of bidding type 1"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $biddingType = $this->biddingTypeRepository->findBiddingTypeById($id);

        if (!$biddingType) {
            return response()->json([
                'result' => false,
                'message' => 'Loại hình đấu thầu không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin loại hình đấu thầu thành công.',
            'data' => $biddingType
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/bidding-types/{id}",
     *     tags={"Bidding Types"},
     *     summary="Update bidding type by ID",
     *     description="Update bidding type by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bidding type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Bidding type 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of bidding type 1"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="number",
     *                 example=1
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bidding type updated successfully"
     *     )
     * )
     */
    public function update(BiddingTypeFormRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $biddingType = $this->biddingTypeRepository->updateBiddingTpye($data, $id);

            if (!$biddingType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hình đấu thầu không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật loại hình đấu thầu thành công.',
                'data' => $biddingType,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật loại hình đấu thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/bidding-types/{id}",
     *     tags={"Bidding Types"},
     *     summary="Delete bidding type by ID",
     *     description="Delete bidding type by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bidding type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bidding type deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $biddingType = $this->biddingTypeRepository->deleteBiddingTpye($id);

            if (!$biddingType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hình đấu thầu không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa loại hình đấu thầu thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa loại hình đấu thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/bidding-types/{id}/toggle-status",
     *     tags={"Bidding Types"},
     *     summary="Toggle active status of bidding type by ID",
     *     description="Toggle active status of bidding type by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of bidding type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bidding type status toggled successfully"
     *     )
     * )
     */
    public function toggleActiveStatus(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $biddingType = $this->biddingTypeRepository->findBiddingTypeById($id);

            if (!$biddingType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hình đấu thầu không tồn tại.',
                ], 404);
            }

            $biddingType->is_active = !$biddingType->is_active;
            $biddingType->save();

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật trạng thái loại hình đấu thầu thành công.',
                'data' => [
                    'is_active' => $biddingType->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái loại hình đấu thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
