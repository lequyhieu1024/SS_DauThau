<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkProgressFormRequest;
use App\Http\Resources\WorkProgressCollection;
use App\Http\Resources\WorkProgressResource;
use App\Repositories\ProjectRepository;
use App\Repositories\WorkProgressRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkProgressController extends Controller
{
    protected $workProgressRepository;
    protected $projectRepository;
    public function __construct(WorkProgressRepository $workProgressRepository, ProjectRepository $projectRepository)
    {
        $this->middleware(['permission:list_work_progress'])->only(['index']);
        $this->middleware(['permission:create_work_progress'])->only('store');
        $this->middleware(['permission:update_work_progress'])->only(['update']);
        $this->middleware(['permission:detail_work_progress'])->only('show');
        $this->middleware(['permission:destroy_work_progress'])->only('destroy');
        // permission
        $this->workProgressRepository = $workProgressRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data =$this->workProgressRepository->filter($request->all());
        return [
            'result' => true,
            'message' => 'Lấy tiến độ công việc thành công',
            'data' => new WorkProgressCollection($data),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkProgressFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $project = $this->projectRepository->findOrFail($data['project_id']);
            if (!$project || empty($project->biddingResult)) {
                return response([
                    'result' => false,
                    'message' => 'Dự án không tồn tại hoặc chưa có kết quả đấu thầu',
                ], 400);
            }
            $data['bidding_result_id'] = $project->biddingResult->id;
            $progress = $this->workProgressRepository->create($data);
            $this->workProgressRepository->syncTaskProgresses($data, $progress->id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Lưu trữ dữ liệu tiến độ dự án thành công'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage(),
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
                'message' => 'Lấy chi tiết nhiệm tiến độ dự án thành công',
                'data' => new WorkProgressResource($this->workProgressRepository->findOrFail($id))
            ], 200);
        } catch (ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy tiến độ dự án',
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkProgressFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $workProgress = $this->workProgressRepository->find($id);
            if (!$workProgress) {
                return response([
                    'result' => false,
                    'message' => 'Không tìm thấy tiến độ dự án',
                ], 404);
            }
            $project = $this->projectRepository->find($data['project_id']);
            if (!$project || empty($project->biddingResult)) {
                return response([
                    'result' => false,
                    'message' => 'Dự án không tồn tại hoặc chưa có kết quả đấu thầu',
                ], 400);
            }

            $data['bidding_result_id'] = $project->biddingResult->id;


            $this->workProgressRepository->update($data, $id);
            $this->workProgressRepository->syncTaskProgresses($data, $id);

            DB::commit();

            return response([
                'result' => true,
                'message' => 'Cập nhật dữ liệu tiến độ dự án thành công'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
//            $task = $this->workProgressRepository->findOrFail($id);
//            $task->taskProgresses()->detach(); // xóa mềm nên không xóa bảng phụ
            $this->workProgressRepository->delete($id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Xóa tiến độ dự án này thành công',
            ], 200);
        } catch (ModelNotFoundException) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => 'Không tìm thấy tiến độ dự án muốn xóa',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
