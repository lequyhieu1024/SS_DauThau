<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
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
}
