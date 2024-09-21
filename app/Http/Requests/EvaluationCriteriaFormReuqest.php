<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationCriteriaFormReuqest extends FormRequest
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
            'project_id' => 'required|numeric|exists:projects,id',
            'name' => 'required|max:191',
            'weight' => 'required|decimal:0,max:100',
            'description' => 'nullable|max:10000',
            'is_active' => 'boolean',
        ];
    }
}
