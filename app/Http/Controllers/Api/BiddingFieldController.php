<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingFields\IndexBiddingFieldRequest;
use App\Http\Requests\BiddingFields\StoreBiddingFieldRequest;
use App\Http\Requests\BiddingFields\UpdateBiddingFieldRequest;
use App\Http\Requests\ValidateIdRequest;
use Illuminate\Support\Facades\DB;
use App\Models\BiddingField;

class BiddingFieldController extends Controller
{
    public function index(IndexBiddingFieldRequest $request)
    {
        $query = BiddingField::getFilteredBiddingFields($request->all());

        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $biddingFields = $query->with('parent')->paginate($limit, ['*'], 'page', $page);

        $transformedBiddingFields = $biddingFields->map(function ($biddingField) {
            return [
                'id' => $biddingField->id,
                'name' => $biddingField->name,
                'description' => $biddingField->description,
                'code' => $biddingField->code,
                'is_active' => $biddingField->is_active,
                'created_at' => $biddingField->created_at,
                'updated_at' => $biddingField->updated_at,
                'parent_id' => $biddingField->parent_id,
                'parent_name' => $biddingField->parent ? $biddingField->parent->name : null,
            ];
        });

        return response()->json([
            'result' => true,
            'message' => 'Get bidding fields successfully',
            'data' => [
                'bidding_fields' => $transformedBiddingFields,
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
        DB::beginTransaction();

        try {
            $biddingField = BiddingField::createBiddingField($request->all());

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Bidding field created successfully',
                'data' => $biddingField,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to create bidding field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(ValidateIdRequest $request)
    {
        $id = $request->route('id');
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

        DB::beginTransaction();

        try {
            $updateData = $updateRequest->except('is_active');

            $biddingField = BiddingField::updateBiddingField($id, $updateData);

            if (!$biddingField) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Bidding field not found',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Bidding field updated successfully',
                'data' => $biddingField,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to update bidding field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleActiveStatus(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $biddingField = BiddingField::findBiddingFieldByIdToggleStatus($id);

            if (!$biddingField) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Bidding field not found',
                ], 404);
            }

            $biddingField->is_active = !$biddingField->is_active;
            $biddingField->save();

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Bidding field status toggled successfully',
                'data' => [
                    'is_active' => $biddingField->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to toggle bidding field status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(ValidateIdRequest $request)
    {
        $id = $request->route('id');

        DB::beginTransaction();

        try {
            $biddingField = BiddingField::deleteBiddingField($id);

            if (!$biddingField) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Bidding field not found',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Bidding field deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to delete bidding field',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
