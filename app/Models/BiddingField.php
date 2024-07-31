<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiddingField extends Model
{
    protected $fillable = [
        'name', 'description', 'code', 'is_active', 'parent_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
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

        if (isset($filters['parent_name'])) {
            $query->whereHas('parent', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['parent_name'].'%');
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
        $biddingField = self::with(['children:id,name,parent_id', 'parent:id,name'])->find($id);

        if ($biddingField && $biddingField->parent_id) {
            $biddingField->parent_name = $biddingField->parent->name;
        }

        if ($biddingField && $biddingField->children) {
            $biddingField->children = $biddingField->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                ];
            });
        }

        return $biddingField;
    }

    public static function findBiddingFieldByIdToggleStatus($id)
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
        return $this->belongsTo(BiddingField::class, 'parent_id')->select('id', 'name');
    }

    public function children()
    {
        return $this->hasMany(BiddingField::class, 'parent_id')->select('id', 'name', 'parent_id');
    }
}
