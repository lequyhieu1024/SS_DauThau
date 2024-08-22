<?php

namespace App\Http\Requests\BiddingTypes;

use Illuminate\Foundation\Http\FormRequest;

class IndexBiddingTypeRequest extends FormRequest
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
            'name' => 'nullable|string|min:1',
        ];
    }
}