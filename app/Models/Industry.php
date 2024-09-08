<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogOptionsTrait;

    protected $table = 'industries';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'business_activity_type_id'
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

    public function enterprises()
    {
        return $this->belongsToMany(Enterprise::class);
    }

    protected function getModelName(): string
    {
        return 'Ngành nghề - Industry';
    }

    protected function getLogAttributes(): array
    {
        return ['name', 'description', 'is_active', 'businessActivityType.name'];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
