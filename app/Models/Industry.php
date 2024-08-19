<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'industries';

    protected $fillable = [
        'name', 'description', 'is_active', 'business_activity_type_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the business activity type associated with the industry.
     */
    public function businessActivityType()
    {
        return $this->belongsTo(BusinessActivityType::class, 'business_activity_type_id');
    }
}
