<?php

namespace App\Repositories;

use App\Models\BiddingResult;

class BiddingResultRepository extends BaseRepository
{
    public function getModel()
    {
        return BiddingResult::class;
    }

    public function filter($data)
    {
        if ($data['enterprise_id'] == null) {
            $query = $this->model->query();
        } else {
            $query = $this->model->where(function ($query) use ($data) {
                $query->where('enterprise_id', $data['enterprise_id']);
            });
        }
        // logic loc kqua dau thau
        if (isset($data['enterprise'])) {
            $query->where('enterprise_id', $data['enterprise']);
        }
        if (isset($data['project'])) {
            $query->where('project_id', $data['project']);
        }
        if (isset($data['bid_document'])) {
            $query->where('bid_document_id', $data['bid_document']);
        }

        if (isset($data['decision_date_start'])) {
            $query->whereDate('decision_date', '>=', $data['decision_date_start']);
        }

        if (isset($data['decision_date_end'])) {
            $query->whereDate('decision_date', '<=', $data['decision_date_end']);
        }

//        if (isset($data['investor'])) {
//            $query->whereHas('investor', function ($q) use ($data) {
//                $q->where('id', $data['investor']);
//            });
//        }
//
//        if (isset($data['tenderer'])) {
//            $query->whereHas('tenderer', function ($q) use ($data) {
//                $q->where('id', $data['tenderer']);
//            });
//        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function countBiddingResult()
    {
        return [
            'name' => 'Kết quả đấu thầu',
            'total_bidding_result' => $this->model->count()
        ];
    }
}
