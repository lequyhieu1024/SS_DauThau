<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['method_name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
