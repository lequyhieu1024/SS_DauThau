<?php

namespace App\Http\Requests\BiddingTypes;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class UpdateBiddingTypeRequest extends FormRequest
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
        $id = $this->route('id');
        $currentName = $id ? DB::table('bidding_types')->where('id', $id)->value('name') : null;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($id, $currentName) {
                    if ($value !== $currentName && DB::table('bidding_types')->where('name', $value)->where('id', '!=', $id)->exists()) {
                        $fail(__('validation.unique'));
                    }
                }
            ],
            'description' => 'sometimes|required|string',
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
