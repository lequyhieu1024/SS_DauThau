<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemFormRequest;
use App\Http\Resources\SystemResource;
use App\Models\System;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:list_system'])->only('index');
        $this->middleware(['permission:update_system'])->only(['update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = System::where('id', 1)->first();
        if ($data) {
            return response([
                'result' => true,
                "message" => "Lấy thông tin hệ thống thành công",
                "data" => new SystemResource($data),
            ], 200);
        } else {
            return response([
                'result' => false,
                'message' => 'Lấy thông tin hệ thống thất bại',
                'data' => []
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SystemFormRequest $request, $id)
    {
        try {
            $system = System::find($id);
            $data = $request->all();
            if ($request->hasFile('logo')) {
                $data['logo'] = upload_image($request->file('logo'));
            }
            $system->update($data);
            return response([
                'result' => true,
                "message" => "Cập nhật thông tin hệ thống thành công",
                "data" => new SystemResource($system)
            ], 200);
        }catch (\Exception $exception){
            return response([
                'result' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getDataSystem()
    {
        $data = System::where('id', 1)->first();
        if ($data) {
            return response([
                'result' => true,
                "message" => "Lấy thông tin hệ thống thành công",
                "data" => $data
            ], 200);
        } else {
            return response([
                'result' => false,
                'message' => 'Chưa có thông tin hệ thống',
                'data' => []
            ], 400);
        }
    }
}
