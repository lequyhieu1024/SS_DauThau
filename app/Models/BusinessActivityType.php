<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class BusinessActivityType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;
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

    protected function getModelName(): string
    {
        return 'Loại hình kinh doanh - Business Activity Type';
    }

    protected function getLogAttributes(): array
    {
        return ['name', 'description', 'is_active'];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
