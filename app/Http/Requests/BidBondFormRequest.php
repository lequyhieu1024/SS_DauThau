<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BidBondFormRequest extends FormRequest
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
            'project_id'=>'required|exists:projects,id',
            'enterprise_id'=>'required|exists:enterprises,id',
            'bond_number' =>[
                'required',
                'max:100',
                'unique:bid_bonds,bond_number,'.$this->route('id')   
            ],
            'bond_amount'=>'required|numeric',
            'bond_type' => 'required|in:1,2,3',
            'expiry_date' => 'required|date|after:issue_date',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'result' => false,
            'message' => 'Lỗi xác thực',
            'errors' => $validator->errors(),
        ], 400));
    }
}
