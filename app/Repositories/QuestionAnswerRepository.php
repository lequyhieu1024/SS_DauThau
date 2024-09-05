<?php

namespace App\Repositories;

class QuestionAnswerRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\QuestionAnswer::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        return $query->with(['askedBy:id,name','answeredBy:id,name'])->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }


    public function deleteQuestionAnswer($id)
    {
        $questionAnswer = $this->model->find($id);
        if ($questionAnswer) {
            $questionAnswer->delete();
        }
        return $questionAnswer;
    }
}
