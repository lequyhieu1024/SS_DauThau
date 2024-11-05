<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFormRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            return response([
                'result' => true,
                'message' => 'Lấy danh sách nhân viên thành công',
                'data' => new EmployeeCollection($this->employeeRepository->filter($request->all()))
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
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
    public function store(EmployeeFormRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $data['avatar'] = upload_image($request->file('avatar'));
            }
            $employee = $this->employeeRepository->create($data);
            return response([
                'result' => true,
                'message' => 'Tạo mới nhân viên thành công',
                'data' => new EmployeeResource($employee)
            ], 200);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
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
                'message' => 'Lấy chi tiết nhân viên thành công',
                'data' => new EmployeeResource($this->employeeRepository->findOrFail($id))
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => "Không tìm thấy nhân viên",
            ], 404);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeFormRequest $request, string $id)
    {
        try {
            $data = $request->all();
            $employee = $this->employeeRepository->findOrFail($id);
            if ($request->hasFile('avatar')) {
                $data['avatar'] = upload_image($request->file('avatar'));
                if ($employee->avatar && file_exists($employee->avatar)) {
                    unlink($employee->avatar);
                }
            } else {
                $data['avatar'] = $employee->avatar;
            }
            $employee->update($data);
            return response([
                'result' => true,
                'message' => 'Cập nhật nhân viên thành công',
                'data' => new EmployeeResource($employee)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => "Không tìm thấy nhân viên",
            ], 404);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeRepository->delete($id);
            return response([
                'result' => true,
                'message' => 'Xóa nhân viên thành công',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => "Không tìm thấy nhân viên",
            ], 404);
        } catch (\Exception $exception) {
            return response([
                'result' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getNameAndIds() {
        $data = $this->employeeRepository->getNameAndIds();
        return response([
            'result' => true,
            'message' => "Lấy danh sách nhân viên thành công",
            'data' => $data
        ], 200);
    }
}
