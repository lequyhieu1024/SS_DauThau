<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportFormRequest;
use App\Http\Resources\SupportCollection;
use App\Jobs\SendSupportRequestMailJob;
use App\Repositories\SupportRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    protected $supportRepository;

    public function __construct(SupportRepository $supportRepository)
    {
        $this->middleware(['permission:list_support'])->only('index');
        $this->middleware(['permission:create_support'])->only(['store']);
        $this->middleware(['permission:update_support'])->only(['update']);
        $this->middleware(['permission:detail_support'])->only('show');
        $this->middleware(['permission:destroy_support'])->only('destroy');
        $this->supportRepository = $supportRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách thư hỗ trợ thành công',
            'data' => new SupportCollection($this->supportRepository->filter($request->all())),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupportFormRequest $request)
    {
        $data = $request->all();
        if (isset($data['document'])) {
            $data['document'] = upload_file($data['document']);
        }
        $this->supportRepository->create($data);
        return response([
            'result' => true,
            'message' => 'Gữi thư hỗ trợ thành công',
            'data' => $data,
        ], 201);
    }

    public function createSupportLandipage(SupportFormRequest $request)
    {
        $data = $request->all();
        if (isset($data['document'])) {
            $data['document'] = upload_file($data['document']);
        }
        $this->supportRepository->create($data);
        SendSupportRequestMailJob::dispatch($data);
        return response([
            'result' => true,
            'message' => 'Gửi thư hỗ trợ thành công',
            'data' => $data,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return response([
                'result' => true,
                'message' => "Xem chi tiết thư hỗ trợ thành công",
                'data' => $this->supportRepository->find($id),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy thư hỗ trợ này',
                'data' => [],
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $rules = [
                'status' => 'required|in:sent,processing,responded',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response([
                    'result' => false,
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }
            $this->supportRepository->update($request->all(), $id);
            return response([
                'result' => true,
                'message' => 'Cập nhật thành công',
                'data' => $this->supportRepository->find($id)
            ], 200);
        } catch (ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy thư hỗ trợ cần cập nhật',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->supportRepository->delete($id);
            return response([
                'result' => true,
                'message' => 'Xóa thư hỗ trợ thành công'
            ], 200);
        } catch (ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy thư hỗ trợ cần xóa',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            return response([
                'result' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
