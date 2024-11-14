<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackComplaint extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'complaint_by', 'responded_by', 'content', 'response_content', 'status',
    ];

    public function complainedBy(){
        return $this->belongsTo(User::class, 'complaint_by');
    }

    public function respondedBy(){
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
}
