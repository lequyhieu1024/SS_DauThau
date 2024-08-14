<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Requests\FundingSources\IndexFundingSourceRequest;
use App\Http\Requests\FundingSources\StoreFundingSourceRequest;
use App\Http\Requests\FundingSources\UpdateFundingSourceRequest;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FundingSourcesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/funding-sources",
     *     tags={"Funding Sources"},
     *     summary="Get all funding sources",
     *     description="Get all funding sources",
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
     *         description="Name of funding source",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="Code of funding source",
     *         required=false,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Get funding sources successfully",
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
     *                 example="Get funding sources successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="fundingSources",
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
     *                             example="funding source 1"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Description of funding source 1"
     *                         ),
     *                         @OA\Property(
     *                             property="code",
     *                             type="string",
     *                             example="BF1"
     *                         ),
     *                         @OA\Property(
     *                             property="is_active",
     *                             type="boolean",
     *                             example=true
     *                         ),
     *                         @OA\Property(
     *                             property="type",
     *                             type="string",
     *                             enum={"Chính phủ", "Tư nhân", "Quốc tế"}, 
     *                             example="Tư nhân"
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
    public function index(IndexFundingSourceRequest $request)
    {
        $query = FundingSource::getFilteredFundingSources($request->all());

        $size = $request->query('size', 10);
        $page = $request->query('page', 1);

        $fundingSources = $query->paginate($size, ['*'], 'page', $page);

        $transformedFundingSources = $fundingSources->map(function ($fundingSource) {
            return [
                'id' => $fundingSource->id,
                'name' => $fundingSource->name,
                'description' => $fundingSource->description,
                'code' => $fundingSource->code,
                'type' => $fundingSource->type,
                'is_active' => $fundingSource->is_active,
                'created_at' => $fundingSource->created_at,
                'updated_at' => $fundingSource->updated_at,
            ];
        });
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách các nguồn tài trợ thành công.',
            'data' => [
                'data' => $transformedFundingSources,
                'total_elements' => $fundingSources->total(),
                'total_pages' => $fundingSources->lastPage(),
                'page_size' => $fundingSources->perPage(),
                // 'number_of_elements' => $fundingSources->count(),
                'current_page' => $fundingSources->currentPage(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/funding-sources",
     *     tags={"Funding Sources"},
     *     summary="Create a new funding sources",
     *     description="Create a new funding sources",
     *     security={{"bearerAuth": {}}},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "code", "type", "is_active"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Funding sources 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of funding sources 1"
     *             ),
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="Vn123"
     *             ),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"Chính phủ", "Tư nhân", "Quốc tế"}, 
     *                 example="Tư nhân"
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
     *         description="Funding sources created successfully",
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
     *                 example="Funding sources created successfully"
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
     *                     example="Funding sources 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of funding sources 1"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string",
     *                     example="Vn123"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     enum={"Chính phủ", "Tư nhân", "Quốc tế"}, 
     *                     example="Tư nhân"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="number",
     *                     example=1
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
    public function store(StoreFundingSourceRequest $request)
    {
        DB::beginTransaction();

        try {
            $fundingSource = FundingSource::createFundingSource($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới nguồn tài trợ thành công.',
                'data' => $fundingSource,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo mới nguồn tài trợ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/funding-sources/{id}",
     *     tags={"Funding Sources"},
     *     summary="Get funding sources by ID",
     *     description="Get funding sources by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of funding sources",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Funding Sources retrieved successfully",
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
     *                 example="Funding Sources retrieved successfully"
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
     *                     example="Funding Sources 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of Funding Sources 1"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string",
     *                     example="Vn123"
     *                 ),
     *                     @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     enum={"Chính phủ", "Tư nhân", "Quốc tế"}, 
     *                     example="Tư nhân"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="number",
     *                     example=1
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
    public function show(string $id)
    {
        $fundingSource = FundingSource::findFundingSourceById($id);

        if (!$fundingSource) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy nguồn tài trợ',
            ], 404);
        };

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin nguồn tài trợ thành công.',
            'data' => $fundingSource,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/funding-sources/{id}",
     *     tags={"Funding Sources"},
     *     summary="Update funding sources by ID",
     *     description="Update funding sources by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of funding sources",
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
     *                 example="Funding Sources 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of funding sources 1"
     *             ),
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="Vn123"
     *             ),
     *                 @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"Chính phủ", "Tư nhân", "Quốc tế"}, 
     *                 example="Tư nhân"
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
     *         description="Funding Sources updated successfully"
     *     )
     * )
     */
    public function update(UpdateFundingSourceRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $fundingSource = FundingSource::updateFundingSource($id, $data);

            if (!$fundingSource) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Nguồn tài trợ không tồn tại',
                ], 404);
            }
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật nguồn tài trợ thành công.',
                'data' => $fundingSource,
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
     *     path="/api/admin/funding-sources/{id}",
     *     tags={"Funding Sources"},
     *     summary="Delete funding sources by ID",
     *     description="Delete funding sources by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of funding sources",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Funding Sources deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $fundingSource = FundingSource::deleteFundingSource($id);

            if (!$fundingSource) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Nguồn tài trợ không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa nguồn tài trợ thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa nguồn tài trợ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/funding-sources/{id}/toggle-status",
     *     tags={"Funding Sources"},
     *     summary="Toggle active status of funding sources by ID",
     *     description="Toggle active status of funding sources by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of funding sources",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Funding sources status toggled successfully"
     *     )
     * )
     */
    public function toggleActiveStatus(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $fundingSource = FundingSource::findFundingSourceById($id);

            if (!$fundingSource) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Nguồn tài trợ không tồn tại',
                ], 404);
            }

            $fundingSource->is_active = !$fundingSource->is_active;
            $fundingSource->save();

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật trạng thái nguồn tài trợ thành công',
                'data' => [
                    'is_active' => $fundingSource->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái nguồn tài trợ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
