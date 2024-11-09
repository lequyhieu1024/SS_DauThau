<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;

class IndustryRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Industry::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (isset($data['business_activity_type_id'])) {
            $query->where('business_activity_type_id', $data['business_activity_type_id']);
        }

        return $query->with(['businessActivityType:id,name'])->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }


    public function delete($id)
    {
        $record = $this->model->find($id);

        if ($record) {
            return $record->delete();
        }

        return false;
    }

    public function top10IndustryHasTheMostProject($industries)
    {
        $data = [];
        foreach ($industries as $industry) {
            $data[] = ["industry" => $industry->name, "total_project" => $industry->projects()->where('status', ProjectStatus::APPROVED->value)->count()];
        }
        return $data;
    }

    public function top10IndustryHasTheMostEnterprise($industries)
    {
        $data = [];
        foreach ($industries as $industry) {
            $data[] = ["industry" => $industry->name, "total_enterprise" => $industry->enterprises()->count()];
        }
        return $data;
    }

}
