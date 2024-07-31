<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingFields\IndexBiddingFieldRequest;
use App\Http\Requests\BiddingFields\StoreBiddingFieldRequest;
use App\Http\Requests\BiddingFields\UpdateBiddingFieldRequest;
use App\Http\Requests\ValidateIdRequest;
use App\Models\BiddingField;

class BiddingFieldController extends Controller
{
    public function index(IndexBiddingFieldRequest $request)
    {
        $query = BiddingField::getFilteredBiddingFields($request->all());

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

    private function buildTree($elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function getAllIds()
    {
        $biddingFields = BiddingField::getAllBiddingFieldIds();

        $tree = $this->buildTree($biddingFields);

        return response()->json([
            'result' => true,
            'message' => 'Get all successful bidding field ids in tree structure',
            'data' => $tree,
        ], 200);
    }

    public function store(StoreBiddingFieldRequest $request)
    {
        $biddingField = BiddingField::createBiddingField($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Bidding field created successfully',
            'data' => $biddingField,
        ], 201);
    }

    public function show(ValidateIdRequest $request, $id)
    {
        $biddingField = BiddingField::findBiddingFieldById($id);

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

    public function update(ValidateIdRequest $request, UpdateBiddingFieldRequest $updateRequest)
    {
        $id = $request->route('id');

        if ($updateRequest->input('parent_id') === $id) {
            return response()->json([
                'result' => false,
                'message' => 'Parent ID cannot be the same as the ID being updated',
            ], 400);
        }

        $biddingField = BiddingField::updateBiddingField($id, $updateRequest->all());

        if (!$biddingField) {
            return response()->json([
                'result' => false,
                'message' => 'Bidding field not found',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Bidding field updated successfully',
            'data' => $biddingField,
        ], 200);
    }

    public function destroy(ValidateIdRequest $request, string $id)
    {
        $biddingField = BiddingField::deleteBiddingField($id);

        if (!$biddingField) {
            return response()->json([
                'result' => false,
                'message' => 'Bidding field not found',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Bidding field deleted successfully',
        ], 200);
    }
}
