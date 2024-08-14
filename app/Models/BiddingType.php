<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiddingType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getFilteredBiddingTypes($filters)
    {
        $query = self::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query;
    }

    public static function createBiddingType($data)
    {
        return self::create($data);
    }

    public static function findBiddingTypeById($id)
    {
        return self::find($id);
    }

    public static function updateBiddingType($id, $data)
    {
        $biddingType = self::find($id);
        if ($biddingType) {
            $biddingType->update($data);
        }
        return $biddingType;
    }

    public static function deleteBiddingType($id){
        $biddingType = self::find($id);
        if ($biddingType) {
            $biddingType->delete();
        }
        return $biddingType;
    }
}
