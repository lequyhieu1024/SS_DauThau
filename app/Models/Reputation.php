<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reputation extends Model
{
    use HasFactory;
    protected $fillable = [
        'enterprise_id',
        'number_of_ban',
        'number_of_blacklist',
        'prestige_score'
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
