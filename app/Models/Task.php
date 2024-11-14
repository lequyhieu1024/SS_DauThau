<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use LogsActivity;
    use ActivityLogOptionsTrait;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'code',
        'description',
        'difficulty_level',
    ];
    public function employees() {
        return $this->belongsToMany(Employee::class, 'employee_task')
            ->withPivot('feedback')
            ->withTimestamps();
    }
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function taskProgress()
    {
        return $this->hasMany(Task::class, 'task_id', 'id');
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
