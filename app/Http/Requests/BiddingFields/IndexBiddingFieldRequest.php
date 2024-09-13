<?php

namespace App\Http\Requests\BiddingFields;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexBiddingFieldRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'size' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'name' => 'nullable|string',
            'code' => 'nullable|integer|min:1',
            'parent_id' => 'nullable|integer|min:1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'size' => $this->query('size'),
            'page' => $this->query('page'),
            'name' => $this->query('name'),
            'code' => $this->query('code'),
            'parent_name' => $this->query('parent_id'),
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
