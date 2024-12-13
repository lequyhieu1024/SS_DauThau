<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeFormRequest extends FormRequest
{
    use HandlesValidationFailures;
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
            'enterprise_id' => 'required|exists:enterprises,id',
            'code' => [
                'required', 'string',
                request()->isMethod('POST')
                    ? 'unique:employees,code'
                    : 'unique:employees,code,' . $this->route('employee')
            ],
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable', 'string', 'max:20', 'regex:/^(\(\+84\)|\+84|\(0\)|0)(\s?\d{3}|\s?\d{4}|\s?\d{5})(\s?\d{3,4}){2}$/',
                request()->isMethod('POST')
                    ? 'unique:employees,phone'
                    : 'unique:employees,phone,' . $this->route('employee')
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                request()->isMethod('POST')
                    ? 'unique:employees,email'
                    : 'unique:employees,email,' . $this->route('employee')
            ],
            'birthday' => 'nullable|date',
            'gender' => 'required|boolean',
            'taxcode' => [
                'nullable', 'string', 'max:20',
                request()->isMethod('POST')
                    ? 'unique:employees,taxcode'
                    : 'unique:employees,taxcode,' . $this->route('employee')
            ],
            'education_level' => 'required|in:primary_school,secondary_school,high_school,college,university,after_university',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:doing,pause,leave',
        ];
    }

}
