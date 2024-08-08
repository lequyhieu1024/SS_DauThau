<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Requests\Industries\IndexIndustryRequest;
use App\Http\Requests\Industries\StoreIndustryRequest;
use App\Http\Requests\Industries\UpdateIndustryRequest;
use App\Http\Resources\Common\IndexBaseCollection;
use App\Models\Industry;

class IndustryController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/admin/industries",
     *     tags={"Industries"},
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
     *      name="business_activity_type_name",
     *      in="query",
     *      description="Name of business activity type",
     *      required=false,
     *      @OA\Schema(
     *      type="string"
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
        $industries = Industry::searchIndustries(
            $request->query('name'),
            $request->query('business_activity_type_name'),
            $request->query('page', 1),
            $request->query('size', 10)
        );
        $data = new IndexBaseCollection($industries);

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách ngành nghề thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/industries",
     *     tags={"Industries"},
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
    public function store(StoreIndustryRequest $request)
    {

        try {
            $industry = Industry::createNew($request->all());
            return response()->json([
                'result' => true,
                'message' => 'Tạo ngành nghề thành công',
                'data' => $industry,
            ], 201);
        } catch (\Exception $e) {
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
     *     tags={"Industries"},
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
            $industry = Industry::findIndustryById($id);

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
     *     tags={"Industries"},
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
    public function update(ValidateIdRequest $request, UpdateIndustryRequest $updateRequest)
    {
        $id = $request->route('id');

        try {
            $industry = Industry::updateIndustry($id, $request->all());

            if (!$industry) {
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật ngành nghề thành công',
                'data' => $industry,
            ], 200);
        } catch (\Exception $e) {
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
     *     tags={"Industries"},
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

        try {
            $industry = Industry::toggleActiveStatus($id);

            if (!$industry) {
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            return response()->json([
                'result' => true,
                'message' => 'Trạng thái hoạt động của ngành nghề đã được cập nhật thành công',
                'data' => [
                    'is_active' => $industry->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
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
     *     tags={"Industries"},
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

        try {
            $deleted = Industry::deleteIndustryById($id);

            if (!$deleted) {
                return response()->json([
                    'result' => false,
                    'message' => 'Ngành nghề không tồn tại',
                ], 404);
            }

            return response()->json([
                'result' => true,
                'message' => 'Ngành nghề đã được xóa thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa ngành nghề',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
