<?php

namespace App\Repositories;
use App\Models\Enterprise;
use App\Models\Project;
use App\Models\WorkProgress;

class WorkProgressRepository extends BaseRepository {
    public function getModel()
    {
        return WorkProgress::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['project'])) {
            $query->where('bidding_result_id', Project::find($data['project'])->biddingResult->id ?? 0);
        }

        if (isset($data['enterprise'])) {
            $query->where('bidding_result_id', Enterprise::find($data['enterprise'])->biddingResults->pluck('id')->toArray() ?? [0]);
        }

        if (isset($data['feedback'])) {
            $query->where('feedback', $data['feedback']);
        }


        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function syncTaskProgresses(array $data, $id)
    {
        $progress = $this->model->findOrFail($id);
        return $progress->taskProgresses()->sync($data['task_ids']);
    }

}
