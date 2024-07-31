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
        ]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
