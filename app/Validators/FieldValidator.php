<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Schema;

class FieldValidator
{
    /**
     * Validate fields against the table's columns.
     *
     * @param  Validator  $validator
     * @param  string  $table
     */
    public static function validateFields(Validator $validator, string $table): void
    {
        $validator->after(function ($validator) use ($table) {
            $columns = Schema::getColumnListing($table);
            $invalidFields = array_diff(array_keys(request()->all()), $columns);

            if (!empty($invalidFields)) {
                foreach ($invalidFields as $field) {
                    $validator->errors()->add($field, $field.' không phải trường hợp lệ.');
                }
            }
        });
    }
}
