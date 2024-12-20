<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class SupportFormRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id',
            'title' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|regex:/^(\+\d{1,3}[- ]?)?\d{10}$/',
            'content' => 'sometimes|max:10000',
            'document' => 'nullable|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,ppt,pptx|max:2048',
            'type' => 'required|numeric',
        ];
    }
}

