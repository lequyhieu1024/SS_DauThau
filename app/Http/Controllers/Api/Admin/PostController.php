<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostFormRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    protected $postRepository;
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/posts",
     *     tags={"Posts"},
     *     summary="Get all Posts",
     *     description="Get all Posts",
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
     *         name="short_title",
     *         in="query",
     *         description="Short title of post",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Posts successfully",
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
     *                 example="Get Posts successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="posts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="author_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="author_name",
     *                             type="string",
     *                             example="Author Name"
     *                         ),
     *                         @OA\Property(
     *                             property="post_catalog_name",
     *                             type="string",
     *                             example="Post Catalog Name"
     *                         ),
     *                         @OA\Property(
     *                             property="short_title",
     *                             type="string",
     *                             example="Short Title"
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Post Title"
     *                         ),
     *                         @OA\Property(
     *                             property="content",
     *                             type="string",
     *                             example="Post Content"
     *                         ),
     *                         @OA\Property(
     *                             property="thumbnail",
     *                             type="string",
     *                             example="thumbnail.png"
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="integer",
     *                             example=1
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
     *                         )
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
        $posts = $this->postRepository->filter($request->all());
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách bài viết thành công',
            'data' => new PostCollection($posts),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     description="Create a new post",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"post_catalog_id", "short_title", "title", "content", "status"},
     *                 @OA\Property(
     *                     property="post_catalog_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="short_title",
     *                     type="string",
     *                     example="short_title"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="title"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="content"
     *                 ),
     *                 @OA\Property(
     *                     property="thumbnail",
     *                     type="string",
     *                     format="binary",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="number",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
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
     *                 example="Post created successfully"
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
     *                     property="author_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="author_name",
     *                     type="string",
     *                     example="Author Name"
     *                 ),
     *                 @OA\Property(
     *                     property="post_catalog_name",
     *                     type="string",
     *                     example="Post Catalog Name"
     *                 ),
     *                 @OA\Property(
     *                     property="short_title",
     *                     type="string",
     *                     example="Short Title"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Title"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="Content of the post"
     *                 ),
     *                 @OA\Property(
     *                     property="thumbnail",
     *                     type="string",
     *                     example="thumbnail.png"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="integer",
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
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(PostFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['author_id'] = auth()->id();
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = upload_image($request->file('thumbnail'));
            }

            $post = $this->postRepository->create($data);
            $this->postRepository->syncPostCatalog($data, $post->id);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo bài viết thành công.",
                "data" => new PostResource($post)
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
     *     path="/api/admin/posts/{id}",
     *     tags={"Posts"},
     *     summary="Get post by ID",
     *     description="Get post by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="post retrieved successfully",
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
     *                 example="post retrieved successfully"
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
     *                     property="author_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="author_name",
     *                     type="string",
     *                     example="Author Name"
     *                 ),
     *                 @OA\Property(
     *                     property="post_catalog_name",
     *                     type="string",
     *                     example="Post Catalog Name"
     *                 ),
     *                 @OA\Property(
     *                     property="short_title",
     *                     type="string",
     *                     example="Short Title"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Title"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="Content of the post"
     *                 ),
     *                 @OA\Property(
     *                     property="thumbnail",
     *                     type="string",
     *                     example="thumbnail.png"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="integer",
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
        $post = $this->postRepository->find($id);
        if (!$post) {
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy bài viết.'
            ], 404);
        } else {
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Lấy bài viết thành công.',
                'data' => new PostResource($post)
            ], 200);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/posts/{id}",
     *     tags={"Posts"},
     *     summary="Update post by ID",
     *     description="Update post by ID",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"post_catalog_id", "short_title", "title", "content", "status"},
     *
     *                 @OA\Property(
     *                     property="post_catalog_id",
     *                         type="integer",
     *                         example=1
     *                 ),
     *                 @OA\Property(
     *                     property="short_title",
     *                     type="string",
     *                     example="short_title"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="title"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="content"
     *                 ),
     *                 @OA\Property(
     *                     property="thumbnail",
     *                     type="string",
     *                     format="binary",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="number",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     example="PATCH"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully"
     *     )
     * )
     */
    public function update(PostFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = upload_image($request->file('thumbnail'));
                isset($this->postRepository->findOrFail($id)->thumbnail) ? unlink($this->postRepository->findOrFail($id)->thumbnail) : "";
            } else {
                $data['thumbnail'] = $this->postRepository->findOrFail($id)->thumbnail;
            }
            $post = $this->postRepository->update($data, $id);
            $this->postRepository->syncPostCatalog($data, $id);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật bài viết thành công",
                "data" => $post
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Cập nhật bài viết không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/posts/{id}",
     *     tags={"Posts"},
     *     summary="Delete post by ID",
     *     description="Delete post by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="post deleted successfully"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $post = $this->postRepository->find($id);
            $this->postRepository->delete($id);
            isset($post->thumbnail) ? unlink($post->thumbnail) : "";

            return response()->json([
                'result' => true,
                'message' => 'Xóa bài viết thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
