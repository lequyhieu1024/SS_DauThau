<?php

namespace App\Http\Requests\Industries;

use App\Http\Requests\Common\UpdateBaseRequest;
use App\Validators\FieldValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateIndustryRequest extends UpdateBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'business_activity_type_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('business_activity_types', 'id'), // Ensure it is a valid foreign key
            ],
        ]);
    }

    /**
     * Configure the validator instance.
     */
    protected function withValidator(Validator $validator): void
    {
        FieldValidator::validateFields($validator, 'industries');
    }
}
