<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait HandlesValidationFailures
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        $response = [
            'result' => false,
            'status' => 422,
            'errors' => $validator->errors()
        ];

        throw new ValidationException($validator, response()->json($response, 422));
    }
}
