<?php

namespace App\Http\Requests\Enterprise;

use App\Validators\FieldValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnterpriseRequest extends FormRequest
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
        return array_merge(parent::rules(), [
            'field_active_id' => [
                'required',
                'integer',
                Rule::exists('bidding_fields', 'id'), // Đảm bảo đây là một khóa ngoại hợp lệ  
            ],
            'user_id' => [
                'required',
                'integer',
                Rule::exists('staffs', 'id'), // Đảm bảo user_id cũng là một khóa ngoại hợp lệ  
            ],
        ]);
    }

    protected function withValidator(Validator $validator): void
    {
        FieldValidator::validateFields($validator, 'enterprises');
    }
}
