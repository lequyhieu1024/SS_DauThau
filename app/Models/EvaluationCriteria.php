<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EvaluationCriteria extends Model
{
    use HasFactory;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $fillable = [
        'project_id',
        'name',
        'weight',
        'description',
        'is_active',
    ];

    // public function project()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    protected function getModelName(): string
    {
        return 'Tiêu chí đánh giá - Evaluation Criteria';
    }

    protected function getLogAttributes(): array
    {
        return [
            'project_id',
            'name',
            'weight',
            'description',
            'is_active',
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
