<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enterprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'representative_name',
        'address',
        'website',
        'description',
        'establish_date',
        'avg_document_rating',
        'field_active_id',
        'is_active',
        'is_blacklist'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_blacklist' => 'boolean'
    ];
    public static function searchEnterprise($name, $representative_name, $address, $fieldActiveId, $establish_date, $is_active, $is_blacklist, $page, $size)
    {
        $query = self::query();

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($representative_name) {
            $query->where('representative_name', 'like', '%' . $representative_name . '%');
        }
        if ($address) {
            $query->where('address', 'like', '%' . $address . '%');
        }
        if ($establish_date) {
            $query->where('establish_date', 'like', '%' . $establish_date . '%');
        }
        if ($is_active) {
            $query->where('is_active', 'like', '%' . $is_active . '%');
        }
        if ($is_blacklist) {
            $query->where('is_blacklist', 'like', '%' . $is_blacklist . '%');
        }

        if ($fieldActiveId) {
            $query->where('field_active_id', $fieldActiveId);
        }

        return $query->with(['fieldActiveId:id,name,representative_name,address,establish_date,is_blacklist,is_active'])->orderBy('id', 'desc')->paginate(
            $size,
            ['*'],
            'page',
            $page
        );
    }

    // public function user()
    // {
    //     return $this->belongsTo(Staff::class, 'user_id');
    // }
    // public function biddingField()
    // {
    //     return $this->belongsTo(BiddingField::class, 'field_active_id');
    // }
}
