<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'is_active', 'business_activity_type_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Search industries with pagination and filter by name and business activity type name.
     */
    public static function searchIndustries($name, $businessActivityTypeName, $page, $size)
    {
        $query = self::query();

        if ($name) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        if ($businessActivityTypeName) {
            $query->whereHas('businessActivityType', function ($q) use ($businessActivityTypeName) {
                $q->where('name', 'like', '%'.$businessActivityTypeName.'%');
            });
        }

        return $query->with(['businessActivityType:id,name'])->orderBy('id', 'desc')->paginate($size, ['*'], 'page',
            $page);
    }

    public static function createNew(array $data)
    {
        return self::create($data);
    }

    public static function findIndustryById(int $id): ?Industry
    {
        return self::find($id);
    }

    public static function updateIndustry(int $id, array $data): ?Industry
    {
        $industry = self::find($id);

        if (!$industry) {
            return null;
        }

        $industry->update($data);
        return $industry;
    }

    public static function toggleActiveStatus(int $id): ?Industry
    {
        $industry = self::find($id);

        if (!$industry) {
            return null;
        }

        $industry->is_active = !$industry->is_active;
        $industry->save();
        return $industry;
    }

    public static function deleteIndustryById(int $id): bool
    {
        $industry = self::find($id);

        if (!$industry) {
            return false;
        }

        $industry->delete();
        return true;
    }

    /**
     * Get the business activity type associated with the industry.
     */
    public function businessActivityType()
    {
        return $this->belongsTo(BusinessActivityType::class, 'business_activity_type_id');
    }
}
