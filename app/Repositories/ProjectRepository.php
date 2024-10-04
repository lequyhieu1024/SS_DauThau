<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Carbon\Carbon;

class ProjectRepository extends BaseRepository
{

    public function getModel()
    {
        return Project::class;
    }

    public function filter($data)
    {
        $query = $this->model->with('children')->whereNull('parent_id');
        // logic loc du an
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['status'])) {
            $data['status'] = array_filter($data['status']);
            $query->whereIn('status', $data['status']);
        }
        if (isset($data['upload_time_start'])) {
            $query->whereDate('created_at', '>=', $data['upload_time_start']);
        }

        if (isset($data['upload_time_end'])) {
            $query->whereDate('created_at', '<=', $data['upload_time_end']);
        }

        if (isset($data['investor'])) {
            $query->whereHas('investor', function ($q) use ($data) {
                $q->where('id', $data['investor']);
            });
        }

        if (isset($data['tenderer'])) {
            $query->whereHas('tenderer', function ($q) use ($data) {
                $q->where('id', $data['tenderer']);
            });
        }

        return $query->paginate(10 ?? $data['size']);
    }

    /**
     * @return mixed
     * Lấy ra những dự án đã hết hạn nộp hồ sơ rồi update trạng trạng thái từ RECEIVED -> SELECTING_CONTRUCTOR
     */
    public function getOverdueProjectSubmission()
    {
        return $this->model->where('bid_submission_end', "<", Carbon::now())->where("status", ProjectStatus::RECEIVED->value)->get();
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     * Đồng bộ ngành nghề khi create || update project
     */
    public function syncIndustry(array $data, $id)
    {
        $project = $this->model->findOrFail($id);
        return $project->industries()->sync($data['industry_id']);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     * Đồng bộ lĩnh vực mua sắm công khi create || update project
     */
    public function syncProcurement(array $data, $id)
    {
        $project = $this->model->findOrFail($id);
        return $project->procurementCategories()->sync($data['procurement_id']);
    }

    /**
     * @param $id
     * @param $decision_number_approve
     * @return mixed
     * Staff quyết định phê duyệt dự án
     */
    public function approveProject($id, $decision_number_approve)
    {
        return $this->model->findOrFail($id)->update([
            'approve_at' => now(),
            'decision_number_approve' => $decision_number_approve,
            'status' => ProjectStatus::RECEIVED->value,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * Staff quyết định reject dự án
     */
    public function rejectProject($id)
    {
        return $this->model->findOrFail($id)->update([
            'approve_at' => now(),
            'status' => ProjectStatus::REJECT->value,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * Khi người đăng tải project lựa chọn nhà thầu rồi bấm submit, cùng lúc đó phải update status , dùng hàm publishResultProject
     */
    public function publishResultProject($id)
    {
        return $this->model->findOrFail($id)->update([
            'status' => ProjectStatus::RESULTS_PUBLICED->value,
        ]);
    }
}
