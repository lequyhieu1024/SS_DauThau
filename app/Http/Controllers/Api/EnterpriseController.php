<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enterprise\IndexEnterpriseRequest;
use App\Http\Resources\Common\IndexBaseCollection;
use App\Models\Enterprise;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexEnterpriseRequest $request)
    {
        $enterprises = Enterprise::searchEnterprise(
            $request->query('name'),
            $request->query('representative_name'),
            $request->query('address'),
            $request->query('establish_date'),
            $request->query('is_active'),
            $request->query('is_blacklist'),
            $request->query('field_active_id'),
            $request->query('page', 1),
            $request->query('size', 10)
        );
        $data = new IndexBaseCollection($enterprises);

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách ngành nghề thành công',
            'data' => $data,
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
