<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use LogsActivity;
    use ActivityLogOptionsTrait;
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'enterprise_id',
        'code',
        'avatar',
        'name',
        'phone',
        'email',
        'birthday',
        'gender',
        'taxcode',
        'education_level',
        'start_date',
        'end_date',
        'salary',
        'address',
        'status',
    ];
    public function enterprise() {
        return $this->belongsTo(Enterprise::class);
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'employee_task')
            ->withPivot('feedback')
            ->withTimestamps();
    }
    protected function getModelName(): string
    {
        return 'Nhân viên doanh nghiệp - Employees';
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
