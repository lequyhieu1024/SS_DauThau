<?php

namespace App\Http\Requests\BiddingFields;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBiddingFieldRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('bidding_field');

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => 'sometimes|required|integer|min:1|unique:bidding_fields,code,'.$id,
//            'is_active' => 'sometimes|required|boolean',
            'parent_id' => 'nullable|exists:bidding_fields,id',
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
