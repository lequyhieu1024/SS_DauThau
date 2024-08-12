<?php

namespace App\Http\Requests\Common;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexBaseRequest extends FormRequest
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
            'size' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'name' => [
                'nullable',
                'string',
                'not_regex:/<[^>]*script.*?>.*?<\/[^>]*script.*?>/i', // Prevent HTML script tags
                'not_regex:/\b(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|EXEC|UNION|DECLARE|GRANT|REVOKE)\b/i',
                // Prevent SQL keywords
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'size' => $this->query('size'),
            'page' => $this->query('page'),
            'name' => $this->query('name'),
        ]);
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
