<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Enterprise extends Model
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $fillable = [
        'user_id',
        'representative_name',
        'address',
        'website',
        'description',
        'establish_date',
        'avg_document_rating',
        'field_active_id',
        'is_active',
        'is_blacklist',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // sẽ viết 'field_active_id' relationship khi tạo bảng này
}
