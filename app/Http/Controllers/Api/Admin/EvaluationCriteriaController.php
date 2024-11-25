<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationCriteriaFormReuqest;
use App\Http\Resources\EvaluationCriteriaCollection;
use App\Http\Resources\EvaluationCriteriaResource;
use App\Repositories\EvaluationCriteriaRepository;
use Illuminate\Http\Request;

class EvaluationCriteriaController extends Controller
{
    protected $evaluationCriteriaRepository;

    public function __construct(EvaluationCriteriaRepository $evaluationCriteriaRepository)
    {
        $this->middleware(['permission:list_evaluation_criteria'])->only('index');
        $this->middleware(['permission:create_evaluation_criteria'])->only(['store']);
        $this->middleware(['permission:update_evaluation_criteria'])->only(['update', 'changeActive']);
        $this->middleware(['permission:detail_evaluation_criteria'])->only('edit');
        $this->middleware(['permission:destroy_evaluation_criteria'])->only('destroy');

        $this->evaluationCriteriaRepository = $evaluationCriteriaRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $evaluationCriterias = $this->evaluationCriteriaRepository->filter($request->all());
        if (empty($evaluationCriterias)) {
            return response([
                'result' => true,
                'message' => "Chưa có tiêu chí đánh giá nào",
                'data' => []
            ], status: 200);
        }
        return response([
            'result' => true,
            'message' => "Lấy danh sách tiêu chí đánh giá thành công",
            'data' => new EvaluationCriteriaCollection($evaluationCriterias)
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(EvaluationCriteriaFormReuqest $request)
    {
        try {
            $data = $this->evaluationCriteriaRepository->create($request->all());
            return response([
                'result' => true,
                'message' => 'Tạo mới tiêu chí đánh giá thành công',
                'data' => $data
            ], 201);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $evaluationCriteria = $this->evaluationCriteriaRepository->findOrFail($id);
        if (empty($evaluationCriteria)) {
            return response([
                'result' => true,
                'message' => "Tiêu chí đánh giá không tồn tại",
                'data' => []
            ], status: 404);
        }
        return response([
            'result' => true,
            'message' => "Lấy danh sách tiêu chí đánh giá thành công",
            'data' => new EvaluationCriteriaResource(resource: $evaluationCriteria)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EvaluationCriteriaFormReuqest $request, string $id)
    {
        try {
            $this->evaluationCriteriaRepository->update($request->all(), $id);
            return response([
                'result' => true,
                'message' => 'Cập nhật tiêu chí đánh giá thành công',
                'data' => new EvaluationCriteriaResource($this->evaluationCriteriaRepository->findOrFail($id))
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->evaluationCriteriaRepository->delete($id);
        return response([
            'result' => true,
            'message' => "Xóa tiêu chí đánh giá thành công",
        ], 200);
    }

    public function changeActive($id)
    {
        $evaluationCriteria = $this->evaluationCriteriaRepository->findOrFail($id);
        $evaluationCriteria->is_active = !$evaluationCriteria->is_active;
        $evaluationCriteria->save();
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $evaluationCriteria->is_active
        ], 200);
    }

    public function getNameAndIds()
    {
        $data = $this->evaluationCriteriaRepository->getNameAndIds();
        return response([
            'result' => true,
            'message' => "Lấy danh sách tiêu chí đánh giá thành công",
            'data' => $data
        ], 200);
    }
}
