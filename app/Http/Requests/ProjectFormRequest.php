<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class ProjectFormRequest extends FormRequest
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
//            'files' => request()->isMethod('POST') ? 'required|array|max:10000|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf,zip,rar,ppt,pptx' : 'nullable|array|max:10000|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf,zip,rar,ppt,pptx',
            'funding_source_id' => 'required|numeric|exists:funding_sources,id',
            'tenderer_id' => 'required|numeric|exists:enterprises,id',
            'investor_id' => 'required|numeric|exists:enterprises,id',
            'staff_id' => 'numeric|exists:staffs,id',
            'selection_method_id' => 'nullable|numeric|exists:selection_methods,id',
            'decision_number_issued' => 'required|max:100',
            'industry_id' => 'required|array|exists:industries,id',
            'procurement_id' => 'required|array|exists:procurement_categories,id',
            'name' => 'required|max:255',
            'is_domestic' => 'required|boolean',
            'location' => 'required|max:255',
            'amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'description' => 'nullable|max:10000',
            'submission_method' => 'required|in:online,in_person',
            'receiving_place' => [
                'nullable',
                'string',
                'max:255',
                'required_if:submission_method,in_person',
            ],
            'bid_submission_start' => 'required|date',
            'bid_submission_end' => 'required|date|after:bid_submission_start',
            'bid_opening_date' => 'nullable|date',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'approve_at' => 'nullable|date',
            'decision_number_approve' => 'nullable|max:100|required_if:approve_at,!null',
            'status' => 'numeric',
        ];
    }
}
