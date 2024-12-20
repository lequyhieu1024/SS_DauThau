<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\BiddingResult;
use App\Models\Employee;
use App\Models\Enterprise;
use App\Models\Evaluate;
use App\Models\FundingSource;
use App\Models\Industry;
use App\Models\Project;
use Carbon\Carbon;

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
        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function syncIndustry(array $data, $id)
    {
        $enterprise = $this->model->findOrFail($id);
        return $enterprise->industries()->sync($data['industry_id']);
    }

    //chart
    public function enterpriseActive()
    {
        return $this->model->where('is_active', 1);
    }

    /*
     * parameter $id is enterprise ID
     * this function is count employee of enterprises
     */
    public function employeeQtyStatisticByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

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
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();
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
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();
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
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

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
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

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
                'difficulty_label' => $this->getDifficultyLabel($averageDifficulty)
            ];
        }

        return $data;
    }

    public function averageDifficultyLevelTasksByEmployee($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $employees = Employee::whereIn('id', $ids)->with('tasks')->get();

        $difficultyLevel = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            'veryhard' => 4,
        ];

        foreach ($employees as $employee) {
            $totalDifficulty = 0;
            $taskCount = 0;
            foreach ($employee->tasks as $task) {
                $difficultyValue = $difficultyLevel[$task->difficulty_level] ?? 0;
                $totalDifficulty += $difficultyValue;
                $taskCount++;
            }
            $averageDifficulty = $taskCount > 0 ? round($totalDifficulty / $taskCount, 2) : 0;

            $data[] = [
                'employee_name' => $employee->name,
                'average_difficulty' => $averageDifficulty,
                'difficulty_label' => $this->getDifficultyLabel($averageDifficulty)
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

    public function getFeedbackLabel($val)
    {
        if ($val === 0) return 'Chưa có đánh giá';
        if ($val <= 1.5) return 'Tệ';
        if ($val <= 2.5) return 'Bình thường';
        if ($val <= 3.5) return 'Tốt';
        if ($val <= 4.5) return 'Rất tốt';
        return 'Xuất sắc';
    }

    public function averageFeedbackByEmployee($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $employees = Employee::whereIn('id', $ids)->get();

        $feedbackLevel = [
            'poor' => 1,
            'medium' => 2,
            'good' => 3,
            'verygood' => 4,
            'excellent' => 5,
        ];

        foreach ($employees as $employee) {
            $totalFeedback = 0;
            $feedbackCount = 0;
            foreach ($employee->tasks as $task) {
                $feedbackValue = $feedbackLevel[$task->pivot->feedback] ?? 0;
                $totalFeedback += $feedbackValue;
                $feedbackCount++;
            }
            $averageFeedback = $feedbackCount > 0 ? round($totalFeedback / $feedbackCount, 2) : 0;

            $data[] = [
                // 'totalFeedback' => $totalFeedback,
                // 'feedbackCount' => $feedbackCount,
                'employee_name' => $employee->name,
                'average_feedback' => $averageFeedback,
                'feedback_label' => $this->getFeedbackLabel($averageFeedback)
            ];
        }

        return $data;
    }

    public function topEnterprisesHaveCompletedProjectsByIndustry($id)
    {
        $industry = Industry::where('is_active', 1)->find($id);
        $topEnterprises = Project::whereHas('industries', function ($query) use ($id) {
            $query->where('industries.id', $id);
        })
            ->where('status', 3)
            ->where('end_time', '<', Carbon::now())
            ->selectRaw('investor_id, COUNT(*) as completed_projects_count')
            ->groupBy('investor_id')
            ->orderByDesc('completed_projects_count')
            ->take(10)
            ->with('investor')
            ->get();

        $data = [];
        foreach ($topEnterprises as $project) {
            $data[] = [
                'enterprise_name' => $project->investor->user->name,
                'completed_projects_count' => $project->completed_projects_count,
            ];
        }

        return response()->json([
            'result' => true,
            'message' => '10 doanh nghiệp có số lượng dự án hoàn thành nhiều nhất theo ngành ' . $industry->name,
            'data' =>  $data
        ], 200);
    }

    public function topEnterprisesHaveCompletedProjectsByFundingSource($id)
    {
        $fundingSource = FundingSource::where('is_active', 1)->find($id);
        $topEnterprises = Project::where('funding_source_id', $id)
            ->where('status', ProjectStatus::APPROVED->value)
            ->selectRaw('investor_id, COUNT(*) as completed_projects_count')
            ->groupBy('investor_id')
            ->orderByDesc('completed_projects_count')
            ->take(10)
            ->with('investor')
            ->get();

        $data = [];
        foreach ($topEnterprises as $project) {
            $data[] = [
                'enterprise_name' => $project->investor->user->name,
                'completed_projects_count' => $project->completed_projects_count,
            ];
        }

        return response()->json([
            'result' => true,
            'message' => '10 doanh nghiệp có số lượng dự án hoàn thành theo lĩnh vực mua sắm công ' . $fundingSource->name,
            'data' => $data
        ], 200);
    }

    public function timeJoiningWebsite($year)
    {
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $startOfMonth = Carbon::createFromDate($year, $i, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, $i, 1)->endOfMonth();

            $data['Tháng ' . $i] = $this->model->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }
        return $data;
    }

    public function projectCompletedByEnterprise($ids, $year)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            // Dự án hoàn thành của doanh nghiệp trong năm được chọn
            $completedProjects = Project::where('investor_id', $enterprise->id)
                ->where('status', ProjectStatus::APPROVED->value)
                ->whereYear('end_time', $year)
                ->selectRaw('MONTH(end_time) as month, COUNT(*) as completed_count')
                ->groupBy('month')
                ->pluck('completed_count', 'month');

            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                if (isset($completedProjects[$month]) && $completedProjects[$month] > 0) {
                    $monthlyData[] = [
                        'month' => $month,
                        'completed_projects' => $completedProjects[$month],
                    ];
                }
            }

            $data[] = [
                'enterprise_id' => $enterprise->id,
                'enterprise_name' => $enterprise->user->name,
                'year' => $year,
                'monthly_data' => $monthlyData,
            ];
        }

        return $data;
    }

    public function projectWonByEnterprise($ids, $year)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            // Dự án trúng thầu của doanh nghiệp trong năm được chọn
            $wonProjects = BiddingResult::where('enterprise_id', $enterprise->id)
                ->whereYear('decision_date', $year)
                ->selectRaw('MONTH(decision_date) as month, COUNT(*) as won_count')
                ->groupBy('month')
                ->pluck('won_count', 'month');

            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                if (isset($wonProjects[$month]) && $wonProjects[$month] > 0) {
                    $monthlyData[] = [
                        'month' => $month,
                        'won_projects' => $wonProjects[$month],
                    ];
                }
            }

            $data[] = [
                'enterprise_id' => $enterprise->id,
                'enterprise_name' => $enterprise->user->name,
                'year' => $year,
                'monthly_data' => $monthlyData,
            ];
        }

        return $data;
    }

    public function evaluationsStatisticsByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            $evaluationStats = Evaluate::where('enterprise_id', $enterprise->id)
                ->selectRaw('COUNT(*) as total_evaluations, AVG(score) as average_score')
                ->first();

            $data[] = [
                'enterprise_id' => $enterprise->id,
                'enterprise_name' => $enterprise->user->name,
                'total_evaluations' => $evaluationStats->total_evaluations ?? 0,
                'average_score' => round($evaluationStats->average_score, 2) ?? 0,
            ];
        }

        return $data;
    }

    public function reputationsStatisticsByEnterprise($ids)
    {
        $data = [];
        $ids = collect($ids)->flatten()->unique()->toArray();
        $enterprises = $this->enterpriseActive()->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {

            $data[] = [
                'enterprise_id' => $enterprise->id,
                'enterprise_name' => $enterprise->user->name,
                'prestige_score' => $enterprise->reputation->prestige_score ?? 100,
                'blacklist_count' => $enterprise->reputation->number_of_blacklist ?? 0,
                'ban_count' => $enterprise->reputation->number_of_ban ?? 0,
            ];
        }

        return $data;
    }

    public function countEnterprises()
    {
        return [
            'total_enterprises' =>$this->model->count(),
            'total_active_enterprises' =>$this->model->where('is_active', 1)->count(),
            'total_inactive_enterprises' =>$this->model->where('is_active', 0)->count(),
        ];
    }

    // biểu đồ so sánh trình độ học vấn của nhân viên khi so sánh doanh nghiệp
    public function employeeEducationLevelStatisticByEnterprises(array $ids)
    {
        $education_levels = ['primary_school', 'secondary_school', 'high_school', 'college', 'university', 'after_university'];
        $data = [];

        $enterprises = $this->model->whereIn('id', $ids)->get();

        foreach ($enterprises as $enterprise) {
            $enterpriseData = [
                'enterprise_name' => $enterprise->user->name,
                'education_levels' => []
            ];

            foreach ($education_levels as $education_level) {
                $enterpriseData['education_levels'][$education_level] = $enterprise->employees->where('education_level', $education_level)->count();
            }

            $data[] = $enterpriseData;
        }

        return $data;
    }
}
