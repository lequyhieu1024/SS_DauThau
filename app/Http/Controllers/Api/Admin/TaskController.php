<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskFormRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            return response([
                'result' => true,
                'message' => 'Lấy danh sách nhiệm vụ thành công.',
                'data' => new TaskCollection($this->taskRepository->filter($request->all()))
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $task = $this->taskRepository->create($data);
            $this->taskRepository->syncEmployee($data, $task->id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Tạo nhiệm vụ thành công'
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
                'data' => new TaskResource($this->taskRepository->findOrFail($id))
            ], 200);
        } catch (ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy nhiệm vụ',
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
    public function update(TaskFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $task = $this->taskRepository->findOrFail($id);
            $this->taskRepository->update($data, $id);
            $this->taskRepository->syncEmployee($data, $id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Cập nhật vụ thành công',
                'data' => new TaskResource($task)
            ], 200);
        } catch (ModelNotFoundException) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => 'Không tìm thấy nhiệm vụ',
            ], 404);
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
            $task = $this->taskRepository->findOrFail($id);
            $task->employees()->detach();
            $this->taskRepository->delete($id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Xóa nhiệm vụ thành công',
            ], 200);
        } catch (ModelNotFoundException) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => 'Không tìm thấy nhiệm vụ',
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
