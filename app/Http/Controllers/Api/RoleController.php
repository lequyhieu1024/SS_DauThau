<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Staff;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->middleware(['permission:list_role'])->only('index');
        $this->middleware(['permission:create_role'])->only(['create', 'store']);
        $this->middleware(['permission:update_role'])->only(['edit', 'update']);
        $this->middleware(['permission:detail_role'])->only('show');
        $this->middleware(['permission:destroy_role'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository->filter($request->all());

        $data = new RoleCollection($roles);

        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách vai trò thành công',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->roleRepository->getPermissions();

        $permissions = $permissions->flatMap(function ($group) {
            return $group;
        });

        $data = new PermissionCollection($permissions);
        if (!$permissions) {
            return response([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy permission',
            ], 404);
        }
        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách permission thành công',
            'permissions' => $data,
        ], 200);
    }

    public function store(RoleFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->roleRepository->createRole($request->all());
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Tạo vai trò thành công',
                'status' => 201,
                'data' => $role
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 400);
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
        $role = $this->roleRepository->showRole($id);
        if (!$role) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy vai trò',
            ], 404);
        }
        return response([
            'result' => true,
            'message' => 'Lấy chi tiết vai trò thành công',
            'data' => new RoleResource($role)
        ], 200);
    }

    public function edit($id)
    {
        $role = $this->roleRepository->showRole($id);
        $permissionIds = $role->permissions->pluck('id');
        return response()->json([
            'result' => true,
            'role' => $role,
            'id_permission_checked' => $permissionIds,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $role = $this->roleRepository->updateRole($request->all(), $id);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Cập nhật quyền thành công',
                'data' => $role,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 400);
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
            $staff = Staff::where('role_id', $id)->get();
            if ($staff->isEmpty()) {
                $this->roleRepository->deleteRole($id);
                return response([
                    "message" => "Xóa quyền thành công",
                    "status" => 200
                ], 200);
            } else {
                return response([
                    "message" => "Không thể xóa quyền vì đã có người dùng",
                    "status" => 400
                ], 400);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response([
                "message" => "Không tìm thấy quyền cần xóa",
                "status" => 400
            ], 400);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                "status" => 400
            ], 400);
        }
    }
}
