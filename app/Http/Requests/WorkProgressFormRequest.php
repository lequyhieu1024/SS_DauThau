<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Traits\HandlesValidationFailures;
use Illuminate\Foundation\Http\FormRequest;

class WorkProgressFormRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'name' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {
                    $projectId = $this->input('project_id');

                    $biddingResultId = optional(Project::find($projectId))->biddingResult->id ?? 1;

                    $uniqueRule = request()->isMethod('POST')
                        ? 'unique:work_progresses,name,NULL,id,bidding_result_id,' . $biddingResultId
                        : 'unique:work_progresses,name,' . $this->route('work_progress') . ',id,bidding_result_id,' . $biddingResultId;

                    $validator = \Validator::make($this->all(), [
                        'name' => $uniqueRule,
                    ]);

                    if ($validator->fails()) {
                        return $fail($validator->errors()->first());
                    }
                },
            ],
            'progress' => 'required|numeric|min:0|max:100',
            'expense' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'feedback' => 'required|in:poor,medium,good,verygood,excellent',
            'description' => 'required|max:10000',
            'task_ids' => 'required|array|exists:tasks,id',
        ];
    }
}
