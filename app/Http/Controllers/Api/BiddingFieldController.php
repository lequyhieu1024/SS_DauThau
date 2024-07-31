<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiddingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BiddingFieldController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'limit' => 'integer|min:1',
            'page' => 'integer|min:1',
            'name' => 'string',
            'code' => 'integer|min:1',
            'parent' => 'string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $query = BiddingField::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%'.$request->input('name').'%');
        }

        if ($request->has('code')) {
            $query->where('code', $request->input('code'));
        }

        if ($request->has('parent')) {
            $query->whereHas('parent', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->input('parent').'%');
            });
        }

        $query->orderBy('id', 'desc');

        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $biddingFields = $query->with('parent')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'result' => true,
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

        $biddingFields = BiddingField::select('id', 'name', 'parent_id')->get()->toArray();

        $tree = buildTree($biddingFields);

        return response()->json([
            'result' => true,
            'message' => 'Get all successful bidding field ids in tree structure',
            'data' => $tree,
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|integer|min:1|unique:bidding_fields,code',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:bidding_fields,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $biddingField = BiddingField::create($request->all());

        // Return success response
        return response()->json([
            'result' => true,
            'message' => 'Bidding field created successfully',
            'data' => $biddingField,
        ], 201);
    }

    public function show($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid ID parameter',
            ], 400);
        }

        $biddingField = BiddingField::with('parent')->find($id);

        if (!$biddingField) {
            return response()->json([
                'result' => false,
                'message' => 'Bidding field not found',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Bidding field retrieved successfully',
            'data' => $biddingField,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $biddingField = BiddingField::find($id);

        if (!$biddingField) {
            return response()->json([
                'result' => false,
                'message' => 'Bidding field not found',
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => 'sometimes|required|integer|min:1|unique:bidding_fields,code,'.$id,
            'is_active' => 'sometimes|required|boolean',
            'parent_id' => 'nullable|exists:bidding_fields,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->input('parent_id') == $id) {
            return response()->json([
                'result' => false,
                'message' => 'Parent ID cannot be the same as the ID being updated',
            ], 400);
        }

        $biddingField->update($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Bidding field updated successfully',
            'data' => $biddingField,
        ], 200);
    }

    public function destroy(string $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid ID parameter',
            ], 400);
        }

        $biddingField = BiddingField::find($id);

        if (!$biddingField) {
            return response()->json([
                'result' => false,
                'message' => 'Bidding field not found',
            ], 404);
        }

        $biddingField->delete();

        return response()->json([
            'result' => true,
            'message' => 'Bidding field deleted successfully',
        ], 200);
    }
}
