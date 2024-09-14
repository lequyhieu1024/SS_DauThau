<?php

namespace App\Repositories;

class FeedbackComplaintRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\FeedbackComplaint::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function createFeedbackComplaint(array $data)
    {
        return $this->model::create($data);
    }

    public function findFeedbackComplaintById($id)
    {
        return $this->model->find($id);
    }

    public function updateFeedbackComplaint(array $data, $id)
    {
        $feedbackComplaint = $this->model->find($id);
        if ($feedbackComplaint) {
            $feedbackComplaint->update($data);
        }
        return $feedbackComplaint;
    }

    public function deleteFeedbackComplaint($id)
    {
        $feedbackComplaint = $this->model->find($id);
        if ($feedbackComplaint) {
            $feedbackComplaint->delete();
        }
        return $feedbackComplaint;
    }
}
