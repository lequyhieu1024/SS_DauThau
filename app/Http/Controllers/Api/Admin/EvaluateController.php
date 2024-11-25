<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluateFormRequest;
use App\Http\Resources\EvaluateCollection;
use App\Http\Resources\EvaluateResource;
use App\Models\Project;
use App\Repositories\EvaluateRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EvaluateController extends Controller
{
    protected $evaluateRepository;
    protected $projectRepository;

    public function __construct(EvaluateRepository $evaluateRepository, ProjectRepository $projectRepository) {
        $this->middleware(['permission:list_evaluate'])->only(['index']);
        $this->middleware(['permission:create_evaluate'])->only('store');
        $this->middleware(['permission:update_evaluate'])->only(['update']);
        $this->middleware(['permission:detail_evaluate'])->only('show');
        $this->middleware(['permission:destroy_evaluate'])->only('destroy');
        $this->evaluateRepository = $evaluateRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response([
            'result' => true,
            'message' => "Lấy danh sách đánh giá kết quả hoàn thành gói thầu thành công",
            'data' => new EvaluateCollection($this->evaluateRepository->filter($request->all()))
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EvaluateFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->evaluateRepository->create($request->all());
            $project = $this->projectRepository->findOrFail($request->all()['project_id']);
            if (empty($project->biddingResult)) {
                return response([
                    'result' => false,
                    'message' => 'Dự án này chưa có kết quả đấu thầu'
                ], 400);
            }
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Lưu trữ dữ liệu đánh giá kết quả hoàn thành gói thầu thành công'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return response([
                'result' => true,
                'message' => 'Lấy thông tin chi tiết đánh giá kết quả hoàn thành gói thầu thành công',
                'data' => new EvaluateResource($this->evaluateRepository->findOrFail($id))
            ], 200);
        } catch (NotFoundHttpException $e) {
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EvaluateFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $this->evaluateRepository->update($request->all(), $id);
            $project = $this->projectRepository->findOrFail($request->all()['project_id']);
            if (empty($project->biddingResult)) {
                return response([
                    'result' => false,
                    'message' => 'Dự án này chưa có kết quả đấu thầu'
                ], 400);
            }
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Cập nhật thông tin chi tiết đánh giá kết quả hoàn thành gói thầu thành công',
            ], 200);
        } catch (NotFoundHttpException $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->evaluateRepository->delete($id);
            return response([
                'result' => true,
                'message' => 'Xóa đánh giá kết quả hoàn thành gói thầu thành công',
            ], 200);
        } catch (NotFoundHttpException $e) {
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
