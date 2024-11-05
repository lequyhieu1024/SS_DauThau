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
            'win_amount' => 'required|numeric|min:0',
            'bid_document_id' => [
                'required','numeric','exists:bid_documents,id',
                request()->isMethod('POST') ? 'unique:bidding_results,bid_document_id' : 'unique:bidding_results,bid_document_id,' . $this->route('bidding_result')
            ],
            'decision_number' => [
                'required','max:255',
                request()->isMethod('POST') ? 'unique:bidding_results,decision_number' : 'unique:bidding_results,decision_number,' . $this->route('bidding_result')
            ],
            'decision_date' => 'required|date',
        ];
    }
}
