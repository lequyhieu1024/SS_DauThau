<?php

namespace App\Http\Requests;

use App\Models\Enterprise;
use Illuminate\Foundation\Http\FormRequest;

class EnterpriseFormRequest extends FormRequest
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
        $userId = request()->isMethod('PUT') ? Enterprise::where('id', $this->route('enterprise'))->pluck('user_id')->first() : '';
        return [
            'name' => 'required|max:50',
            'taxcode' => [
                'required',
                'regex:/^[0-9]{10,14}$/',
                request()->isMethod('PUT') ? 'unique:users,taxcode,' . $userId : 'unique:users,taxcode'
            ],
            'email' => [
                'required',
                'email',
                request()->isMethod('PUT') ? 'unique:users,email,' . $userId : 'unique:users,email',
                'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/'
            ],
            'account_ban_at' => 'nullable|date',
            'password' => request()->isMethod('PUT') ? 'nullable|min:8' : 'required|min:8',
            'representative' => 'required|max:191',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' =>  [
                'required',
                request()->isMethod('PUT') ? 'unique:enterprises,phone,' . $this->route('enterprise') . '' : 'unique:enterprises,phone',
                'regex:/^0\d{9}$/'
            ],
            'address' => 'required|max:191',
            'website' => 'required|max:191',
            'establish_date' => 'required|date|before:today|after_or_equal:1900-01-01',
            'registration_date' => 'required|date|before:today|after_or_equal:1900-01-01',
            'registration_number' => 'required|max:50',
            'organization_type' => 'required|in:1,2',
            'is_active' => 'required|in:1,0',
            'is_blacklist' => 'required|in:1,0',
            'industry_id' => 'required|array'
        ];
    }
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = [
            'result' => false,
            'status' => 400,
            'message' => $validator->errors()
        ];

        throw new \Illuminate\Validation\ValidationException($validator, response()->json($response, 400));
    }
}