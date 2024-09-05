<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionAnswer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'question_content', 'answer_content', 'asked_by', 'answered_by', 'status'
    ];

    public function askedBy(){
        return $this->belongsTo(User::class, 'asked_by');
    }

    public function answeredBy(){
        return $this->belongsTo(User::class, 'answered_by');
    }
}
