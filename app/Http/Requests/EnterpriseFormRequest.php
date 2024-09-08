<?php

namespace App\Http\Requests;

use App\Models\Enterprise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EnterpriseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = request()->isMethod('PUT') ? Enterprise::where('id', $this->route('enterprise'))->pluck('user_id')->first() : '';
        $enterpriseId = $this->route('enterprise');

        $rules = [
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
            'phone' =>  [
                'required',
                request()->isMethod('PUT') ? 'unique:enterprises,phone,' . $enterpriseId : 'unique:enterprises,phone',
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

        // Xử lý đặc biệt cho trường avatar
        $rules['avatar'] = [
            'nullable',
            function ($attribute, $value, $fail) use ($enterpriseId) {
                if (request()->isMethod('PUT')) {
                    $enterprise = Enterprise::find($enterpriseId);
                    if ($enterprise && $value !== $enterprise->avatar) {
                        // Chỉ áp dụng validation khi avatar thay đổi
                        $validator = Validator::make(
                            ['avatar' => $value],
                            ['avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']
                        );
                        if ($validator->fails()) {
                            $fail($validator->errors()->first('avatar'));
                        }
                    }
                } else {
                    // Đối với phương thức POST, áp dụng validation mặc định
                    $validator = Validator::make(
                        ['avatar' => $value],
                        ['avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']
                    );
                    if ($validator->fails()) {
                        $fail($validator->errors()->first('avatar'));
                    }
                }
            },
        ];

        return $rules;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = [
            'result' => false,
            'status' => 400,
            'errors' => $validator->errors()
        ];

        throw new \Illuminate\Validation\ValidationException($validator, response()->json($response, 400));
    }
}
