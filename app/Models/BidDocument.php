<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BidDocument extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    // N-1 relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // N-1 relationship with Enterprise
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    // 1-1 relationship with BidBond
//    public function bidBond()
//    {
//        return $this->hasOne(BidBond::class, 'id', 'bid_bond_id');
//    }
}
