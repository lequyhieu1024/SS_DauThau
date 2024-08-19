<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BusinessActivityType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'business_activity_types';

    protected $fillable = [
        'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the industries for the business activity type.
     */
    public function industries()
    {
        return $this->hasMany(Industry::class, 'business_activity_type_id');
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($businessActivityType) {
            // ID của bản ghi bị xóa mềm
            $id = $businessActivityType->id;

            // ID của danh mục chưa xác định
            $undefinedCategoryId = 1;

            // Cập nhật các bản ghi trong bảng industries
            DB::table('industries')
                ->where('business_activity_type_id', $id)
                ->update(['business_activity_type_id' => $undefinedCategoryId]);
        });
    }
}
