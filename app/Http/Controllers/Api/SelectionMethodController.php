<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Requests\SelectionMethodRequest;
use App\Http\Resources\SelectionMethodCollection;
use App\Repositories\SelectionMethodRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SelectionMethodController extends Controller
{
    protected $selectionMethodRepository;

    public function __construct(SelectionMethodRepository $selectionMethodRepository)
    {
        $this->selectionMethodRepository = $selectionMethodRepository;
        // $this->middleware(['permission:list_selection_method'])->only('index');
        // $this->middleware(['permission:create_selection_method'])->only(['store']);
        // $this->middleware(['permission:update_selection_method'])->only(['update', 'toggleActiveStatus']);
        // $this->middleware(['permission:detail_selection_method'])->only('show');
        // $this->middleware(['permission:destroy_selection_method'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/selection-methods",
     *     tags={"Selection methods"},
     *     summary="Get all Selection methods",
     *     description="Get all Selection methods",
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
     *         name="method_name",
     *         in="query",
     *         description="Method name of selection method",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Selection methods successfully",
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
     *                 example="Get Selection methods successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="selectionMethods",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="method_name",
     *                             type="string",
     *                             example="Selection methods 1"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Description of Selection methods 1"
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
    public function index(Request $request)
    {
        $selectionMethods = $this->selectionMethodRepository->filter($request->all());

        $data = new SelectionMethodCollection($selectionMethods);

        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách các hình thức lựa chọn nhà thầu thành công',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/selection-methods",
     *     tags={"Selection methods"},
     *     summary="Create a new selection method",
     *     description="Create a new selection method",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"method_name", "description", "is_active"},
     *             @OA\Property(
     *                 property="method_name",
     *                 type="string",
     *                 example="selection method 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of selection method 1"
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
     *         description="selection method created successfully",
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
     *                 example="selection method created successfully"
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
     *                     example="selection method 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of selection method 1"
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
    public function store(SelectionMethodRequest $request)
    {
        DB::beginTransaction();

        try {
            $selectionMethod = $this->selectionMethodRepository->createSelectionMethod($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới hình thức lựa chọn nhà thầu thành công.',
                'data' => $selectionMethod,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo mới hình thức lựa chọn nhà thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/selection-methods/{id}",
     *     tags={"Selection methods"},
     *     summary="Get selection method by ID",
     *     description="Get selection method by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of selection method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="selection method retrieved successfully",
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
     *                 example="selection method retrieved successfully"
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
     *                     example="selection method 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of selection method 1"
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
        $selectionMethod = $this->selectionMethodRepository->findSelectionMethodById($id);

        if (!$selectionMethod) {
            return response()->json([
                'result' => false,
                'message' => 'Hình thức lựa chọn nhà thầu không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin hình thức lựa chọn nhà thầu thành công.',
            'data' => $selectionMethod
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/selection-methods/{id}",
     *     tags={"Selection methods"},
     *     summary="Update selection method by ID",
     *     description="Update selection method by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of selection method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="method_name",
     *                 type="string",
     *                 example="selection method 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of selection method 1"
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
     *         description="selection method updated successfully"
     *     )
     * )
     */
    public function update(SelectionMethodRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $selectionMethod = $this->selectionMethodRepository->updateSelectionMethod($data, $id);

            if (!$selectionMethod) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Hình thức lựa chọn nhà thầu không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật hình thức lựa chọn nhà thầu thành công.',
                'data' => $selectionMethod,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật hình thức lựa chọn nhà thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/selection-methods/{id}",
     *     tags={"Selection methods"},
     *     summary="Delete selection method by ID",
     *     description="Delete selection method by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of selection method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="selection method deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $selectionMethod = $this->selectionMethodRepository->deleteSelectionMethod($id);

            if (!$selectionMethod) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Hình thức lựa chọn nhà thầu không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa hình thức lựa chọn nhà thầu thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa hình thức lựa chọn nhà thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/selection-methods/{id}/toggle-status",
     *     tags={"Selection methods"},
     *     summary="Toggle active status of selection method by ID",
     *     description="Toggle active status of selection method by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of selection method",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="selection method status toggled successfully"
     *     )
     * )
     */
    public function toggleActiveStatus(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $selectionMethod = $this->selectionMethodRepository->findSelectionMethodById($id);

            if (!$selectionMethod) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Hình thức lựa chọn nhà thầu không tồn tại.',
                ], 404);
            }

            $selectionMethod->is_active = !$selectionMethod->is_active;
            $selectionMethod->save();

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật trạng thái hình thức lựa chọn nhà thầu thành công.',
                'data' => [
                    'is_active' => $selectionMethod->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật trạng thái hình thức lựa chọn nhà thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getNameAndIds()
    {
        return response()->json([
            'result' => true,
            'message' => "Lấy danh sách phương thức lựa chọn nhà thầu thành công",
            'data' => $this->selectionMethodRepository->getSelectionMethod()
        ],200);
    }
}
