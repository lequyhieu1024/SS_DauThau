<?php

namespace App\Http\Requests\Industries;

use App\Http\Requests\Common\BaseFormRequest;
use App\Validators\FieldValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class IndustryFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'business_activity_type_id' => [
                'required',
                'integer',
                Rule::exists('business_activity_types', 'id'),
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
