<?php

namespace App\Http\Controllers\Api;

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

    public function __construct(EnterpriseRepository $enterpriseRepository, UserRepository $userRepository, IndustryRepository $industryRepository)
    {
        $this->enterpriseRepository = $enterpriseRepository;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
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
            $user = $this->userRepository->create($data);
            $data['user_id'] = $user->id;
            $enterprise = $this->enterpriseRepository->create($data);
            $industry = $this->enterpriseRepository->syncIndustry($data, $enterprise->id);
            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo doanh nghiệp thành công",
                "data" => $enterprise
            ], 201);
        } catch (\Throwable $th) {
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
        $industries = $this->industryRepository->getAllNotPaginate();
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
                'industries' => $industries,
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
            $this->userRepository->update($data, $this->enterpriseRepository->findOrFail($id)->user_id);
            $this->enterpriseRepository->update($data, $id);
            $this->enterpriseRepository->syncIndustry($data, $id);
            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật doanh nghiệp thành công",
                "data" => new EnterpriseResource($this->enterpriseRepository->findOrFail($id))
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "result" => false,
                "message" => "Cập nhật doanh nghiệp không thành công." . $th,
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
}
