<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcurementCategory extends Model
{
    use HasFactory;
    use LogsActivity;
    use ActivityLogOptionsTrait;
    protected $table = 'procurement_categories';

    protected $fillable = [
        'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the industries for the business activity type.
     */
    public function projects()
    {
        return $this->belongsToMany(ProcurementCategory::class, 'project_procurement', 'project_id', 'procurement_id');
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
