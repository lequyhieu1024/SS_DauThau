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
        $this->workProgressRepository = $workProgressRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return [
            'result' => true,
            'message' => 'Lấy tiến độ công việc thành công',
            'data' => new WorkProgressCollection($this->workProgressRepository->filter($request->all())),
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
                'message' => 'Lấy chi tiết nhiệm vụ thành công',
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
