<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingSource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'is_active',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getFilteredFundingSources($filters)
    {
        $query = self::query()->latest('id');

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['code'])) {
            $query->where('code', $filters['code']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query;
    }

    public static function createFundingSource($data)
    {
        return self::create($data);
    }

    public static function findFundingSourceById($id)
    {
        return self::find($id);
    }

    public static function updateFundingSource($id, $data)
    {
        $fundingSource = self::find($id);
        if ($fundingSource) {
            $fundingSource->update($data);
        }
        return $fundingSource;
    }

    public static function deleteFundingSource($id){
        $fundingSource = self::find($id);
        if ($fundingSource) {
            $fundingSource->delete();
        }
        return $fundingSource;
    }
}
