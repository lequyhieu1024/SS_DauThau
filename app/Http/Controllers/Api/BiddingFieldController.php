<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingFields\IndexBiddingFieldRequest;
use App\Http\Requests\BiddingFields\StoreBiddingFieldRequest;
use App\Http\Requests\BiddingFields\UpdateBiddingFieldRequest;
use App\Http\Requests\ValidateIdRequest;
use Illuminate\Support\Facades\DB;
use App\Models\BiddingField;
use OpenApi\Annotations as OA;

class BiddingFieldController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/bidding-fields",
     *     tags={"Bidding Field"},
     *     summary="Get all bidding fields",
     *     description="Get all bidding fields",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Limit items per page",
     *     required=false,
     *     @OA\Schema(
     *     type="integer",
     *     default=10
     *     )
     *    ),
     *     @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     required=false,
     *     @OA\Schema(
     *     type="integer",
     *     default=1
     *     )
     *    ),
     *     @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Name of bidding field",
     *     required=false,
     *     @OA\Schema(
     *     type="string"
     *    )
     *   ),
     *     @OA\Parameter(
     *     name="code",
     *     in="query",
     *     description="Code of bidding field",
     *     required=false,
     *     @OA\Schema(
     *     type="string"
     *   )
     * ),
     *     @OA\Parameter(
     *     name="parent_name",
     *     in="query",
     *     description="Parent name of bidding field",
     *     required=false,
     *     @OA\Schema(
     *     type="string"
     *  )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Get bidding fields successfully",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="result",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Get bidding fields successfully"
     *    ),
     *     @OA\Property(
     *     property="data",
     *     type="object",
     *     @OA\Property(
     *     property="biddingFields",
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Bidding Field 1"
     *    ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Description of bidding field 1"
     *   ),
     *     @OA\Property(
     *     property="code",
     *     type="string",
     *     example="BF1"
     *  ),
     *     @OA\Property(
     *     property="is_active",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     *   ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     * ),
     *     @OA\Property(
     *     property="parent_id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="parent_name",
     *     type="string",
     *     example="Parent Bidding Field 1"
     *   )
     * )
     * ),
     *     @OA\Property(
     *     property="pagination",
     *     type="object",
     *     @OA\Property(
     *     property="currentPage",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="pageSize",
     *     type="integer",
     *     example=10
     *     ),
     *     @OA\Property(
     *     property="totalItems",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="totalPages",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="hasNextPage",
     *     type="boolean",
     *     example=false
     *     ),
     *     @OA\Property(
     *     property="hasPreviousPage",
     *     type="boolean",
     *     example=false
     *     )
     * )
     * )
     * )
     * ),
     * )
     *
     *
     *
     */
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
                'biddingFields' => $transformedBiddingFields,
                'pagination' => [
                    'currentPage' => $biddingFields->currentPage(),
                    'pageSize' => $biddingFields->perPage(),
                    'totalItems' => $biddingFields->total(),
                    'totalPages' => $biddingFields->lastPage(),
                    'hasNextPage' => $biddingFields->hasMorePages(),
                    'hasPreviousPage' => $biddingFields->currentPage() > 1,
                ],
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

    /**
     * @OA\Get(
     *     path="/api/admin/bidding-fields/all-ids",
     *     tags={"Bidding Field"},
     *     summary="Get all bidding field ids",
     *     description="Get all bidding field ids",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *     response=200,
     *     description="Get all successful bidding field ids in tree structure",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="result",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Get all successful bidding field ids in tree structure"
     *   ),
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1
     *     ),
     *     ),
     *     )
     *    )
     *  )
     * )
     *
     */
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


    /**
     * @OA\Post(
     *     path="/api/admin/bidding-fields",
     *     tags={"Bidding Field"},
     *     summary="Create a new bidding field",
     *     description="Create a new bidding field",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *     required={"name", "description", "code", "is_active"},
     *     nullable={"parent_id"},
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Bidding Field 1"
     *    ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Description of bidding field 1"
     *   ),
     *     @OA\Property(
     *     property="code",
     *     type="number",
     *     example="666"
     *  ),
     *     @OA\Property(
     *     property="is_active",
     *     type="number",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="parent_id",
     *     type="number",
     *     example=1
     *     )
     *   )
     * ),
     *     @OA\Response(
     *     response=201,
     *     description="Bidding field created successfully",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="result",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Bidding field created successfully"
     *   ),
     *     @OA\Property(
     *     property="data",
     *     type="object",
     *     @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Bidding Field 1"
     *   ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Description of bidding field 1"
     * ),
     *     @OA\Property(
     *     property="code",
     *     type="number",
     *     example="666"
     * ),
     *     @OA\Property(
     *     property="is_active",
     *     type="number",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     *  ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     * ),
     *     @OA\Property(
     *     property="parent_id",
     *     type="integer",
     *     example=1
     *     ),
     *    ),
     *     )
     *   )
     * )
     *
     *
     *
     *
     */
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

    /**
     * @OA\Get(
     *     path="/api/admin/bidding-fields/{id}",
     *     tags={"Bidding Field"},
     *     summary="Get bidding field by ID",
     *     description="Get bidding field by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of bidding field",
     *     required=true,
     *     @OA\Schema(
     *     type="integer"
     *   )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Bidding field retrieved successfully",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="result",
     *     type="boolean",
     *     example=true
     *     ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Bidding field retrieved successfully"
     *  ),
     *     @OA\Property(
     *     property="data",
     *     type="object",
     *     @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Bidding Field 1"
     *  ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Description of bidding field 1"
     * ),
     *     @OA\Property(
     *     property="code",
     *     type="number",
     *     example="666"
     * ),
     *     @OA\Property(
     *     property="is_active",
     *     type="number",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     * ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00.000000Z"
     * ),
     *     @OA\Property(
     *     property="parent_id",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="parent_name",
     *     type="string",
     *     example="Parent Bidding Field 1"
     *  )
     * )
     * )
     * )
     * )
     *
     */
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
        ], 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/bidding-fields/{id}",
     *     tags={"Bidding Field"},
     *     summary="Update bidding field by ID",
     *     description="Update bidding field by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of bidding field",
     *     required=true,
     *     @OA\Schema(
     *     type="integer"
     *  )
     * ),
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *     nullable={"parent_id"},
     *     @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Bidding Field 1"
     *   ),
     *     @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Description of bidding field 1"
     * ),
     *     @OA\Property(
     *     property="code",
     *     type="number",
     *     example="666"
     * ),
     *     @OA\Property(
     *     property="is_active",
     *     type="number",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="parent_id",
     *     type="number",
     *     example=1
     *     )
     *  )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Bidding field updated successfully",
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/admin/bidding-fields/{id}/toggle-status",
     *     tags={"Bidding Field"},
     *     summary="Toggle active status of bidding field by ID",
     *     description="Toggle active status of bidding field by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of bidding field",
     *     required=true,
     *     @OA\Schema(
     *     type="integer"
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Bidding field status toggled successfully",
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/admin/bidding-fields/{id}",
     *     tags={"Bidding Field"},
     *     summary="Delete bidding field by ID",
     *     description="Delete bidding field by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of bidding field",
     *     required=true,
     *     @OA\Schema(
     *     type="integer"
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Bidding field deleted successfully",
     *     )
     * )
     */
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
