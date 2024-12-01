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

    public function top10IndustryHasTheMostProjects($industries)
    {
        $data = [];

        foreach ($industries as $industry) {
            $totalProjects = $industry->projects()
                ->where('status', ProjectStatus::APPROVED->value)
                ->count();

            $data[] = [
                "industry" => $industry->name,
                "total_project" => $totalProjects
            ];
        }

        usort($data, function ($a, $b) {
            return $b['total_project'] <=> $a['total_project'];
        });

        $top10 = array_slice($data, 0, 10);

        $others = array_slice($data, 10);
        $othersTotalProjects = 0;
        foreach ($others as $other) {
            $othersTotalProjects += $other['total_project'];
        }

        if (count($others) > 0) {
            $top10[] = [
                'industry' => 'Còn lại',
                'total_project' => $othersTotalProjects
            ];
        }

        return $top10;
    }


    public function top10IndustryHasTheMostEnterprises($industries)
    {
        $data = [];

        foreach ($industries as $industry) {
            $totalEnterprises = $industry->enterprises()->count();
            $data[] = [
                "industry" => $industry->name,
                "total_enterprise" => $totalEnterprises
            ];
        }

        usort($data, function ($a, $b) {
            return $b['total_enterprise'] <=> $a['total_enterprise'];
        });

        $top10 = array_slice($data, 0, 10);

        $others = array_slice($data, 10);
        $othersTotalEnterprises = 0;
        foreach ($others as $other) {
            $othersTotalEnterprises += $other['total_enterprise'];
        }

        if (count($others) > 0) {
            $top10[] = [
                'industry' => 'Còn lại',
                'total_enterprise' => $othersTotalEnterprises
            ];
        }

        return $top10;
    }

    public function countIndustries(){
        return $this->model->count();
    }


}
