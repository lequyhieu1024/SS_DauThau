<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class SelectionMethodRequest extends FormRequest
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
        if ($this->isMethod('get')) {
            return [
                'size' => 'nullable|integer|min:1',
                'page' => 'nullable|integer|min:1',
                'method_name' => 'nullable|string|min:1',
            ];
        }

        $id = $this->route('id');
        return [
            'method_name' => [
                'required',
                'string',
                'max:255',
                'unique:selection_methods,method_name,' . $id,
            ],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }
}
