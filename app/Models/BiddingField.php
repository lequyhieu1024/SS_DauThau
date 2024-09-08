<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiddingField extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogOptionsTrait;

    protected $table = 'bidding_fields';

    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active',
        'parent_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Quan hệ với parent (bản ghi cha)
    public function parent()
    {
        return $this->belongsTo(BiddingField::class, 'parent_id', 'id')->select('id', 'name');
    }

    // Quan hệ với children (các bản ghi con)
    public function children()
    {
        return $this->hasMany(BiddingField::class, 'parent_id', 'id')->select('id', 'name', 'parent_id');
    }

    protected function getModelName(): string
    {
        return 'Lĩnh vực đấu thầu - Bidding Field';
    }

    protected function getLogAttributes(): array
    {
        return ['name', 'description', 'code', 'is_active', 'parent_id'];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
