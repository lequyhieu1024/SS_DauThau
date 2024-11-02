<?php

namespace App\Repositories;

use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Enterprise;

class EnterpriseRepository extends BaseRepository
{
    public function getModel()
    {
        return Enterprise::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['status'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('account_ban_at', $data['account_ban_at']);
            });
        }
        if (isset($data['name'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            });
        }
        if (isset($data['avg_document_rating_from'])) {
            $query->where('avg_document_rating', '>=', $data['avg_document_rating_from']);
        }
        if (isset($data['avg_document_rating_to'])) {
            $query->where('avg_document_rating', '<=', $data['avg_document_rating_to']);
        }
        if (isset($data['organization_type'])) {
            $query->where('organization_type', '=', $data['organization_type']);
        }
        if (isset($data['is_active'])) {
            $query->where('is_active', '=', $data['is_active']);
        }
        if (isset($data['is_blacklist'])) {
            $query->where('is_blacklist', '=', $data['is_blacklist']);
        }
        if (isset($data['industry_ids']) && is_array($data['industry_ids'])) {
            $data['industry_ids'] = array_filter($data['industry_ids'], function ($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($data['industry_ids'])) {
                $query->whereHas('industries', function ($query) use ($data) {
                    $query->whereIn('industry_id', $data['industry_ids']);
                });
            }
        }
        return $query->paginate($data['size'] ?? 10);
    }

    public function syncIndustry(array $data, $id)
    {
        $enterprise = $this->model->findOrFail($id);
        return $enterprise->industries()->sync($data['industry_id']);
    }

    //chart

    /*
     * parameter $id is enterprise ID
     * this function is count employee of enterprises
     */
    public function employeeQtyStatisticByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            $data[] = [
                'enterprise' => $enterprise->user->name,
                'quantityEmployee' => $enterprise->employees->count(),
            ];
        }
        return $data;
    }

    public function employeeEducationLevelStatisticByEnterprise($id)
    {
        $education_levels = ['primary_school', 'secondary_school', 'high_school', 'college', 'university', 'after_university'];
        $data = [];
        foreach ($education_levels as $education_level) {
            $data[$education_level] = $this->model->findOrFail($id)->employees->where('education_level', $education_level)->count();
        }
        return $data;
    }

    public function employeeSalaryStatisticByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->get();
        foreach ($enterprises as $enterprise) {
            $data[] = [
                'enterprise' => $enterprise->user->name,
                'salaryHighest' => $enterprise->employees->max('salary') ?? 0,
                'salaryLowest' => $enterprise->employees->min('salary') ?? 0,
                'salaryAvg' => $enterprise->employees->avg('salary') ?? 0,
            ];
        }
        return $data;
    }

    public function employeeAgeStatisticByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->get();
        foreach ($enterprises as $enterprise) {
            $ages = $enterprise->employees->map(function ($employee) {
                return \Carbon\Carbon::parse($employee->birthday)->diffInYears(\Carbon\Carbon::parse(now()));
            });
            $data[] = [
                'enterprise' => $enterprise->user->name,
                'ageHighest' => $ages->max(),
                'ageLowest' => $ages->min(),
                'ageAvg' => $ages->avg(),
            ];
        }
        return $data;
    }

    public function tendererAndInvestorProjectStatisticByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            $tendererProjectCount = $this->getProjectCount($enterprise, true);
            $investorProjectCount = $this->getProjectCount($enterprise, false);

            $data[] = [
                'enterprise' => $enterprise->user->name,
                'tendererProjectCount' => $tendererProjectCount,
                'investorProjectCount' => $investorProjectCount
            ];
        }

        return $data;
    }

    private function getProjectCount($enterprise, $isTenderer)
    {
        $projects = $isTenderer ? $enterprise->tendererProjects : $enterprise->investorProjects;

        $parentProjects = $projects->filter(function ($project) {
            return is_null($project->parent_id);
        });

        $children = $projects->filter(function ($project) {
            return !is_null($project->parent_id);
        });

        $filteredChildren = $children->filter(function ($child) use ($parentProjects) {
            $parent = $parentProjects->firstWhere('id', $child->parent_id);
            return !$parent || $parent->tendered_id !== $child->tenderer_id;
        });

        $parentCount = $parentProjects->count();
        $childCount = $filteredChildren->count();

        return $parentCount + $childCount;
    }

    public function biddingResultStatisticsByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            $biddingResults = $enterprise->biddingResults;

            $filteredResults = $biddingResults->filter(function ($result) {
                return is_null($result->parent_id) || $result->parent_id !== $result->id;
            });

            $wonCount = $filteredResults->count();

            $totalWinningAmount = $filteredResults->sum('win_amount');

            $averageWinningAmount = $wonCount > 0 ? $totalWinningAmount / $wonCount : 0;

            $data[] = [
                'enterprise' => $enterprise->user->name,
                'numberProjectWinning' => $wonCount,
                'averageWinningAmount' => $averageWinningAmount,
                'totalWinningAmount' => $totalWinningAmount,
            ];
        }

        return $data;
    }

    public function averageDifficultyLevelTasksByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->model->whereIn('id', $ids)->with('employees.tasks')->get();

        $difficultyLevel = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            'veryhard' => 4,
        ];

        foreach ($enterprises as $enterprise) {
            $totalDifficulty = 0;
            $taskCount = 0;

            foreach ($enterprise->employees as $employee) {
                foreach ($employee->tasks as $task) {
                    $difficultyValue = $difficultyLevel[$task->difficulty_level] ?? 0;
                    $totalDifficulty += $difficultyValue;
                    $taskCount++;
                }
            }

            $averageDifficulty = $taskCount > 0 ? round($totalDifficulty / $taskCount, 2) : 0;

            $data[] = [
                // 'totalDifficulty' => $totalDifficulty,
                // 'taskCount' => $taskCount,
                'enterprise_name' => $enterprise->user->name,
                'average_difficulty' => $averageDifficulty,
                'difficulty_label'=>$this->getDifficultyLabel($averageDifficulty)
            ];
        }

        return $data;
    }

    public function getDifficultyLabel($val)
    {
        if ($val === 0) return 'Chưa có nhiệm vụ';
        if ($val <= 1.5) return 'Dễ';
        if ($val <= 2.5) return 'Trung bình';
        if ($val <= 3.5) return 'Khó';
        return 'Rất khó';
    }
}
