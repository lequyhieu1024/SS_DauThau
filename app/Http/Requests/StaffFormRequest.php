<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;

class StaffFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = Staff::where('id', $this->route('staff'))->pluck('user_id')->first();
        return [
            'name' => 'required',
            'birthday' => 'required',
            'gender' => 'required',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif',
            'taxcode' => [
                'required',
                'regex:/^[0-9]{10,14}$/',
                request()->isMethod('PUT') ? 'unique:users,taxcode,' . $userId : 'unique:users,taxcode'
            ],
            'email' => [
                'required',
                'email',
                request()->isMethod('PUT') ? 'unique:users,email,' . $userId : 'unique:users,email',
                'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/'
            ],
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                request()->isMethod('PUT') ? 'unique:staffs,phone,' . $this->route('staff') : 'unique:staffs,phone'
            ],
            'password' => request()->isMethod('PUT') ? '' : 'required',
            'role_id' => 'required|array|min:1|exists:roles,id',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = [
            'result' => false,
            'status' => 400,
            'message' => $validator->errors()
        ];

        throw new \Illuminate\Validation\ValidationException($validator, response()->json($response, 400));
    }
}
