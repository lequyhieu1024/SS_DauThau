<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportFormRequest;
use App\Http\Resources\SupportCollection;
use App\Repositories\SupportRepository;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected $supportRepository;

    public function __construct(SupportRepository $supportRepository)
    {
//        $this->middleware(['permission:list_support'])->only('index');
//        $this->middleware(['permission:create_support'])->only(['store']);
//        $this->middleware(['permission:update_support'])->only(['update']);
//        $this->middleware(['permission:detail_support'])->only('show');
//        $this->middleware(['permission:destroy_support'])->only('destroy');
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

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
