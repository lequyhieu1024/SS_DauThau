<?php

namespace App\Http\Requests\BiddingFields;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class UpdateBiddingFieldRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        $currentCode = $id ? DB::table('bidding_fields')->where('id', $id)->value('code') : null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($id, $currentCode) {
                    if ($value !== $currentCode && DB::table('bidding_fields')->where('code', $value)->where('id', '!=',
                            $id)->exists()) {
                        $fail(__('validation.custom.code.unique'));
                    }
                },
            ],
            'is_active' => 'sometimes|required|boolean',
            'parent_id' => [
                'nullable',
                'exists:bidding_fields,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail(__('validation.custom.parent_id.same_as_current'));
                    }
                },
            ],
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
