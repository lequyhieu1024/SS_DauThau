<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessActivityTypes\BusinessActivityTypeFormRequest;
use App\Http\Requests\Common\IndexBaseRequest;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Resources\Common\IndexBaseCollection;
use App\Repositories\BusinessActivityTypeRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


class BusinessActivityTypeController extends Controller
{

    protected $businessActivityTypeRepository;

    public function __construct(BusinessActivityTypeRepository $businessActivityTypeRepository)
    {
        $this->middleware(['permission:list_business_activity_type'])->only(['index', 'getAllIds']);
        $this->middleware(['permission:create_business_activity_type'])->only('store');
        $this->middleware(['permission:update_business_activity_type'])->only(['update', 'toggleActiveStatus']);
        $this->middleware(['permission:detail_business_activity_type'])->only('show');
        $this->middleware(['permission:destroy_business_activity_type'])->only('destroy');

        $this->businessActivityTypeRepository = $businessActivityTypeRepository;
    }


    /**
     * @OA\Get (
     *     path="/api/admin/business-activity-types",
     *     tags={"Business Activity Type"},
     *     summary="Get business activity types",
     *     description="Get business activity types",
     *     operationId="getBusinessActivityTypes",
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
     *    @OA\Response(
     *      response=200,
     *      description="Get bidding fields successfully",
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
     *      example="Get bidding fields successfully"
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
    public function index(IndexBaseRequest $request)
    {
        $businessActivityTypes = $this->businessActivityTypeRepository->filter($request->all());

        if ($businessActivityTypes->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy loại hoạt động kinh doanh',
            ], 404);
        }

        $data = new IndexBaseCollection($businessActivityTypes);

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách loại hoạt động kinh doanh thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/business-activity-types",
     *     tags={"Business Activity Type"},
     *     summary="Create a new business activity type",
     *     description="Store a newly created business activity type in storage",
     *     operationId="storeBusinessActivityType",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the business activity type",
     *                 example="Consulting"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the business activity type",
     *                 example="Consulting services"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="boolean",
     *                 description="Active status of the business activity type",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Business activity type created successfully",
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
     *                 example="Business activity type created successfully"
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
     *                     example="Consulting"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Consulting services"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create business activity type",
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
     *                 example="Failed to create business activity type"
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
    public function store(BusinessActivityTypeFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $businessActivityType = $this->businessActivityTypeRepository->create($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo loại hoạt động kinh doanh thành công',
                'data' => $businessActivityType,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo loại hoạt động kinh doanh',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/business-activity-types/all-ids",
     *     tags={"Business Activity Type"},
     *     summary="Get all business activity type IDs",
     *     description="Get all business activity type IDs",
     *     operationId="getAllBusinessActivityTypeIds",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *     response=200,
     *     description="Get all business activity type IDs successfully",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="result",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Get all business activity type IDs successfully"
     *    ),
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Consulting"
     *    ),
     *     )
     * )
     * )
     * )
     * )
     */
    public function getAllIds()
    {
//        $businessActivityTypes = $this->businessActivityTypeRepository->getAllIds();
        $businessActivityTypes = $this->businessActivityTypeRepository->getBusinessActivityTypesWithIndustries();

        if (!$businessActivityTypes) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy loại hoạt động kinh doanh',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách loại hoạt động kinh doanh thành công',
            'data' => $businessActivityTypes,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/business-activity-types/{id}",
     *     tags={"Business Activity Type"},
     *     summary="Get a business activity type by ID",
     *     description="Retrieve a specific business activity type by its ID",
     *     operationId="showBusinessActivityType",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the business activity type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business activity type retrieved successfully",
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
     *                 example="Business activity type retrieved successfully"
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
     *                     example="Consulting"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Consulting services"
     *                 ),
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
     *         description="Business activity type not found",
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
     *                 example="Business activity type not found"
     *             )
     *         )
     *     )
     * )
     */
    public function show(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        $businessActivityType = $this->businessActivityTypeRepository->find($id);

        if (!$businessActivityType) {
            return response()->json([
                'result' => false,
                'message' => 'Loại hoạt động kinh doanh không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin loại hoạt động kinh doanh thành công',
            'data' => $businessActivityType,
        ], 200);
    }

    private function checkSpecialRecord($id)
    {
        if ($id == 1) {
            throw new HttpResponseException(response()->json([
                'result' => false,
                'message' => 'Không thể chỉnh sửa hoặc xóa danh mục "Chưa phân loại".',
            ], 403));
        }
    }

    /**
     * @OA\Patch (
     *     path="/api/admin/business-activity-types/{id}",
     *     tags={"Business Activity Type"},
     *     summary="Update a business activity type by ID",
     *     description="Update a specific business activity type by its ID",
     *     operationId="updateBusinessActivityType",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the business activity type",
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
     *                 description="Name of the business activity type",
     *                 example="Consulting"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the business activity type",
     *                 example="Consulting services"
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="boolean",
     *                 description="Active status of the business activity type",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business activity type updated successfully",
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
     *                 example="Business activity type updated successfully"
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
     *                     example="Consulting"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Consulting services"
     *                 ),
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
     *         description="Business activity type not found",
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
     *                 example="Business activity type not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update business activity type",
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
     *                 example="Failed to update business activity type"
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
    public function update(ValidateIdRequest $request, BusinessActivityTypeFormRequest $updateRequest)
    {
        $id = $request->route('id');
        $this->checkSpecialRecord($id);

        DB::beginTransaction();

        try {
            $updateData = $updateRequest->all();
            $businessActivityType = $this->businessActivityTypeRepository->update($updateData, $id);

            if (!$businessActivityType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hoạt động kinh doanh không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Loại hoạt động kinh doanh đã được cập nhật thành công',
                'data' => $this->businessActivityTypeRepository->find($id),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật loại hoạt động kinh doanh',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/business-activity-types/{id}/toggle-status",
     *     tags={"Business Activity Type"},
     *     summary="Toggle active status of business activity type by ID",
     *     description="Toggle active status of business activity type by ID",
     *     operationId="toggleBusinessActivityTypeStatus",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the business activity type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business activity type status toggled successfully",
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
     *                 example="Business activity type status toggled successfully"
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
     *         description="Business activity type not found",
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
     *                 example="Business activity type not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to toggle business activity type status",
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
     *                 example="Failed to toggle business activity type status"
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
        $this->checkSpecialRecord($id);

        DB::beginTransaction();

        try {
            $businessActivityType = $this->businessActivityTypeRepository->toggleStatus($id);

            if (!$businessActivityType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hoạt động kinh doanh không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Trạng thái loại hoạt động kinh doanh đã được cập nhật thành công',
                'data' => [
                    'is_active' => $businessActivityType->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái loại hoạt động kinh doanh',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/business-activity-types/{id}",
     *     tags={"Business Activity Type"},
     *     summary="Delete business activity type by ID",
     *     description="Delete a specific business activity type by its ID",
     *     operationId="deleteBusinessActivityType",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the business activity type",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business activity type deleted successfully",
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
     *                 example="Business activity type deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business activity type not found",
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
     *                 example="Business activity type not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete business activity type",
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
     *                 example="Failed to delete business activity type"
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
        $this->checkSpecialRecord($id);

        DB::beginTransaction();

        try {
            $businessActivityType = $this->businessActivityTypeRepository->delete($id);

            if (!$businessActivityType) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Loại hoạt động kinh doanh không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Loại hoạt động kinh doanh đã được xóa thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa loại hoạt động kinh doanh',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}