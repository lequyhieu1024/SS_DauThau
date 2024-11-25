<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
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
                "data" => $data
            ], 200);
        } else {
            return response()->json([
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
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required',
            'logo' => 'required',
            'phone' => 'required|regex:/^(\+84|0)(\s?\d{3}|\s?\d{4}|\s?\d{5})(\s?\d{3,4}){2}$/',

            'email' => 'required|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
            'address' => 'required',
        ];
        $messages = [
            'name.required' => 'Tên website không được bỏ trống',
            'logo.required' => 'Logo website không được để trống',
            'phone.required' => 'Số điện thoại website không được để trống',
            'email.required' => 'Email không được bỏ trống',
            'address.required' => 'Địa chỉ không được bỏ trống',
            'phone.regex' => 'Số điện thoại sai định dạng',
            'email.regex' => 'Email phải đúng định dạng example@ex.com',
        ];

        $validator = validator($data, $rules, $messages);

        if ($validator->fails()) {
            return response([
                'result' => false,
                "message" => $validator->errors()
            ], 200);
        } else {
            $system = System::find($id);
            $system->name = $data['name'];
            if ($request->hasFile('logo')) {
                $system->logo = upload_image($request->file('logo'));
            }
            $system->phone = $data['phone'];
            $system->email = $data['email'];
            $system->address = $data['address'];
            $system->save();
            return response()->json([
                'result' => true,
                "message" => "Cập nhật thành công",
                "data" => $system
            ], 200);
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
}
