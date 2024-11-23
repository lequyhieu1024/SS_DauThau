<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIndustry extends Model
{
    use HasFactory;
    protected $table = 'project_industry';
    protected $fillable = [
        'project_id',
        'industry_id',
    ];
}
