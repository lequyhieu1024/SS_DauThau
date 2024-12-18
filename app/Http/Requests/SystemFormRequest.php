<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;
class SystemFormRequest extends FormRequest
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
            'name' => 'required',
            'logo' => 'required',
            'phone' =>  [
                'required',
                'regex:/^(\(\+84\s?\d{1,2}\)|\+84|\(0\d{1,2}\)|0\d{1,2})(\s?\d{3,4})(\s?\d{3,4})$/'
            ],
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
            'address' => 'required',
            ],
        ];
    }
}
