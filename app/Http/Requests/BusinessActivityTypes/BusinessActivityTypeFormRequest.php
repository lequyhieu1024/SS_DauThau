<?php

namespace App\Http\Requests\BusinessActivityTypes;

use App\Http\Requests\Common\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Validators\FieldValidator;

class BusinessActivityTypeFormRequest extends BaseFormRequest
{
    /**
     * Configure the validator instance.
     */
    protected function withValidator(Validator $validator): void
    {
        FieldValidator::validateFields($validator, 'business_activity_types');
    }
}
