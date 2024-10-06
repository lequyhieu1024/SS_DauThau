<?php

namespace App\Models;

use App\Traits\HandlesValidationFailures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'title',
        'phone',
        'content',
        'document',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function getModelName(): string
    {
        return 'Hỗ trợ - Support';
    }

    protected function getLogAttributes(): array
    {
        return [
            'user_id',
            'email',
            'title',
            'phone',
            'content',
            'document',
            'type',
            'status',
        ];
    }

    protected function getFieldName(): string
    {
        return $this->title;
    }
}
