<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostCatalogFormRequest;
use App\Http\Resources\PostCatalogCollection;
use App\Repositories\PostCatalogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostCatalogController extends Controller
{
    protected $postCatalogRepository;
    public function __construct(PostCatalogRepository $postCatalogRepository){
        $this->middleware(['permission:list_catalog'])->only(['index']);
        $this->middleware(['permission:create_catalog'])->only('store');
        $this->middleware(['permission:update_catalog'])->only(['update']);
        $this->middleware(['permission:detail_catalog'])->only('show');
        $this->middleware(['permission:destroy_catalog'])->only('destroy');
        $this->postCatalogRepository = $postCatalogRepository;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/post-catalogs",
     *     tags={"Post Catalogs"},
     *     summary="Get all Post Catalogs",
     *     description="Get all Post Catalogs",
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
     *         description="Name of post catalog",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Post Catalogs successfully",
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
     *                 example="Get Post Catalogs successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="postCatalogs",
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
     *                             example="Post Catalogs 1"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Description of Post Catalogs 1"
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
        $postCatalogs = $this->postCatalogRepository->filter($request->all());
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách danh mục bài viết thành công.',
            'data' => new PostCatalogCollection($postCatalogs),
        ], 200);
    }

    public function getCatalogsLandipage(Request $request)
    {
        $postCatalogs = $this->postCatalogRepository->filter($request->all());
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách danh mục bài viết thành công.',
            'data' => new PostCatalogCollection($postCatalogs),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/post-catalogs",
     *     tags={"Post Catalogs"},
     *     summary="Create a new post catalog",
     *     description="Create a new post catalog",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "is_active"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="post catalog 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of post catalog 1"
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
     *         description="post catalog created successfully",
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
     *                 example="post catalog created successfully"
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
     *                     example="post catalog 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of post catalog 1"
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
    public function store(PostCatalogFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $postCatalog = $this->postCatalogRepository->create($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới danh mục bài viết thành công.',
                'data' => $postCatalog
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo mới danh mục bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/post-catalogs/{id}",
     *     tags={"Post Catalogs"},
     *     summary="Get post catalog by ID",
     *     description="Get post catalog by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post catalog",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="post catalog retrieved successfully",
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
     *                 example="post catalog retrieved successfully"
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
     *                     example="post catalog 1"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Description of post catalog 1"
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
    public function show(string $id)
    {
        $postCatalog = $this->postCatalogRepository->find($id);

        if (!$postCatalog) {
            return response()->json([
                'result' => false,
                'message' => 'Danh mục bài viết không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin danh mục bài viết thành công.',
            'data' => $postCatalog
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Patch(
     *     path="/api/admin/post-catalogs/{id}",
     *     tags={"Post Catalogs"},
     *     summary="Update post catalog by ID",
     *     description="Update post catalog by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post catalog",
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
     *                 example="post catalog 1"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Description of post catalog 1"
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
     *         description="post catalog updated successfully"
     *     )
     * )
     */
    public function update(PostCatalogFormRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $postCatalog = $this->postCatalogRepository->update($data, $id);

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật danh mục bài viết thành công.',
                'data' => $postCatalog,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật danh mục bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/post-catalogs/{id}",
     *     tags={"Post Catalogs"},
     *     summary="Delete post catalog by ID",
     *     description="Delete post catalog by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post catalog",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="post catalog deleted successfully"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->postCatalogRepository->delete($id);

            return response()->json([
                'result' => true,
                'message' => 'Xóa danh mục bài viết thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa danh mục bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/post-catalogs/{id}/toggle-status",
     *     tags={"Post Catalogs"},
     *     summary="Toggle active status of post catalog by ID",
     *     description="Toggle active status of post catalog by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post catalog",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="post catalog status toggled successfully"
     *     )
     * )
     */
    public function toggleActiveStatus($id)
    {
        $postCatalog = $this->postCatalogRepository->toggleStatus($id);
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $postCatalog->is_active
        ], 200);
    }
}
