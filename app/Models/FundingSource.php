<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class FundingSource extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function getModelName(): string
    {
        return 'Nguồn tài trợ - Funding Source';
    }

    protected function getLogAttributes(): array
    {
        return [
            'name',
            'description',
            'code',
            'type',
            'is_active'
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
