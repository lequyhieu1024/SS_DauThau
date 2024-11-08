<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruct extends Model
{
    use HasFactory;
    protected $fillable = [
        'instruct',
        'is_use'
    ];
    
    protected function getModelName(): string
    {
        return 'Hướng dẫn - Instruct';
    }

    protected function getLogAttributes(): array
    {
        return [
            'instruct',
            'is_use'
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
