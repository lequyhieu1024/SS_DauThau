<?php

namespace App\Http\Requests\BiddingFields;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BiddingFieldFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'name' => $this->isMethod('post') ? 'required|string|max:255' : 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'code' => [
                'required',
                'integer',
                'min:1',
                'unique:bidding_fields,code,' . $id,
            ],
            'is_active' => 'required|boolean',
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
