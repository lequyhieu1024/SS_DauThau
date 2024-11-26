<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProgress extends Model
{
    use HasFactory;
    protected $table='task_progresses';
    protected $fillable = [
        'work_progress_id',
        'task_id',
    ];
}
