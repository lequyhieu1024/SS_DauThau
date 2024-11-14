<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkProgress extends Model
{
    use LogsActivity;
    use ActivityLogOptionsTrait;
    use HasFactory, SoftDeletes;

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
        return $this->belongsTo(BiddingResult::class, 'bidding_result_id');
    }

    public function taskProgresses() {
        return $this->belongsToMany(Task::class, 'task_progresses');
    }

    protected function getModelName(): string
    {
        return 'Tiến độ nhiệm vụ của dự án - WorkProgress';
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
