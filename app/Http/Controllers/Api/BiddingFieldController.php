<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiddingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BiddingFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Define validation rules
        $rules = [
            'limit' => 'integer|min:1',
            'page' => 'integer|min:1',
            'name' => 'string',
            'code' => 'integer|min:1',
            'parent' => 'string',
        ];

        // Validate the request parameters
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }


        $query = BiddingField::query();

        // Search by name
        if ($request->has('name')) {
            $query->where('name', 'like', '%'.$request->input('name').'%');
        }

        // Search by code
        if ($request->has('code')) {
            $query->where('code', $request->input('code'));
        }

        // Search by parent name
        if ($request->has('parent')) {
            $query->whereHas('parent', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->input('parent').'%');
            });
        }

        // Sorting by id in ascending order
        $query->orderBy('id', 'desc');

        // Pagination with dynamic limit and page
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $biddingFields = $query->with('parent')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'isOk' => true,
            'statusCode' => 200,
            'message' => 'Get bidding fields successfully',
            'data' => [
                'bidding_fields' => $biddingFields->items(),
                'page' => $biddingFields->currentPage(),
                'limit' => $biddingFields->perPage(),
                'total_items' => $biddingFields->total(),
                'total_pages' => $biddingFields->lastPage(),
            ],
        ], 200);
    }

//    public function getAllIds()
//    {
//        // Lấy tất cả các id và name từ bảng BiddingField
//        $biddingFields = BiddingField::select('id', 'name')->get();
//
//        // Trả về phản hồi thành công với dữ liệu
//        return response()->json([
//            'isOk' => true,
//            'statusCode' => 200,
//            'message' => 'Get all successful bidding field ids',
//            'data' => $biddingFields,
//        ], 200);
//    }

    public function getAllIds()
    {
        /**
         * Đệ quy xây dựng cấu trúc cây từ một mảng phẳng các phần tử.
         *
         * @param  array  $elements  Mảng phẳng gồm các phần tử, mỗi phần tử phải có khóa 'id', 'name' và 'parent_id'.
         * @param  int|null  $parentId  ID cha để bắt đầu xây dựng cây. Mặc định là null cho cấp gốc.
         * @return array Cấu trúc cây dưới dạng một mảng lồng nhau.
         */
        function buildTree($elements, $parentId = null)
        {
            $branch = [];

            foreach ($elements as $element) {
                if ($element['parent_id'] == $parentId) {
                    $children = buildTree($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                }
            }

            return $branch;
        }

        // Lấy tất cả các bản ghi từ bảng BiddingField, chỉ bao gồm id, name và parent_id
        $biddingFields = BiddingField::select('id', 'name', 'parent_id')->get()->toArray();

        // Xây dựng cấu trúc cây
        $tree = buildTree($biddingFields);

        // Trả về phản hồi thành công với dữ liệu
        return response()->json([
            'isOk' => true,
            'statusCode' => 200,
            'message' => 'Get all successful bidding field ids in tree structure',
            'data' => $tree,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|integer|min:1|unique:bidding_fields,code',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:bidding_fields,id',
        ];

        // Validate the request parameters
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create a new BiddingField record
        $biddingField = BiddingField::create($request->all());

        // Return success response
        return response()->json([
            'isOk' => true,
            'statusCode' => 201,
            'message' => 'Bidding field created successfully',
            'data' => $biddingField,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Validate the id parameter
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Invalid ID parameter',
            ], 400);
        }

        // Retrieve the BiddingField record by id
        $biddingField = BiddingField::with('parent')->find($id);

        // If the record is not found, return a not found error response
        if (!$biddingField) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 404,
                'message' => 'Bidding field not found',
            ], 404);
        }

        // Return success response with the record data
        return response()->json([
            'isOk' => true,
            'statusCode' => 200,
            'message' => 'Bidding field retrieved successfully',
            'data' => $biddingField,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Find the BiddingField record by id
        $biddingField = BiddingField::find($id);

        // If the record is not found, return a not found error response
        if (!$biddingField) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 404,
                'message' => 'Bidding field not found',
            ], 404);
        }

        // Define validation rules
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => 'sometimes|required|integer|min:1|unique:bidding_fields,code,'.$id,
            'is_active' => 'sometimes|required|boolean',
            'parent_id' => 'nullable|exists:bidding_fields,id',
        ];

        // Validate the request parameters
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if parent_id is the same as id
        if ($request->input('parent_id') == $id) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Parent ID cannot be the same as the ID being updated',
            ], 400);
        }

        // Update the BiddingField record with validated data
        $biddingField->update($request->all());

        // Return success response with the updated record data
        return response()->json([
            'isOk' => true,
            'statusCode' => 200,
            'message' => 'Bidding field updated successfully',
            'data' => $biddingField,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        // Validate the id parameter
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 400,
                'message' => 'Invalid ID parameter',
            ], 400);
        }

        // Find the BiddingField record by id
        $biddingField = BiddingField::find($id);

        // If the record is not found, return a not found error response
        if (!$biddingField) {
            return response()->json([
                'isOk' => false,
                'statusCode' => 404,
                'message' => 'Bidding field not found',
            ], 404);
        }

        // Delete the BiddingField record
        $biddingField->delete();

        // Return success response
        return response()->json([
            'isOk' => true,
            'statusCode' => 200,
            'message' => 'Bidding field deleted successfully',
        ], 200);
    }
}
