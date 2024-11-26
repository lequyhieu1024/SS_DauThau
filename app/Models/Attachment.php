<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'name',
        'path',
        'type',
        'size',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
