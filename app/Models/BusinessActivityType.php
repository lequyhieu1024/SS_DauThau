<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function getFilteredBusinessActivityTypes($filters)
    {
        $query = self::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        $query->orderBy('id', 'desc');

        return $query;
    }

    public static function createNew(array $data)
    {
        return self::create($data);
    }

    public static function findBusinessActivityTypeById($id)
    {
        return self::find($id);
    }

    public static function updateBusinessActivityTypeById($id, $data)
    {
        $businessActivityType = self::find($id);

        if (!$businessActivityType) {
            return null;
        }

        $businessActivityType->fill($data);
        $businessActivityType->save();

        return $businessActivityType;
    }

    public static function toggleActiveStatusById($id)
    {
        $businessActivityType = self::find($id);

        if (!$businessActivityType) {
            return null;
        }

        $businessActivityType->is_active = !$businessActivityType->is_active;
        $businessActivityType->save();

        return $businessActivityType;
    }

    public static function deleteBusinessActivityTypeById($id)
    {
        $businessActivityType = self::find($id);

        if (!$businessActivityType) {
            return null;
        }

        $businessActivityType->delete();

        return $businessActivityType;
    }

}
