<?php

namespace App\Models;

use App\Models\Project;
use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function projects()
    {
        return $this->hasMany(Project::class, 'funding_source_id');
    }

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
