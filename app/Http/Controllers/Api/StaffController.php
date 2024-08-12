<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Staff;
use App\Models\ModelHasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StaffResource;
use App\Http\Resources\StaffCollection;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:list_staff'])->only('index');
        $this->middleware(['permission:create_staff'])->only(['create', 'store']);
        $this->middleware(['permission:update_staff'])->only(['edit', 'update']);
        $this->middleware(['permission:detail_staff'])->only('show');
        $this->middleware(['permission:destroy_staff'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Lấy tham số 'size' từ request, mặc định là 10 nếu không có
            $pageSize = $request->query('size', 10);
            $staffs = Staff::join('users', 'users.id', '=', 'staffs.user_id')->join('roles', 'roles.id', '=', 'staffs.role_id')->select('users.*','users.id as user_id', 'staffs.*','staffs.id as staff_id','roles.name as role_name')->paginate($pageSize);
            $data = new StaffCollection($staffs); // chưa hoàn thiện staff collection
            return response(
                [
                    'result' => true,
                    'status' => 200,
                    'message' => 'Lấy danh sách nhân viên thành công',
                    'data' => $data
                ],
                200
            );
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
            // lấy ra danh sách  vai trò
            $roles = Role::all();
            // nếu chưa có vai trò nào thì thông báo chưa có vai trò
            if ($roles->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Chưa có vai trò nào!',
                ], 404);
            }
            // nếu có vai trò thì in ra vai trò
            else
            {
                return response()->json([
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'taxcode' => 'required|unique:users',
                'email' => 'required|unique:users|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'phone' => 'required|unique:users|regex:/^0[0-9]{9}$/',
                'password' => 'required',
                'role_id' => 'required'
            ];
            $message = [
                'name.required' => 'Vui lòng nhập tên',
                'taxcode.required' => 'Vui lòng nhập mã số thuế',
                'taxcode.unique' => 'Mã số thuế đã tồn tại',
                'phone.required' => 'Vui lòng nhập sđt',
                'phone.regex' => 'Sđt không đúng đinh dạng',
                'phone.unique' => 'Sđt đã tồn tại',
                'email.unique' => 'Email đã tồn tại',
                'email.regex' => 'Email không đúng đinh dạng',
                'email.required' => 'Vui lòng nhập email',
                'password.required' => 'Vui lòng nhập password',
                'role_id.required' => 'Vui lòng chọn vai trò'
            ];
            $validator = validator($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            // thêm vào user db
            $user = new User();
            $user->name = $request->name;
            $user->taxcode = $request->taxcode;
            $user->account_ban_at = $request->account_ban_at;
            $user->type = "staff";
            if ($request->hasFile('avatar')) {
                $user->avatar = upload_image($request->file('avatar'));
            }
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            // thêm vào staff db
            $staff = new Staff();
            $staff->user_id = $user->id;
            $staff->role_id = json_encode($request->role_id);
            $staff->save();
            // thêm vào bảng model has role để được phân quyền
            foreach ($request->role_id as $roleId) {
                $modelHasRole = new ModelHasRole();
                $modelHasRole->role_id = $roleId;
                $modelHasRole->model_type = 'App\Models\User';
                $modelHasRole->model_id = $user->id;
                $modelHasRole->save();
            }
//            dd($modelHasRole);
            //commit
            DB::commit();
            // thêm thành công
            return response()->json([
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staff = Staff::join('users', 'users.id', '=', 'staffs.user_id')->where('staffs.id', '=', $id)->select('staffs.*','users.*','users.id as user_id')->first();
        $staffRole = json_decode($staff->role_id);
        $roleName = [];
        foreach ($staffRole as $roleId) {
            $role = Role::find($roleId);
            $roleName[] = $role->name;
        }

        $staff->role_name = $roleName;
        if (!$staff) {
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        } else {
            return response()->json([
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // lấy ra thông tin nhân viên
            $staff = Staff::join('users', 'users.id', 'staffs.user_id')->where('staffs.id', '=', $id)->select('staffs.id as staff_id','staffs.role_id as staff_role','users.*')->first();
            // dd($staff);
            $staff->staff_role = json_decode($staff->staff_role);
            // lấy ra danh sách  vai trò
            $roles = Role::all();
            // nếu chưa có vai trò nào thì thông báo chưa có vai trò
            if(!$staff){
                return response()->json([
                    'result' => false,
                    'message' => 'Không tìm thấy nhân viên',
                ], 404);
            }
            if ($roles->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Chưa có vai trò nào!',
                ], 404);
            }
            // nếu có vai trò thì in ra vai trò
            else
            {
                return response()->json([
                    'result' => true,
                    'status' => 200,
                    'message' => 'Lấy thông tin nhân viên thành công',
                    'staff' => $staff,
                    'list_role' => $roles
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
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
            DB::beginTransaction();
            $data = $request->all();
//            dd($data);
            $staff = Staff::where('id', $id)->firstOrFail();
            $rules = [
                'name' => 'required',
                'taxcode' => 'required|unique:users,taxcode,' . $staff->user_id,
                'email' => 'required|unique:users,email,' . $staff->user_id . '|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'phone' => 'required|unique:users,phone,' . $staff->user_id . '|regex:/^0[0-9]{9}$/',
                'role_id' => 'required'
            ];
            $message = [
                'name.required' => 'Vui lòng nhập tên',
                'taxcode.required' => 'Vui lòng nhập mã số thuế',
                'taxcode.unique' => 'Mã số thuế đã tồn tại',
                'phone.required' => 'Vui lòng nhập sđt',
                'phone.regex' => 'Sđt không đúng đinh dạng',
                'phone.unique' => 'Sđt đã tồn tại',
                'email.unique' => 'Email đã tồn tại',
                'email.regex' => 'Email không đúng đinh dạng',
                'email.required' => 'Vui lòng nhập email',
                'role_id.required' => 'Vui lòng chọn vai trò'
            ];
            $validator = validator($data, $rules, $message);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // câph nhật vào staff db
            $staff->role_id = json_encode($request->role_id);
            $staff->save();

            // câph nhật vào user db
            $user = User::findOrFail($staff->user_id);
            $user->name = $request->name;
            $user->taxcode = $request->taxcode;
            if ($request->hasFile('avatar')) {
                $user->avatar = upload_image($request->file('avatar'));
            }
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            // Lấy các vai trò hiện tại của nhân viên từ bảng model_has_roles
            $currentRoles = ModelHasRole::where('model_id', $staff->user_id)->pluck('role_id')->toArray();

            // Vai trò mới từ yêu cầu
            $newRoles = $request->input('role_id', []);

            // Vai trò cần xóa: những vai trò hiện tại nhưng không còn trong danh sách vai trò mới
            $rolesToDelete = array_diff($currentRoles, $newRoles);

            // Vai trò cần thêm: những vai trò mới nhưng không có trong danh sách vai trò hiện tại
            $rolesToAdd = array_diff($newRoles, $currentRoles);

            // Xóa các vai trò không còn tồn tại
            ModelHasRole::where('model_id', $staff->user_id)
                ->whereIn('role_id', $rolesToDelete)
                ->delete();

            // Thêm các vai trò mới
            foreach ($rolesToAdd as $roleId) {
                ModelHasRole::create([
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $staff->user_id,
                ]);
            }

            // commit
            DB::commit();

            // cập nhật thành công thì lấy lại thông tin nhân viên vừa cập nhật
            $staffNew = Staff::join('users', 'users.id', '=', 'staffs.user_id')
                            ->select('staffs.*','users.*','users.id as user_id')
                            ->where('staffs.id', $id)
                            ->first();
//            dd($staffNew);
            $staffNew->role_id = json_decode($staffNew->role_id);
            $roleName = [];
            foreach ($staffNew->role_id as $roleId) {
                $role = Role::find($roleId);
                $roleName[] = $role->name;
            }

            $staffNew->role_name = $roleName;

            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Cập nhật nhân viên thành công',
                'staff' => new StaffResource($staffNew)
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
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
        $staff = Staff::find($id);
        $user = User::find($staff->user_id);
        if(!$staff){
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên cần xóa'
            ], 404);
        }
        if($staff->delete() && $user->delete()){
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Xóa nhân viên thành công'
            ], 200);
        }else{
            return response()->json([
                'result' => false,
                'status' => 400,
                'message' => 'Xóa nhân viên thất bại'
            ], 400);
        }
    }

    public function banStaff($id){
        $staff = Staff::find($id);
        $user = User::find($staff->user_id);
        //dd($user);
        if(!$user){
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        }
        if($user->account_ban_at == null){
            $user->account_ban_at = now();
            $user->save();
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Khóa tài khoản thành công'
            ], 200);
        }else{
            $user->account_ban_at = null;
            $user->save();
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Mở khóa tài khoản thành công'
            ], 200);
        }
    }
}
