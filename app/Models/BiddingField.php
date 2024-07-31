<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiddingField extends Model
{
    protected $fillable = [
        'name', 'description', 'code', 'is_active', 'parent_id'
    ];

    public static function getFilteredBiddingFields($filters)
    {
        $query = self::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (isset($filters['code'])) {
            $query->where('code', $filters['code']);
        }

        if (isset($filters['parent'])) {
            $query->whereHas('parent', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['parent'].'%');
            });
        }

        $query->orderBy('id', 'desc');

        return $query;
    }

    public static function getAllBiddingFieldIds()
    {
        return self::select('id', 'name', 'parent_id')->get()->toArray();
    }

    public static function createBiddingField($data)
    {
        return self::create($data);
    }

    public static function findBiddingFieldById($id)
    {
        return self::with('parent')->find($id);
    }

    public static function updateBiddingField($id, $data)
    {
        $biddingField = self::find($id);
        if ($biddingField) {
            $biddingField->update($data);
        }
        return $biddingField;
    }

    public static function deleteBiddingField($id)
    {
        $biddingField = self::find($id);
        if ($biddingField) {
            $biddingField->delete();
        }
        return $biddingField;
    }

    public function parent()
    {
        return $this->belongsTo(BiddingField::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BiddingField::class, 'parent_id');
    }
}
