<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class ProcurementCategoryFormRequest extends FormRequest
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
        return [
            'name' => [
                'required', 'max:255',
                request()->isMethod('POST') ? 'unique:procurement_categories,name' : 'unique:procurement_categories,name,' . $this->route('procurement_category'),
            ],
            'is_active' => 'boolean',
            'description' => 'nullable|max:10000',
        ];
    }
}
