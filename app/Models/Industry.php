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
        DB::beginTransaction();

        try {
            $industry = self::create($data);
            DB::commit();
            return $industry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function findIndustryById(int $id): ?Industry
    {
        return self::find($id);
    }

    public static function updateIndustry(int $id, array $data): ?Industry
    {
        DB::beginTransaction();

        try {
            $industry = self::find($id);

            if (!$industry) {
                DB::rollBack();
                return null;
            }

            $industry->update($data);
            DB::commit();
            return $industry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function toggleActiveStatus(int $id): ?Industry
    {
        DB::beginTransaction();

        try {
            $industry = self::find($id);

            if (!$industry) {
                DB::rollBack();
                return null;
            }

            $industry->is_active = !$industry->is_active;
            $industry->save();
            DB::commit();
            return $industry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function deleteIndustryById(int $id): bool
    {
        DB::beginTransaction();

        try {
            $industry = self::find($id);

            if (!$industry) {
                DB::rollBack();
                return false;
            }

            $industry->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get the business activity type associated with the industry.
     */
    public function businessActivityType()
    {
        return $this->belongsTo(BusinessActivityType::class, 'business_activity_type_id');
    }
}
