<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class QuestionAnswerRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id', 
            'question_content' => 'required|string|max:500',
            'answer_content' => 'nullable|string|max:500',
            'asked_by' => 'required|integer|exists:users,id',
            'answered_by' => 'nullable|integer|exists:users,id',
            'status' => 'required|in:pending,answered,closed',
        ];
    }
}
