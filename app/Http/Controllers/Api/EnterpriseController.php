<?php

namespace App\Http\Controllers\Api;

use App\Jobs\sendEmailActiveJob;
use App\Models\Enterprise;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\IndustryRepository;
use App\Http\Resources\EnterpriseResource;
use App\Repositories\EnterpriseRepository;
use App\Http\Requests\EnterpriseFormRequest;
use App\Http\Resources\EnterpriseCollection;

class EnterpriseController extends Controller
{
    public $enterpriseRepository;
    public $userRepository;
    public $industryRepository;
    public $roleRepository;

    public function __construct(EnterpriseRepository $enterpriseRepository, UserRepository $userRepository, IndustryRepository $industryRepository, RoleRepository $roleRepository)
    {
        // $this->middleware(['permission:list_staff'])->only('index');
        // $this->middleware(['permission:create_staff'])->only(['create', 'store']);
        // $this->middleware(['permission:update_staff'])->only(['edit', 'update']);
        // $this->middleware(['permission:detail_staff'])->only('show');
        // $this->middleware(['permission:destroy_staff'])->only('destroy');
        $this->enterpriseRepository = $enterpriseRepository;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
        $this->roleRepository = $roleRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $enterprises = $this->enterpriseRepository->filter($request->all());
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách doanh nghiệp thành công',
            'data' => new EnterpriseCollection($enterprises),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnterpriseFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = $this->userRepository->create($data)->syncRoles($this->roleRepository->getNameById($data['organization_type'] == 1 ? [13] : [14])); //13 là doanh nghiệp nhà nước / 14 là ngoài nhà nước
            $data['user_id'] = $user->id;
            if ($request->hasFile('avatar')) {
                $data['avatar'] = upload_image($request->file('avatar'));
            }
            $enterprise = $this->enterpriseRepository->create($data);
            $this->enterpriseRepository->syncIndustry($data, $enterprise->id);
            $data['receiver'] = 'doanh nghiệp';
            sendEmailActiveJob::dispatch($data);
            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo doanh nghiệp thành công",
                "data" => new EnterpriseResource($this->enterpriseRepository->find($enterprise->id))
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Tạo doanh nghiệp không thành công." . $th,
                "data" => $request->all(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $enterprise = $this->enterpriseRepository->find($id);
        if (!$enterprise) {
            return response()->json([
                'result' => false,
                'status' => 404,
                'message' => 'Không tìm thấy doanh nghiệp'
            ], 404);
        } else {
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Lấy doanh nghiệp thành công',
                'data' => new EnterpriseResource($enterprise),
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnterpriseFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = $this->userRepository->update($data, $this->enterpriseRepository->findOrFail($id)->user_id);
            $this->userRepository->findOrFail($this->enterpriseRepository->findOrFail($id)->user_id)->syncRoles($this->roleRepository->getNameById($data['organization_type'] == 1 ? [13] : [14]));
            if ($request->hasFile('avatar')) {
                $data['avatar'] = upload_image($request->file('avatar'));
                isset($this->enterpriseRepository->findOrFail($id)->avatar) ? unlink($this->enterpriseRepository->findOrFail($id)->avatar) : "";
            } else {
                $data['avatar'] = $this->enterpriseRepository->findOrFail($id)->avatar;
            }
            $this->enterpriseRepository->update($data, $id);
            $this->enterpriseRepository->syncIndustry($data, $id);
            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật doanh nghiệp thành công",
                "data" => new EnterpriseResource($this->enterpriseRepository->findOrFail($id))
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Cập nhật doanh nghiệp không thành công. Error : " . $th,
                "data" => $request->all(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->userRepository->delete($this->enterpriseRepository->findOrFail($id)->user_id);
            $this->enterpriseRepository->delete($id);
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Xóa doanh nghiệp thành công'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'status' => 400,
                'message' => 'Xóa doanh nghiệp thất bại, lỗi : ' . $th
            ], 400);
        }
    }
    public function banEnterprise($id)
    {
        $user = $this->userRepository->findOrFail($this->enterpriseRepository->findOrFail($id)->user_id);
        if ($user->account_ban_at == null) {
            $user->account_ban_at = now();
            $user->save();
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Khóa tài khoản thành công'
            ], 200);
        } else {
            $user->account_ban_at = null;
            $user->save();
            return response()->json([
                'result' => true,
                'status' => 200,
                'message' => 'Mở khóa tài khoản thành công'
            ], 200);
        }
    }


    public function changeActive($id)
    {
        $enterprise = $this->enterpriseRepository->findOrFail($id);
        $enterprise->is_active = !$enterprise->is_active;
        $enterprise->save();
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $enterprise->is_active
        ], 200);
    }

    public function getnameAndIds(){
        $enterprises = $this->enterpriseRepository->getAllNotPaginate();
        return response()->json([
            'result' => true,
            'message' => "Lấy danh sách doanh nghiệp thành công",
            'data' => $enterprises->map(function ($enterprise) {
                return [
                    'id' => $enterprise->id,
                    'name' => $enterprise->user->name
                ];
            })
        ],200);
    }
}
