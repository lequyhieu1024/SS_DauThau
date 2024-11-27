<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructFormRequest;
use App\Http\Resources\InstructResource;
use App\Http\Resources\InstructCollection;
use App\Repositories\InstructRepository;
use Illuminate\Http\Request;

class InstructController extends Controller
{
    protected $instructRepository;
    public function __construct(InstructRepository $instructRepository){
         $this->middleware(['permission:list_instruct'])->only('index');
         $this->middleware(['permission:create_instruct'])->only('store');
         $this->middleware(['permission:update_instruct'])->only('update', 'changeActive');
         $this->middleware(['permission:detail_instruct'])->only('edit');
         $this->middleware(['permission:destroy_instruct'])->only('destroy');

        $this->instructRepository = $instructRepository;

    }
    public function index(Request $req){
        $instructs = $this->instructRepository->filter($req->all());
        if(empty($instructs)){
            return response([
                'result' => true,
                'message' => "Chưa có hướng dẫn nào!",
                'data' => []
            ], 200);
        }
        return response([
            'result' => true,
            'message' => "Lấy danh sách hướng dẫn thành công!",
            'data' => new InstructCollection($instructs)
        ],200);
    }

    public function store(InstructFormRequest $req){
        try {
            $totalCount = $this->instructRepository->countAll();
            $data = $req->all();
            if ($totalCount > 0 && $data['is_use'] == "1") {
                return response([
                    'result' => false,
                    'message' => "Không thể tạo mới hướng dẫn với trạng thái is_use = 1 vì đã có một bản ghi đang được sử dụng."
                ], 400);
            }

            $data1 = $this->instructRepository->create($req->all());
            return response([
                'result' => true,
                'message' => "Tạo mới hướng dẫn thành công!",
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
        return response([
            'result' => true,
            'message' => "Lấy phần giới thiệu thành công",
            'data' => new InstructResource($this->instructRepository->findOrFail($id)),
        ], 200);
    }

    public function getInstructLandipage()
    {
        return response([
            'result' => true,
            'message' => "Lấy phần giới thiệu thành công",
            'data' => empty($this->instructRepository->getInstructLandipage()) ? null : new InstructResource($this->instructRepository->getInstructLandipage()),
        ], 200);
    }

    public function edit(string $id){
        $instruct = $this->instructRepository->findOrFail($id);
        if(empty($instruct)){
            return response([
                'result' => true,
                'message' => "hướng dẫn không tồn tại!",
                'data' => []
            ], 404);
        }
        return response([
            'result' => true,
            'message' => "Lấy hướng dẫn thành công!",
            'data' => new InstructResource($instruct)
        ], 200);
    }

    public function update(InstructFormRequest $req, string $id){
        try{
            $totalCount = $this->instructRepository->countAll();

            $currentInstruct = $this->instructRepository->findOrFail($id);

            $data = $req->all();
            if ($totalCount > 0 && $data['is_use'] == "1" && $currentInstruct->is_use !== 1) {
                return response([
                    'result' => false,
                    'message' => "Không thể cập nhật hướng dẫn với trạng thái is_use = 1 vì đã có một bản ghi đang được sử dụng."
                ], 400);
            }
            $this->instructRepository->update($req->all(), $id);
            return response([
                'result' => true,
                'message' => "Cập nhật hướng dẫn thành công!",
                'data' => new InstructResource($this->instructRepository->findOrFail($id))
            ], 200);
        } catch (\Throwable $e) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }

    public function destroy(string $id){
        $instruct = $this->instructRepository->findOrFail($id);

        if ($instruct->is_use) {
            return response([
                'result' => false,
                'message' => "instruct đang được sử dụng nên không thể xóa."
            ], 400);
        }

        $totalCount = $this->instructRepository->countAll();
        if ($totalCount <= 1) {
            return response([
                'result' => false,
                'message' => "Phải có ít nhất 1 instruct."
            ], 400);
        }

        $this->instructRepository->delete($id);
        return response([
            'result' => true,
            'message' => "Xóa hướng dẫn thành công",
        ], 200);
    }

    public function changeActive($id){
        $activeCount = $this->instructRepository->countActive();

        if ($activeCount != "1") {
            return response([
                'result' => false,
                'status' => 400,
                'message' => 'Bắt buộc phải có 1 instruct được sử dụng'
            ], 400);
        }

        $currentActiveInstruct = $this->instructRepository->getCurrentActive();

        $instruct = $this->instructRepository->findOrFail($id);

        $instruct->is_use = !$instruct->is_use;
        $instruct->save();

        $currentActiveInstruct->is_use = !$currentActiveInstruct->is_use;
        $currentActiveInstruct->save();

        return response([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $instruct->is_use
        ], 200);
    }
}
