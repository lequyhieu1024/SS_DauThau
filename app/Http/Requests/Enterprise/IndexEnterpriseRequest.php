<?php

namespace App\Http\Requests\Enterprise;

use App\Http\Requests\Common\IndexBaseRequest;

class IndexEnterpriseRequest extends IndexBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'user_id' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20'
            ],
            'representative_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'address' => [
                'nullable',
                'string',
                'max:255'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'establish_date' => [
                'nullable',
                'date'
            ],
            'avg_document_rating' => [
                'nullable',
                'integer',
                'min:0',
                'max:5'
            ],
            'field_active_id' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'is_active' => [
                'nullable',
                'boolean'
            ],
            'is_blacklist' => [
                'nullable',
                'boolean'
            ]
        ]);
    }
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'field_active_id' => $this->query('field_active_id'),
            'user_id' => $this->query('user_id'),
            'name' => $this->input('name'),
            'phone' => $this->input('phone'),
            'representative_name' => $this->input('representative_name'),
            'address' => $this->input('address'),
            'website' => $this->input('website'),
            'description' => $this->input('description'),
            'establish_date' => $this->input('establish_date'),
            'avg_document_rating' => $this->input('avg_document_rating'),
            'is_active' => $this->boolean('is_active'),
            'is_blacklist' => $this->boolean('is_blacklist')
        ]);
    }
}
