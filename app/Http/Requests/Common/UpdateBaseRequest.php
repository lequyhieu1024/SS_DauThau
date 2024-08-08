<?php

namespace App\Http\Requests\Common;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBaseRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'not_regex:/<[^>]*script.*?>.*?<\/[^>]*script.*?>/i', // Prevent HTML script tags
                'not_regex:/\b(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|EXEC|UNION|DECLARE|GRANT|REVOKE)\b/i',
                // Prevent SQL keywords
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'not_regex:/<[^>]*script.*?>.*?<\/[^>]*script.*?>/i', // Prevent HTML script tags
                'not_regex:/\b(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|EXEC|UNION|DECLARE|GRANT|REVOKE)\b/i',
                // Prevent SQL keywords
            ],
            'is_active' => 'sometimes|required|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'result' => false,
            'message' => 'Lỗi xác thực',
            'errors' => $validator->errors(),
        ], 400));
    }
}
