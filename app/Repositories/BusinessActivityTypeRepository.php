<?php

namespace App\Repositories;

use App\Models\Industry;

class BusinessActivityTypeRepository extends BaseRepository
{
    protected static $specialRecordCreated = false;

    public function __construct()
    {
        parent::__construct();

        if (!self::$specialRecordCreated) {
            $this->createSpecialRecordIfNotExists();
            self::$specialRecordCreated = true;
        }
    }

    public function getModel()
    {
        return \App\Models\BusinessActivityType::class;
    }

    private function createSpecialRecordIfNotExists()
    {
        $exists = $this->model->where('id', 1)->exists();

        if (!$exists) {
            $this->model->create([
                'id' => 1,
                'name' => 'Chưa phân loại',
                'description' => 'Danh mục "Chưa phân loại" đại diện cho các mục chưa được phân loại vào nhóm cụ thể nào. Danh mục đặc biệt không thể xóa.',
                'is_active' => true,
            ]);
        }
    }

    public function filter($data)
    {
        $query = $this->model->query();
        $query->where('id', '!=', 1);

        if (isset($data['name'])) {
            $query->where('name', 'like', '%'.$data['name'].'%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function getBusinessActivityTypesWithIndustries()
    {
        return $this->model->select('id', 'name')
            ->where('is_active', true)
            ->with(['industries:id,name,business_activity_type_id'])
            ->get();
    }


    public function delete($id)
    {
        $businessActivityType = $this->model->find($id);

        if (!$businessActivityType) {
            return false;
        }

        if ($businessActivityType->id == 1) {
            return false;
        }

        Industry::where('business_activity_type_id', $businessActivityType->id)
            ->update(['business_activity_type_id' => 1]);
        return $businessActivityType->delete();
    }

}
