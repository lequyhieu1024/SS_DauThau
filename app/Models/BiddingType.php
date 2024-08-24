<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class BiddingType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function getModelName(): string
    {
        return 'Ngành nghề - Industry';
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
