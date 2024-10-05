<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCatalogCollection;
use App\Models\PostCatalog;
use App\Repositories\PostCatalogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostCatalogController extends Controller
{
    protected $postCatalogRepository;
    public function __construct(PostCatalogRepository $postCatalogRepository){
        $this->postCatalogRepository = $postCatalogRepository;
    }
    /**
     * Display a listing of the resource.
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
    public function store(Request $request)
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
}
