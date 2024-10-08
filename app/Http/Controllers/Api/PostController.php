<?php

namespace App\Http\Controllers\Api;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
