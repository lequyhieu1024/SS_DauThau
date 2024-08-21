<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enterprise extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'representative',
        'avatar',
        'phone',
        'address',
        'website',
        'description',
        'establish_date',
        'avg_document_rating',
        'registration_date',
        'registration_number',
        'organization_type',
        'is_active',
        'is_blacklist',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class);
    }
}
