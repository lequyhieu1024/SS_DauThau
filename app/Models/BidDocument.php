<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class BidDocument extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $fillable = [
        'project_id',
        'enterprise_id',
        'bid_bond_id',
        'submission_date',
        'bid_price',
        'implementation_time',
        'validity_period',
        'status',
        'note',
        'file',
    ];

    // N-1 relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    // N-1 relationship with Enterprise
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    // 1-1 relationship with BidBond
    public function bidBond()
    {
        return $this->hasOne(BidBond::class, 'id', 'bid_bond_id');
    }

    protected function getModelName(): string
    {
        return 'Hồ sơ dự thầu - Bid Document';
    }

    protected function getLogAttributes(): array
    {
        return [
            'project_id',
            'enterprise_id',
            'bid_bond_id',
            'submission_date',
            'bid_price',
            'implementation_time',
            'validity_period',
            'status',
            'note'
        ];
    }

    public function getEnterpriseNameByUserId($enterpriseId)
    {
        $enterprise = Enterprise::where('id', $enterpriseId)->first();
        return $enterprise ? $enterprise->representative : '';
    }

    protected function getFieldName(): string
    {
        return $this->getEnterpriseNameByUserId($this->enterprise_id);
    }

}
