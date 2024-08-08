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
            'business_activity_type_name' => [
                'nullable',
                'string',
                'not_regex:/<[^>]*script.*?>.*?<\/[^>]*script.*?>/i', // Prevent HTML script tags
                'not_regex:/\b(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|EXEC|UNION|DECLARE|GRANT|REVOKE)\b/i',
                // Prevent SQL keywords
            ],
        ]);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'business_activity_type_name' => $this->query('business_activity_type_name'),
        ]);
    }
}
