<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnterpriseFormRequest;
use App\Http\Resources\EnterpriseCollection;
use App\Http\Resources\EnterpriseResource;
use App\Jobs\sendEmailActiveJob;
use App\Repositories\EnterpriseRepository;
use App\Repositories\IndustryRepository;
use App\Repositories\ReputationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EnterpriseController extends Controller
{
    public $enterpriseRepository;
    public $userRepository;
    public $industryRepository;
    public $roleRepository;
    public $reputationRepository;

    public function __construct(
        EnterpriseRepository $enterpriseRepository,
        UserRepository $userRepository,
        IndustryRepository $industryRepository,
        RoleRepository $roleRepository,
        ReputationRepository $reputationRepository
    ) {
        //        $this->middleware(['permission:list_enterprise'])->only('index', 'getnameAndIds');
        //        $this->middleware(['permission:create_enterprise'])->only(['create', 'store']);
        //        $this->middleware(['permission:update_enterprise'])->only(['edit', 'update', 'changeActive', 'banEnterprise']);
        //        $this->middleware(['permission:detail_enterprise'])->only('show');
        //        $this->middleware(['permission:destroy_enterprise'])->only('destroy');


        $this->enterpriseRepository = $enterpriseRepository;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
        $this->roleRepository = $roleRepository;
        $this->reputationRepository = $reputationRepository;
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
            // thêm dữ liệu vào bảng uy tín
            $reputationData = ['enterprise_id' => $enterprise->id];
            $this->reputationRepository->create($reputationData);
            $this->enterpriseRepository->syncIndustry($data, $enterprise->id);
            $data['receiver'] = 'doanh nghiệp';
            unset($data['avatar']);
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
            $this->updateBanReputation($id);
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

    public function moveToBlacklist($id)
    {
        $enterprise = $this->enterpriseRepository->findOrFail($id);
        $this->updateBlacklistReputation($enterprise->is_blacklist, $id);
        $enterprise->is_blacklist = !$enterprise->is_blacklist;
        $enterprise->save();
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái danh sách đen thành công',
            'is_blacklist' => $enterprise->is_blacklist
        ], 200);
    }

    public function updateBlacklistReputation($isBlacklist, $enterpriseId)
    {
        if (!$isBlacklist) {
            $reputation = $this->reputationRepository->getOneBy('enterprise_id', $enterpriseId);
            $reputation->number_of_blacklist = $reputation->number_of_blacklist + 1;
            $reputation->prestige_score = $reputation->prestige_score - 1;
            $reputation->save();
        }
    }

    public function updateBanReputation($enterpriseId)
    {
        $reputation = $this->reputationRepository->getOneBy('enterprise_id', $enterpriseId);
        $reputation->number_of_ban = $reputation->number_of_ban + 1;
        $reputation->prestige_score = $reputation->prestige_score - 1;
        $reputation->save();
    }

    public function getnameAndIds()
    {
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
        ], 200);
    }

    public function employeeQtyStatisticByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê số lượng nhân viên thành công',
            'data' => $this->enterpriseRepository->employeeQtyStatisticByEnterprise($request->all())
        ], 200);
    }

    public function employeeEducationLevelStatisticByEnterprise($id)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê trình độ học vấn của nhân viên thành công',
            'data' => $this->enterpriseRepository->employeeEducationLevelStatisticByEnterprise($id)
        ], 200);
    }

    public function employeeSalaryStatisticByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê lương của nhân viên thành công',
            'data' => $this->enterpriseRepository->employeeSalaryStatisticByEnterprise($request->all())
        ], 200);
    }

    public function employeeWorkingTimeStatisticByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê thời gian gắn bó của nhân viên với doanh nghiệp',
            'data' => $this->enterpriseRepository->employeeWorkingTimeStatisticByEnterprise($request->all())
        ], 200);
    }

    public function employeeAgeStatisticByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê độ tuổi của nhân viên theo doanh nghiệp',
            'data' => $this->enterpriseRepository->employeeAgeStatisticByEnterprise($request->all())
        ], 200);
    }

    public function employeeProjectStatisticByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê dự án đã đăng tải và dự án đã đầu tư của doanh nghiệp',
            'data' => $this->enterpriseRepository->tendererAndInvestorProjectStatisticByEnterprise($request->all())
        ], 200);
    }
    public function biddingResultStatisticsByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê số lượng dự án đã trúng,giá trúng thầu trung bình và tổng giá trị thầu đã trúng của doanh nghiệp',
            'data' => $this->enterpriseRepository->biddingResultStatisticsByEnterprise($request->all())
        ], 200);
    }

    public function averageDifficultyLevelTasksByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thể hiện độ khó trung bình của nhiệm vụ mà doanh nghiệp thực hiện',
            'data' => $this->enterpriseRepository->averageDifficultyLevelTasksByEnterprise($request->all())
        ], 200);
    }

    public function averageDifficultyLevelTasksByEmployee(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thể hiện độ khó trung bình của nhiệm vụ mà nhân viên thực hiện',
            'data' => $this->enterpriseRepository->averageDifficultyLevelTasksByEmployee($request->all())
        ], 200);
    }

    public function averageFeedbackByEmployee(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê đánh giá trung bình của nhân viên',
            'data' => $this->enterpriseRepository->averageFeedbackByEmployee($request->all())
        ], 200);
    }

    public function getDetailEnterpriseByIds(Request $request)
    {
        $rules = [
            'enterprise_ids' => 'required|array',
            'enterprise_ids.*' => 'exists:enterprises,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $enterpriseIds = $request->input('enterprise_ids');

        $enterprises = $this->enterpriseRepository->findWhereIn('id', $enterpriseIds);

        if ($enterprises->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy doanh nghiệp nào.',
                'data' => [],
            ], 404);
        }

        return response([
            'result' => true,
            'message' => "Lấy dữ liệu chi tiết của các doanh nghiệp thành công",
            'data' => EnterpriseResource::collection($enterprises),
        ], 200);
    }

    public function projectCompletedByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê số lượng dự án đã hoàn thành của doanh nghiệp theo từng tháng trong năm',
            'data' => $this->enterpriseRepository->projectCompletedByEnterprise($request->ids, $request->year)
        ], 200);
    }

    public function projectWonByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thống kê số lượng dự án đã trúng thầu của doanh nghiệp theo từng tháng trong năm',
            'data' => $this->enterpriseRepository->projectWonByEnterprise($request->ids, $request->year)
        ], 200);
    }

    public function evaluationsStatisticsByEnterprise(Request $request)
    {
        return response([
            'result' => true,
            'message' => 'Biểu đồ thể hiện số lượng đánh giá và đánh giá trung bình doanh nghiệp nhận được',
            'data' => $this->enterpriseRepository->evaluationsStatisticsByEnterprise($request->ids)
        ], 200);
    }
}
