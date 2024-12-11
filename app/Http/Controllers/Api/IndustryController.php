<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Requests\Industries\IndexIndustryRequest;
use App\Http\Requests\Industries\IndustryFormRequest;
use App\Http\Resources\Common\IndexBaseCollection;
use App\Repositories\IndustryRepository;
use Illuminate\Support\Facades\DB;


class IndustryController extends Controller
{
    protected $industryRepository;

    public function __construct(IndustryRepository $industryRepository)
    {
        $this->middleware(['permission:list_industry'])->only('index');
        $this->middleware(['permission:create_industry'])->only('store');
        $this->middleware(['permission:update_industry'])->only(['update', 'toggleActiveStatus']);
        $this->middleware(['permission:detail_industry'])->only('show');
        $this->middleware(['permission:destroy_industry'])->only('destroy');

        $this->industryRepository = $industryRepository;
    }

    /**
     * @OA\Get (
     *     path="/api/admin/industries",
     *     tags={"Industry"},
     *     summary="Get industries",
     *     description="Get industries",
     *     operationId="getIndustries",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records per page",
     *     required=false,
     *     @OA\Schema(
     *     type="integer",
     *     default=10
     *     )
     *    ),
     *     @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     required=false,
     *     @OA\Schema(
     *     type="integer",
     *     default=1
     *     )
     *   ),
     *     @OA\Parameter (
     *     name="name",
     *     in="query",
     *     description="Name of business activity type",
     *     required=false,
     *     @OA\Schema(
     *     type="string"
     *    )
     *   ),
     *     @OA\Parameter (
     *      name="business_activity_type_id",
     *      in="query",
     *      description="Id of business activity type",
     *      required=false,
     *      @OA\Schema(
     *      type="number"
     *     )
     *    ),
     *    @OA\Response(
     *      response=200,
     *      description="Get industries successfully",
     *      @OA\JsonContent(
     *      type="object",
     *      @OA\Property(
     *      property="result",
     *      type="boolean",
     *      example=true
     *      ),
     *      @OA\Property(
     *      property="message",
     *      type="string",
     *      example="Get industries successfully"
     *     ),
     *     @OA\Property(
     *     property="data",
     *     type="object",
     *     example={}
     *     )
     * )
     * )
     * )
     */
    public function index(IndexIndustryRequest $request)
    {
        $industries = $this->industryRepository->filter($request->all());
        $data = new IndexBaseCollection($industries);

        if ($industries->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy ngành nghề',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách ngành nghề thành công',
            'data' => $data,
        ], 200);
    }

    public function getNameAndIds()
    {
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách ngành nghề thành công',
            'data' => $this->industryRepository->getNameAndIds(),
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/industries",
     *     tags={"Industry"},
     *     summary="Create a new industry",
     *     description="Create a new industry",
     *     operationId="createIndustry",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Industry Name"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Industry Description"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="business_activity_type_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Industry created successfully",
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
     *                 example="Create industry successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create industry",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to create industry"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */

    public function store(IndustryFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $industry = $this->industryRepository->create($request->all());
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Tạo ngành nghề thành công',
                'data' => $industry,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/industries/{id}",
     *     tags={"Industry"},
     *     summary="Get industry by ID",
     *     description="Retrieve a specific industry by its ID",
     *     operationId="getIndustryById",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the industry",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Industry retrieved successfully",
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
     *                 example="Get industry successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Industry not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Industry not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve industry",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to retrieve industry"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function show(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        try {
            $industry = $this->industryRepository->find($id);

            if (!$industry) {
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            return response()->json([
                'result' => true,
                'message' => 'Lấy thông tin ngành nghề thành công',
                'data' => $industry,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi lấy thông tin ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch (
     *     path="/api/admin/industries/{id}",
     *     tags={"Industry"},
     *     summary="Update industry by ID",
     *     description="Update a specific industry by its ID",
     *     operationId="updateIndustry",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the industry",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Updated Industry Name"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="text",
     *                 example="Updated Industry Description"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="business_activity_type_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Industry updated successfully",
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
     *                 example="Update industry successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Industry not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Industry not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update industry",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to update industry"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function update(ValidateIdRequest $request, IndustryFormRequest $updateRequest)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $industry = $this->industryRepository->update($updateRequest->all(), $id);

            if (!$industry) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Cập nhật ngành nghề thành công',
                'data' => $this->industryRepository->find($id),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/industries/{id}/toggle-status",
     *     tags={"Industry"},
     *     summary="Toggle active status of industry by ID",
     *     description="Toggle active status of industry by ID",
     *     operationId="toggleIndustryActiveStatus",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the industry",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Industry active status toggled successfully",
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
     *                 example="Status of industry has been updated successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Industry not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Industry not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to toggle industry active status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to toggle industry active status"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function toggleActiveStatus(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $industry = $this->industryRepository->toggleStatus($id);

            if (!$industry) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Trạng thái hoạt động của ngành nghề đã được cập nhật thành công',
                'data' => [
                    'is_active' => $industry->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái hoạt động của ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/admin/industries/{id}",
     *     tags={"Industry"},
     *     summary="Delete industry by ID",
     *     description="Delete a specific industry by its ID",
     *     operationId="deleteIndustry",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the industry",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Industry deleted successfully",
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
     *                 example="Industry has been deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Industry not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Industry not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete industry",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to delete industry"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $deleted = $this->industryRepository->delete($id);

            if (!$deleted) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Ngành nghề đã được xóa thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
