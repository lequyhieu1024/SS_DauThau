<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Introduction;
use App\Http\Requests\IntroductionFormRequest;
use App\Http\Resources\IntroductionResource;
use App\Http\Resources\IntroductionCollection;
use App\Repositories\IntroductionRespository;
use Illuminate\Http\Request;

class IntroductionController extends Controller
{
    protected $introductionRespository;
    public function __construct(IntroductionRespository $introductionRespository){
        $this->middleware(['permission:list_introduction'])->only('index');
        $this->middleware(['permission:create_introduction'])->only('store');
        $this->middleware(['permission:update_introduction'])->only('update', 'changeActive');
        $this->middleware(['permission:detail_introduction'])->only('edit');
        $this->middleware(['permission:destroy_introduction'])->only('destroy');

        $this->introductionRespository = $introductionRespository;

    }

    public function index(Request $req){
        $introductions = $this->introductionRespository->filter($req->all());
        if(empty($introductions)){
            return response([
                'result' => true,
                'message' => "Chưa có giới thiệu nào!",
                'data' => []
            ], 200);
        }
        return response([
            'result' => true,
            'message' => "Lấy danh sách giới thiệu thành công!",
            'data' => new IntroductionCollection($introductions)
        ],200);
    }

    public function store(IntroductionFormRequest $req){
        try {
            $totalCount = $this->introductionRespository->countAll();
            $data = $req->all();
            if ($totalCount > 0 && $data['is_use'] == "1") {
                return response([
                    'result' => false,
                    'message' => "Không thể tạo mới giới thiệu với trạng thái is_use = 1 vì đã có một bản ghi đang được sử dụng."
                ], 400);
            }

            $data1 = $this->introductionRespository->create($req->all());
            return response([
                'result' => true,
                'message' => "Tạo mới giới thiệu thành công!",
                'data' => $data1
            ], 201);
        } catch(\Throwable $th){
            return response([
                'result' => true,
                'message' => "Có lỗi từ serve",
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id){
        $introduction = $this->introductionRespository->findOrFail($id); 
        if(empty($introduction)){
            return response([
                'result' => true,
                'message' => "Giới thiệu không tồn tại!",
                'data' => []
            ], 404);
        }
        return response([
            'result' => true,
            'message' => "Lấy giới thiệu thành công!",
            'data' => new IntroductionResource($introduction)
        ], 200);
    }

    public function update(IntroductionFormRequest $req, string $id){
        try{
            $totalCount = $this->introductionRespository->countAll();
        
            $currentIntroduction = $this->introductionRespository->findOrFail($id);

            $data = $req->all();
            if ($totalCount > 0 && $data['is_use'] == "1" && $currentIntroduction->is_use !== 1) {
                return response([
                    'result' => false,
                    'message' => "Không thể cập nhật giới thiệu với trạng thái is_use = 1 vì đã có một bản ghi đang được sử dụng."
                ], 400);
            }
            $this->introductionRespository->update($req->all(), $id);
            return response([
                'result' => true,
                'message' => "Cập nhật giới thiệu thành công!",
                'data' => new IntroductionResource($this->introductionRespository->findOrFail($id))
            ], 200);
        } catch (\Throwable $e) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }

    public function destroy(string $id){
        $introduction = $this->introductionRespository->findOrFail($id);

        if ($introduction->is_use) {
            return response([
                'result' => false,
                'message' => "Introduction đang được sử dụng nên không thể xóa."
            ], 400);
        }

        $totalCount = $this->introductionRespository->countAll();
        if ($totalCount <= 1) {
            return response([
                'result' => false,
                'message' => "Phải có ít nhất 1 introduction."
            ], 400);
        }

        $this->introductionRespository->delete($id);
        return response([
            'result' => true,
            'message' => "Xóa giới thiệu thành công",
        ], 200);
    }

    public function changeActive($id){
        $activeCount = $this->introductionRespository->countActive();

        if ($activeCount != "1") {
            return response([
                'result' => false,
                'status' => 400,
                'message' => 'Bắt buộc phải có 1 introduction được sử dụng'
            ], 400);
        }

        $currentActiveIntroduction = $this->introductionRespository->getCurrentActive();

        $introduction = $this->introductionRespository->findOrFail($id);

        $introduction->is_use = !$introduction->is_use;
        $introduction->save();

        $currentActiveIntroduction->is_use = !$currentActiveIntroduction->is_use;
        $currentActiveIntroduction->save();

        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $introduction->is_use
        ], 200);
    }
}
