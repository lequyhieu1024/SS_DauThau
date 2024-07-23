<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RoleResource;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleCollection;
use App\Models\Permission;
// use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
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
        // Lấy tham số 'size' từ request, mặc định là 10 nếu không có
        $pageSize = $request->query('size', 10);
    
        // Lấy dữ liệu từ model Role và phân trang
        $roles = Role::paginate($pageSize);
    
        // Tạo collection để trả về
        $data = new RoleCollection($roles);
    
        // Trả về dữ liệu JSON
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách vai trò thành công',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        foreach($permissions as $permission) {
            // dd($permission);
            $permission->name = __(convertText($permission->name));
            $permission->value = $permission->section;
            $permission->section = __(convertText($permission->section));
        }
        
        if ($permissions->isEmpty()) {
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy permission',
            ], 404);
        }
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Lấy danh sách permission thành công',
            'permissions' => $permissions
        ], 200);
    }
    public function store(Request $request)
    {
        try {
            // lưu tất cả dữ liệu gửi đi từ phương thức post vào $data
            $data = $request->all();
            // tạo rule để validate dữ liệu
            $rules = [
                'name' => 'required',
                'permissions' => 'required'
            ];
            // tạo message tương ứng với rule để báo lỗi
            $message = [
                'name.required' => 'Vui lòng nhập tên quyền',
                'permissions.required' => 'Chọn ít nhất 1 quyền'
            ];
            // tạo đối tượng validater với dữ liệu $data, $rules, $message
            $validator = Validator::make($data, $rules, $message);
            // kiểm tra dữ liệu với đối tượng validater, nếu fails thì return message báo lỗi bằng json, không fails thì chạy xuống else, thực hiện transaction
            if ($validator->fails()) {
                return response()->json(
                    [
                        'result' => false,
                        'status' => 400,
                        'message' => $validator->errors()
                    ],
                    400
                );
            } else {
                // bắt đầu transaction
                DB::beginTransaction();
                // insert name role vào bảng role, từ input có name là : "name", guard_name mặc định "api", created_at và updated_at là thời điểm hiện tại
                $role = Role::create(['name' => $request->name, 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()]);
                // lặp dữ liệu permissions từ form gửi lên rồi insert vào bảng role_has_permissions
                foreach ($request->permissions as $permission) {
                    $roleHasPermission = new RoleHasPermission();
                    $roleHasPermission->permission_id = $permission;
                    $roleHasPermission->role_id = $role->id;
                    $roleHasPermission->save();
                }
                // không có lỗi thì commit với transaction
                DB::commit();
                // sau khi commit thì return message thông báo bằng json
                return response()->json([
                    'result' => true,
                    'message' => 'Tạo vai trò thành công',
                    'status' => 201,
                    'data' => $role
                ], 201);
            }
        } catch (\Exception $e) {
            //có lỗi thì rollback + message báo lỗi
            DB::rollBack();
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy vai trò',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy vai trò thành công',
            'data' => new RoleResource($role)
        ], 200);
    }
    public function edit($id)
    {
        $permission_groups = Permission::all();
        foreach($permission_groups as $permission) {
            // dd($permission);
            $permission->name = __(convertText($permission->name));
            $permission->section = __(convertText($permission->section));
        }
        // $permission_groups = Permission::get()->groupBy('section');
        $role = Role::findOrFail($id);
        $permission_checked = RoleHasPermission::where('role_id', $id)->pluck('permission_id')->toArray();
        return response()->json([
            'result' => true,
            'role' => $role,
            'permissions' => $permission_groups,
            'id_permission_checked' => $permission_checked,
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $rules = [
                'name' => 'required|unique:roles,name,' . $id,
                'permissions' => 'required'
            ];
            $message = [
                'name.required' => 'Vui lòng nhập tên quyền',
                'name.unique' => 'Tên quyền đã tồn tại',
                'permissions.required' => 'Chọn ít nhất 1 quyền'
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();
            $permissions = $request->permissions;
            foreach ($permissions as $permission) {
                // Tìm kiếm bản ghi RoleHasPermission tương ứng với role_id và permission_id
                $roleHasPermission = RoleHasPermission::where('role_id', $role->id)
                    ->where('permission_id', $permission)
                    ->first();
                if ($roleHasPermission !== null) {
                    continue;
                }

                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission
                ]);
            }
            // Xóa các bản ghi RoleHasPermission mà không có trong danh sách permissions từ request
            RoleHasPermission::where('role_id', $role->id)
                ->whereNotIn('permission_id', $permissions)
                ->delete();
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Cập nhật quyền thành công',
                'data' => $role,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            $staff = Staff::where('role_id', $id)->get();
            $permissions = RoleHasPermission::where('role_id', $id)->get();

            if ($staff->isEmpty()) {
                foreach ($permissions as $permission) {
                    $permission->delete();
                }
                $role->delete();

                return response()->json([
                    "message" => "Xóa quyền thành công",
                    "status" => 200
                ], 200);
            } else {
                return response()->json([
                    "message" => "Không thể xóa quyền vì đã có người dùng",
                    "status" => 400
                ], 400);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response()->json([
                "message" => "Không tìm thấy quyền cần xóa",
                "status" => 400
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "status" => 400
            ], 400);
        }
    }
}
