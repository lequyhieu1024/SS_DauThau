<?php

namespace App\Http\Requests;

use App\Models\Enterprise;
use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class EnterpriseFormRequest extends FormRequest
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
        $isPutOrPatch = request()->isMethod('PUT') || request()->isMethod('PATCH');
        $userId = $isPutOrPatch ? Enterprise::where('id', $this->route('enterprise'))->pluck('user_id')->first() : '';
        return [
            'name' => 'required|max:50',
            'taxcode' => [
                'required',
                'regex:/^[0-9]{10,14}$/',
                $isPutOrPatch ? 'unique:users,taxcode,' . $userId : 'unique:users,taxcode'
            ],
            'email' => [
                'required',
                'email',
                $isPutOrPatch ? 'unique:users,email,' . $userId : 'unique:users,email',
                'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/'
            ],
            'account_ban_at' => 'nullable|date',
            'password' => $isPutOrPatch ? 'nullable|min:8' : 'required|min:8',
            'representative' => 'required|max:191',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' =>  [
                'required',
                $isPutOrPatch ? 'unique:enterprises,phone,' . $this->route('enterprise') : 'unique:enterprises,phone',
                'regex:/^(\+84|0)(\s?\d{3}|\s?\d{4}|\s?\d{5})(\s?\d{3,4}){2}$/'
            ],

            'address' => 'required|max:191',
            'website' => 'required|max:191',
            'establish_date' => 'required|date|before:today|after_or_equal:1900-01-01',
            'registration_date' => 'required|date|before:today|after_or_equal:1900-01-01',
            'registration_number' => 'required|max:50',
            'organization_type' => 'required|in:1,2',
            'is_active' => 'required|in:1,0',
            'is_blacklist' => 'required|in:1,0',
            'industry_id' => 'required|array|exists:industries,id',
            'roles' => 'required|array|exists:roles,id',
        ];
    }
}
