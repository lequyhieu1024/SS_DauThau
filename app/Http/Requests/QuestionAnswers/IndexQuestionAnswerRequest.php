<?php

namespace App\Http\Requests\QuestionAnswers;

use Illuminate\Foundation\Http\FormRequest;

class IndexQuestionAnswerRequest extends FormRequest
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
            'page' => 'nullable|integer|min:1'
        ];
    }
}
