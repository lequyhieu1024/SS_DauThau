<?php

namespace App\Http\Requests\Industries;

use App\Http\Requests\Common\IndexBaseRequest;

class IndexIndustryRequest extends IndexBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'business_activity_type_id' => [
                'nullable',
                'integer',
                'min:1'
            ],
        ]);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'business_activity_type_id' => $this->query('business_activity_type_id'),
        ]);
    }
}
