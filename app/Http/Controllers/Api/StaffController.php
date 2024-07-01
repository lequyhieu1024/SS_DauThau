<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Staff;
use App\Models\ModelHasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StaffCollection;
use App\Http\Resources\StaffResource;

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
    public function index()
    {
        try {
            $staffs = Staff::join('users', 'users.id', '=', 'staffs.user_id')->join('roles', 'roles.id', '=', 'staffs.role_id')->select('users.*','users.id as user_id', 'staffs.*','staffs.id as staff_id','roles.name as role_name')->paginate(10);
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
                'code' => 'required|unique:users',
                'email' => 'required|unique:users|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'phone' => 'required|unique:users|regex:/^0[0-9]{9}$/',
                'password' => 'required',
                'role_id' => 'required'
            ];
            $message = [
                'name.required' => 'Vui lòng nhập tên',
                'code.required' => 'Vui lòng nhập mã nhân viên',
                'code.unique' => 'Mã nhân viên đã tồn tại',
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
            $user->code = $request->code;
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
            $staff->role_id = $request->role_id;
            $staff->save();
            // thêm vào bảng model has role để được phân quyền
            $modelHasRole = new ModelHasRole();
            $modelHasRole->role_id = $request->role_id;
            $modelHasRole->model_type = 'App\Models\User';
            $modelHasRole->model_id = $user->id;
            $modelHasRole->save();
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
        $staff = Staff::join('users', 'users.id', '=', 'staffs.user_id')->join('roles', 'roles.id', '=', 'staffs.role_id')->where('staffs.id', '=', $id)->select('staffs.*','users.*','users.id as user_id', 'roles.name as role_name')->first();
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
            $staff = Staff::join('users', 'users.id', 'staffs.user_id')->join('roles', 'roles.id', 'staffs.role_id')->where('staffs.id', '=', $id)->select('staffs.id as staff_id','staffs.role_id as staff_role','users.*', 'roles.name as role_name')->first();
            // dd($staff);
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
            dd($data);
            $staff = Staff::where('id', $id)->firstOrFail();
            $rules = [
                'name' => 'required',
                'code' => 'required|unique:users,code,' . $staff->user_id,
                'email' => 'required|unique:users,email,' . $staff->user_id . '|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'phone' => 'required|unique:users,phone,' . $staff->user_id . '|regex:/^0[0-9]{9}$/',
                'password' => 'required',
                'role_id' => 'required'
            ];
            $message = [
                'name.required' => 'Vui lòng nhập tên',
                'code.required' => 'Vui lòng nhập mã nhân viên',
                'code.unique' => 'Mã nhân viên đã tồn tại',
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
            
            // câph nhật vào staff db
            $staff->role_id = $request->role_id;
            $staff->save();
        
            // câph nhật vào user db
            $user = User::findOrFail($staff->user_id);
            $user->name = $request->name;
            $user->code = $request->code;
            if ($request->hasFile('avatar')) {
                $user->avatar = upload_image($request->file('avatar'));
            }
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
        
            // câph nhật vào bảng model has role để được phân quyền
            $modelHasRole = ModelHasRole::where('model_id', $staff->user_id)->firstOrFail();
            $modelHasRole->role_id = $request->role_id;
            $modelHasRole->save();
        
            // commit
            DB::commit();
        
            // cập nhật thành công thì lấy lại thông tin nhân viên vừa cập nhật
            $staffNew = Staff::join('users', 'users.id', '=', 'staffs.user_id')
                            ->join('roles', 'roles.id', '=', 'staffs.role_id')->where('staffs.id', '=', $id)
                            ->select('staffs.*','users.*','users.id as user_id', 'roles.name as role_name')
                            ->first();
        
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
        if(!$staff){
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy nhân viên cần xóa'
            ], 404);
        }
        if($staff->delete()){
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
}
