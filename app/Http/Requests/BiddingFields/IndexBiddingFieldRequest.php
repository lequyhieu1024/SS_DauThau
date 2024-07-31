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
            'limit' => 'integer|min:1',
            'page' => 'integer|min:1',
            'name' => 'string',
            'code' => 'integer|min:1',
            'parent' => 'string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'result' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 400));
    }
}
