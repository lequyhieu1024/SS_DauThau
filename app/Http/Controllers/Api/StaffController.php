<?php

namespace App\Http\Controllers\Api;

use App\Jobs\sendEmailActiveJob;
use App\Models\Enterprise;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Http\Resources\StaffResource;
use App\Repositories\StaffRepository;
use App\Http\Requests\StaffFormRequest;
use App\Http\Resources\StaffCollection;

class StaffController extends Controller
{
    protected $staffRepository;
    protected $userRepository;
    protected $roleRepository;

    public function __construct(StaffRepository $staffRepository, RoleRepository $roleRepository, UserRepository $userRepository)
    {
        $this->middleware(['permission:list_staff'])->only('index');
        $this->middleware(['permission:create_staff'])->only(['create', 'store']);
        $this->middleware(['permission:update_staff'])->only(['edit', 'update']);
        $this->middleware(['permission:detail_staff'])->only('show');
        $this->middleware(['permission:destroy_staff'])->only('destroy');
        $this->staffRepository = $staffRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $staffs = $this->staffRepository->filter($request->all());
            return response([
                'result' => true,
                'message' => 'Danh sách nhan vien',
                'data' => new StaffCollection($staffs)
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $roles = $this->roleRepository->getAllNotPaginate();
            if ($roles->isEmpty()) {
                return response([
                    'result' => false,
                    'message' => 'Chưa có vai trò nào!',
                ], 404);
            } else {
                return response([
                    'result' => true,
                    'status' => 200,
                    'message' => 'Danh sách vai trò',
                    'data' => $roles
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaffFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = $this->userRepository->create($data)->syncRoles($this->roleRepository->getNameById($data['role_id']));
            $data['user_id'] = $user->id;
            $staff = $this->staffRepository->create($data);
            $data['receiver'] = "nhân viên";
            sendEmailActiveJob::dispatch($data);
            DB::commit();
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Thêm nhân viên thành công',
                'data' => $staff
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staff = $this->staffRepository->showStaff($id);
        if (!$staff) {
            return response([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        } else {
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Lấy nhân viên thành công',
                'data' => new StaffResource($staff)
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $staff = $this->staffRepository->showStaff($id);
            if (!$staff) {
                return response([
                    'result' => false,
                    'message' => 'Không tìm thấy nhân viên',
                ], 404);
            }
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Lấy thông tin nhân viên thành công',
                'staff' => new StaffResource($staff),
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StaffFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $this->userRepository->update($data, $this->staffRepository->showStaff($id)->user_id);
            $this->userRepository->findOrFail($this->staffRepository->findOrFail($id)->user_id)->syncRoles($this->roleRepository->getNameById($data['role_id']));
            $this->staffRepository->update($data, $id);
            DB::commit();
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Cập nhật nhân viên thành công',
                'staff' => new StaffResource($this->staffRepository->showStaff($id))
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->delete($this->staffRepository->findOrFail($id)->user_id);
            $this->staffRepository->delete($id);
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Xóa nhân viên thành công'
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'status' => 400,
                'message' => 'Xóa nhân viên thất bại, lỗi : ' . $th
            ], 400);
        }
    }

    public function banStaff($id)
    {
        $staff = Staff::find($id);
        $user = User::find($staff->user_id);
        //dd($user);
        if (!$user) {
            return response([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        }
        if ($user->account_ban_at == null) {
            $user->account_ban_at = now();
            $user->save();
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Khóa tài khoản thành công'
            ], 200);
        } else {
            $user->account_ban_at = null;
            $user->save();
            return response([
                'result' => true,
                'status' => 200,
                'message' => 'Mở khóa tài khoản thành công'
            ], 200);
        }
    }

    public function getnameAndIds()
    {
        $staffs = $this->staffRepository->getAllNotPaginate();
        return response()->json([
            'result' => true,
            'message' => "Lấy danh sách doanh nghiệp thành công",
            'data' => $staffs->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->user->name
                ];
            })
        ], 200);
    }
}
