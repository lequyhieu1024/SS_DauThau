<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ValidateIdRequest;
use App\Http\Requests\FundingSources\IndexFundingSourceRequest;
use App\Http\Requests\FundingSources\StoreFundingSourceRequest;
use App\Http\Requests\FundingSources\UpdateFundingSourceRequest;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FundingSourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexFundingSourceRequest $request)
    {
        $query = FundingSource::getFilteredFundingSources($request->all());

        $size = $request->query('size', 10);
        $page = $request->query('page', 1);

        $fundingSources = $query->paginate($size, ['*'], 'page', $page);

        $transformedFundingSources = $fundingSources->map(function ($fundingSource) {
            return [
                'id' => $fundingSource->id,
                'name' => $fundingSource->name,
                'description' => $fundingSource->description,
                'code' => $fundingSource->code,
                'type' => $fundingSource->type,
                'is_active' => $fundingSource->is_active,
                'created_at' => $fundingSource->created_at,
                'updated_at' => $fundingSource->updated_at,
            ];
        });
        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách các nguồn tài trợ thành công.',
            'data' => [
                'data' => $transformedFundingSources,
                'total_elements' => $fundingSources->total(),
                'total_pages' => $fundingSources->lastPage(),
                'page_size' => $fundingSources->perPage(),
                // 'number_of_elements' => $fundingSources->count(),
                'current_page' => $fundingSources->currentPage(),
            ],
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFundingSourceRequest $request)
    {
        DB::beginTransaction();

        try {
            $fundingSource = FundingSource::createFundingSource($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tạo mới nguồn tài trợ thành công.',
                'data' => $fundingSource,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tạo mới nguồn tài trợ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fundingSource = FundingSource::findFundingSourceById($id);

        if (!$fundingSource) {
            return response()->json([
                'result' => false,
                'message' => 'Không tìm thấy nguồn tài trợ',
            ], 404);
        };

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin nguồn tài trợ thành công.',
            'data' => $fundingSource,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id ,UpdateFundingSourceRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $fundingSource = FundingSource::updateFundingSource($id, $data);

            if (!$fundingSource) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Nguồn tài trợ không tồn tại',
                ], 404);
            }
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Cập nhật nguồn tài trợ thành công.',
                'data' => $fundingSource,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi cập nhật nguồn tài trợ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $fundingSource = FundingSource::deleteFundingSource($id);

            if (!$fundingSource) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                   'message' => 'Nguồn tài trợ không tồn tại',
                ], 404);
            }

            DB::commit();

            return response()->json([
               'result' => true,
               'message' => 'Xóa nguồn tài trợ thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
               'result' => false,
               'message' => 'Lỗi khi xóa nguồn tài trợ.',
               'error' => $e->getMessage(),
            ],500);
        }
    }
}
