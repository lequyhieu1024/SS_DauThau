<?php

namespace App\Http\Requests;

use App\Models\FeedbackComplaint;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FeedbackComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private function isPutOrPatch()
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH');
    }

    private function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'content' => $this->isPost() ? 'required|string' : 'nullable',
            'responded_by' => $this->isPutOrPatch() ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'response_content' => $this->isPutOrPatch() ? 'required|string' : 'nullable',
            'status' => 'required|in:pending,responded',

        ];
        if ($this->isPost()) {
            $rules['complaint_by'] = 'required|exists:users,id';
        }
        return $rules;
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'complaint_by' => $this->isPost() ? auth()->id() : null,
            'status' => $this->isPost() ? 'pending' : 'responded',
            'responded_by' => $this->isPutOrPatch() ? auth()->id() : null,
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'result' => false,
            'message' => 'Lỗi xác thực',
            'errors' => $validator->errors(),
        ], 400));
    }
}
