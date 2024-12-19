<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLogOptionsTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class QuestionAnswer extends Model
{
    use HasFactory;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $table = "questions_answers";

    protected $fillable = [
        'project_id',
        'question_content',
        'answer_content',
        'asked_by',
        'answered_by',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function asker()
    {
        return $this->belongsTo(User::class, 'asked_by');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }


    protected function getModelName(): string
    {
        return 'Hỏi đáp - Question and Answer';
    }

    protected function getLogAttributes(): array
    {
        return [
            'project_id',
            'question_content',
            'answer_content',
            'asked_by',
            'answered_by',
            'status',
        ];
    }

    protected function getFieldName(): string
    {
        return $this->question_content;
    }

}
