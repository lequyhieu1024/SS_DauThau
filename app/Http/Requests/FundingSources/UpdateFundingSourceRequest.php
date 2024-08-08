<?php

namespace App\Http\Requests\FundingSources;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class UpdateFundingSourceRequest extends FormRequest
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
        $currentCode = $id ? DB::table('funding_sources')->where('id', $id)->value('code') : null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                function ($value, $fail) use ($id, $currentCode) {
                    if ($value !== $currentCode && DB::table('funding_sources')->where('code', $value)->where('id', '!=', $id)->exists()) {
                        $fail(__('validation.custom.code.unique'));
                    }
                },
            ],
            'type' => 'required|in:Chính phủ,Tư nhân,Quốc tế',
            'is_active' => 'sometimes|required|boolean',
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
