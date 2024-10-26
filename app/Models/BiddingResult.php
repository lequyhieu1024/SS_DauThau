<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'enterprise_id',
        'bid_document_id',
        'decision_number',
        'decision_date',
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }
    public function enterprise() {
        return $this->belongsTo(Enterprise::class);
    }
    public function biddingDocument() {
        return $this->belongsTo(BidDocument::class, 'bid_document_id');
    }
}