<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidBond extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'enterprise_id',
        'bond_number',
        'bond_amount',
        'bond_amount_in_words',
        'bond_type',
        'issue_date',
        'expiry_date',
        'description',
    ];

    public function project()
{
    return $this->belongsTo(Project::class);
}
    public function enterprise(){
        return $this->belongsTo(Enterprise::class);
    }
}
