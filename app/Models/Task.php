<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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
}
