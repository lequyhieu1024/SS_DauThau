<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class TaskFormRequest extends FormRequest
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
            'name' => 'required|string',
            'code' => [
                'required',
                request()->isMethod('POST') ? 'unique:tasks,code' : 'unique:tasks,code,' . $this->route('task'),
            ],
            'description' => 'nullable|max:10000',
            'difficulty_level' => 'required|in:easy,medium,hard,veryhard',
            'employee_id' => 'required|exists:employees,id',
            'feedback' => 'nullable|in:poor,medium,good,verygood,excellent',
        ];
    }
}
