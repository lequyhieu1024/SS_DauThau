<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcurementCategoryFormRequest;
use App\Http\Resources\Common\IndexBaseCollection;
use App\Repositories\ProcurementCategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementCategoryController extends Controller
{
    protected $procurementCategoryRepository;

    public function __construct(ProcurementCategoryRepository $procurementCategoryRepository)
    {
        $this->middleware(['permission:list_procurement_category'])->only(['index']);
        $this->middleware(['permission:create_procurement_category'])->only('store');
        $this->middleware(['permission:update_procurement_category'])->only(['update', 'changeActive']);
        $this->middleware(['permission:detail_procurement_category'])->only('show');
        $this->middleware(['permission:destroy_procurement_category'])->only('destroy');

        $this->procurementCategoryRepository = $procurementCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $procurementCategories = $this->procurementCategoryRepository->filter($request->all());

        if ($procurementCategories->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy loại hoạt động kinh doanh',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách loại hoạt động kinh doanh thành công',
            'data' => new IndexBaseCollection($procurementCategories),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProcurementCategoryFormRequest $request)
    {
        try {
            $procurementCategories = $this->procurementCategoryRepository->create($request->all());
            return response()->json([
                'result' => true,
                'message' => 'Tạo loại hình mua sắm công thành công',
                'data' => $procurementCategories,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo loại hình mua sắm công',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $procurementCategory = $this->procurementCategoryRepository->find($id);

        if (!$procurementCategory) {
            return response()->json([
                'result' => false,
                'message' => 'Loại hoạt động kinh doanh không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin loại hoạt động kinh doanh thành công',
            'data' => $procurementCategory,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProcurementCategoryFormRequest $request, string $id)
    {
        try {
            if ($id == 1) {
                return response()->json([
                    'result' => false,
                    'message' => 'Không thể cập nhật loại hình mua sắm công mặc định',
                ], 403);
            }
            $procurementCategory = $this->procurementCategoryRepository->update($request->all(), $id);
            return response()->json([
                'result' => true,
                'message' => 'Loại hoạt động kinh doanh đã được cập nhật thành công',
                'data' => $this->procurementCategoryRepository->find($id),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật loại hoạt động kinh doanh',
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
            if ($id == 1) {
                return response()->json([
                    'result' => false,
                    'message' => 'Không được xóa loại hình mua sắm công mặc định',
                ], 403);
            }

            $this->procurementCategoryRepository->delete($id);
            return response()->json([
                'result' => true,
                'message' => 'Loại hoạt động kinh doanh đã được xóa thành công',
            ], 200);
        } catch (ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy lĩnh vực mua sắm công cần xóa'
            ], 404);
        }
    }

    public function changeActive($id)
    {
        if ($id == 1) {
            return response()->json([
                'result' => false,
                'message' => 'Không được ẩn loại hình mua sắm công mặc định',
            ], 403);
        }
        $data = $this->procurementCategoryRepository->findOrFail($id);
        $data->is_active = !$data->is_active;
        $data->save();
        return response()->json([
            'result' => true,
            'message' => 'Trạng thái loại hình mua sắm công đã được cập nhật thành công',
            'data' => [
                'is_active' => $data->is_active,
            ],
        ], 200);
    }

    public function getNameAndIds()
    {
        $procurementCategories = $this->procurementCategoryRepository->getNameAndIds();
        return response()->json([
            'result' => true,
            'data' => $procurementCategories,
        ], 200);
    }
}
