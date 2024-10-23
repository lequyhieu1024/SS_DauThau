<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class BiddingResultFormRequest extends FormRequest
{
    use HandlesValidationFailures;
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
            'project_id' => 'required|numeric|exists:projects,id',
            'enterprise_id' => 'required|numeric|exists:enterprises,id',
            'bid_document_id' => 'required|numeric|exists:bid_documents,id',
            'decision_number' => 'required|max:255',
            'decision_date' => 'required|date',
        ];
    }
}
