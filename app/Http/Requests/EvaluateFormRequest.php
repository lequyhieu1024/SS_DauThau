<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Traits\HandlesValidationFailures;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class EvaluateFormRequest extends FormRequest
{
    use HandlesValidationFailures;
    protected $project;

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
            'project_id' => [
                'required','numeric','exists:projects,id',
                request()->isMethod('POST') ? 'unique:evaluates,project_id' : 'unique:evaluates,project_id,' . $this->route('evaluate'),
            ],
            'enterprise_id' => 'required|integer|exists:enterprises,id',
            'title' => 'required|max:255',
            'score' => 'required|numeric|between:0,10',
            'evaluate' => 'nullable|max:10000',
        ];
    }
}
