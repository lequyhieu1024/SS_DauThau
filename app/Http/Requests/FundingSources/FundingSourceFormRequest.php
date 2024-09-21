<?php

namespace App\Http\Requests\FundingSources;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class FundingSourceFormRequest extends FormRequest
{
    use HandlesValidationFailures;

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

        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => [
                'required',
                'string',
                'max:100',
                'unique:funding_sources,code,' . $id
            ],
            'type' => 'required|in:Chính phủ,Tư nhân,Quốc tế',
            'is_active' => 'required|boolean',
        ];
    }
}
