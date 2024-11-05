<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkProgress extends Model
{
    use LogsActivity;
    use ActivityLogOptionsTrait;
    use HasFactory;

    protected $table = 'work_progresses';
    protected $fillable = [
        'bidding_result_id',
        'name',
        'progress',
        'expense',
        'start_date',
        'end_date',
        'feedback',
        'description',
    ];

    public function biddingResult()
    {
        return $this->belongsTo(BiddingResult::class);
    }

    public function taskProgresses() {
        return $this->belongsToMany(Task::class, 'task_progresses');
    }

    protected function getModelName(): string
    {
        return 'Nhiệm vụ của dự án - Task';
    }

    protected function getLogAttributes(): array
    {
        return $this->fillable;
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
