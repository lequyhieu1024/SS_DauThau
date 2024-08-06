<?php

namespace App\Http\Requests\BusinessActivityTypes;

use App\Http\Requests\Common\StoreBaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Schema;

class StoreBusinessActivityTypeRequest extends StoreBaseRequest
{
    /**
     * Configure the validator instance.
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $table = 'business_activity_types';
            $columns = Schema::getColumnListing($table);
            $invalidFields = array_diff(array_keys($this->all()), $columns);

            if (!empty($invalidFields)) {
                foreach ($invalidFields as $field) {
                    $validator->errors()->add($field, 'The '.$field.' field is not a valid field.');
                }
            }
        });
    }
}
