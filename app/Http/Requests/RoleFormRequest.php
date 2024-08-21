<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleFormRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', request()->isMethod('PUT') ? 'unique:roles,name,' . $this->route('role') : 'unique:roles,name'],
            'permissions' => ['required', 'array'],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên quyền',
            'permissions.required' => 'Chọn ít nhất 1 quyền'
        ];
    }

    // Tùy chỉnh phản hồi lỗi
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