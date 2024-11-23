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

        // Tính số lượng dự án phê duyệt của mỗi ngành
        foreach ($industries as $industry) {
            $totalProjects = $industry->projects()
                ->where('status', ProjectStatus::APPROVED->value)
                ->count();

            $data[] = [
                "industry" => $industry->name,
                "total_project" => $totalProjects
            ];
        }

        // Sắp xếp theo số lượng dự án phê duyệt giảm dần
        usort($data, function ($a, $b) {
            return $b['total_project'] <=> $a['total_project'];
        });

        // Lấy ra 10 ngành có số lượng dự án lớn nhất
        $top10 = array_slice($data, 0, 10);

        // Tổng hợp các ngành còn lại
        $others = array_slice($data, 10);
        $othersTotalProjects = 0;
        foreach ($others as $other) {
            $othersTotalProjects += $other['total_project'];
        }

        // Nếu có ngành "Còn lại", thêm vào mảng kết quả
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

        // Tính số lượng doanh nghiệp của mỗi ngành
        foreach ($industries as $industry) {
            $totalEnterprises = $industry->enterprises()->count();
            $data[] = [
                "industry" => $industry->name,
                "total_enterprise" => $totalEnterprises
            ];
        }

        // Sắp xếp theo số lượng doanh nghiệp giảm dần
        usort($data, function ($a, $b) {
            return $b['total_enterprise'] <=> $a['total_enterprise'];
        });

        // Lấy ra 10 ngành có số lượng doanh nghiệp lớn nhất
        $top10 = array_slice($data, 0, 10);

        // Tổng hợp các ngành còn lại
        $others = array_slice($data, 10);
        $othersTotalEnterprises = 0;
        foreach ($others as $other) {
            $othersTotalEnterprises += $other['total_enterprise'];
        }

        // Nếu có ngành "Còn lại", thêm vào mảng kết quả
        if (count($others) > 0) {
            $top10[] = [
                'industry' => 'Còn lại',
                'total_enterprise' => $othersTotalEnterprises
            ];
        }

        return $top10;
    }


}
