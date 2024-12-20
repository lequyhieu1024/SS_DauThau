<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Models\Enterprise;
use App\Models\FundingSource;
use App\Models\Industry;
use App\Models\Project;
use App\Models\SelectionMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends BaseRepository
{
    public function getModel()
    {
        return Project::class;
    }

    public function filter($data)
    {
        //        if ($data['enterprise_id'] == null) {
        //            $query = $this->model->with('children')->whereNull('parent_id');
        //        } else { // login với tài khoản doanh nghiệp thì chỉ xem được dự án của doanh nghiệp đó
        //            $query = $this->model->with('children')->whereNull('parent_id')->where(function ($query) use ($data) {
        //                $query->where('investor_id', $data['enterprise_id'])
        //                    ->orWhere('tenderer_id', $data['enterprise_id']);
        //            });
        //        }
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

        if (isset($data['staff'])) {
            $query->where('staff_id',$data['staff']);
        }

        if (isset($data['win'])) {
            $query->whereHas('biddingResult', function ($query) use ($data) {
                $query->where('enterprise_id', $data['win']);
            });
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    /**
     * @return mixed
     * Lấy ra những dự án đã hết hạn nộp hồ sơ rồi update trạng trạng thái từ RECEIVED -> SELECTING_CONTRUCTOR
     */
    public function getOverdueProjectSubmission()
    {
        return $this->model->where('bid_submission_end', "<", Carbon::now())->where("status", ProjectStatus::APPROVED->value)->get();
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
            'status' => ProjectStatus::APPROVED->value,
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

    public function getNameAndIdsProject()
    {
        return $this->model->select('id', 'name')
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->select('id', 'name', 'parent_id');
            }])->orderBy('id','desc')
            ->get();
    }


    public function getNameAndIdProjectHasBiddingResult()
    {
        return $this->model
            ->whereHas('BiddingResult')
            ->select('id', 'name')
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->select('id', 'name', 'parent_id');
            }])
            ->orderBy('id', 'desc')
            ->get();
    }


    public function getProjectCountByIndustry()
    {
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                'result' => true,
                'message' => 'Không có dự án nào',
                'data' => []
            ];
        }

        $industries = Industry::withCount('projects')
            ->orderByDesc('projects_count')
            ->get();

        $topIndustries = $industries->take(10);
        $otherIndustries = $industries->skip(10);

        $data = [];

        foreach ($topIndustries as $industry) {
            $data[] = [
                'name' => $industry->name,
                'value' => $industry->projects_count
            ];
        }

        $otherProjectCount = $otherIndustries->sum('projects_count');
        if ($otherProjectCount > 0) {
            $data[] = [
                'name' => 'Ngành khác',
                'value' => $otherProjectCount
            ];
        }

        return $data;
    }

    public function getProjectPercentageByFundingSource()
    {
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                'result' => true,
                'message' => 'Không có dự án nào',
                'data' => []
            ];
        }

        $rawData = FundingSource::query()
            ->selectRaw('funding_sources.name as funding_source_name, COUNT(projects.id) as projects_count')
            ->leftJoin('projects', 'funding_sources.id', '=', 'projects.funding_source_id')
            ->groupBy('funding_sources.id', 'funding_sources.name')
            ->orderByDesc('projects_count')
            ->get();


        $topFundingSources = $rawData->take(10);
        $otherFundingSourcesCount = $rawData->skip(10)->sum('projects_count');

        $data = $topFundingSources->map(function ($fundingSource) {
            return [
                'name' => $fundingSource->funding_source_name,
                'value' => $fundingSource->projects_count,
            ];
        })->toArray();

        if ($otherFundingSourcesCount > 0) {
            $data[] = [
                'name' => 'Nguồn tài trợ khác',
                'value' => $otherFundingSourcesCount,
            ];
        }

        return $data;
    }



    public function getDomesticPercentage()
    {
        // Tổng số dự án
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                ['name' => 'Trong nước', 'value' => 0],
                ['name' => 'Quốc tế', 'value' => 0]
            ];
        }

        // trong nước
        $domesticCount = $this->model::where('is_domestic', true)->count();
        // quốc tế
        $internationalCount = $this->model::where('is_domestic', false)->count();

        return [
            ['name' => 'Trong nước', 'value' => $domesticCount],
            ['name' => 'Quốc tế', 'value' => $internationalCount]
        ];
    }

    public function getProjectPercentageBySubmissionMethod()
    {
        // Tổng số dự án
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                ['name' => 'Online', 'value' => 0],
                ['name' => 'Trực tiếp', 'value' => 0]
            ];
        }

        // Online
        $onlineCount = $this->model::where('submission_method', 'online')->count();

        // Trực tiếp
        $inPersonCount = $this->model::where('submission_method', 'in_person')->count();

        return [
            ['name' => 'Online', 'value' => $onlineCount],
            ['name' => 'Trực tiếp', 'value' => $inPersonCount]
        ];
    }

    public function getProjectPercentageBySelectionMethod()
    {
        // 1. Lấy tổng số dự án
        $totalProjects = $this->model::count();

        // Nếu không có dự án nào
        if ($totalProjects === 0) {
            return [
                'result' => true,
                'message' => 'Không có dự án nào',
                'data' => []
            ];
        }

        //
        $selectionMethods = SelectionMethod::withCount('projects')
            ->orderByDesc('projects_count')
            ->get();

        $topSelectionMethods = $selectionMethods->take(10);
        $otherSelectionMethods = $selectionMethods->skip(10);

        //
        foreach ($topSelectionMethods as $selectionMethod) {
            $data[] = [
                'name' => $selectionMethod->method_name,
                'value' => $selectionMethod->projects_count
            ];
        }

        //
        $otherSelectionMethods = $otherSelectionMethods->sum('projects_count');
        if ($otherSelectionMethods > 0) {
            $data[] = [
                'name' => 'Phương thức khác',
                'value' => $otherSelectionMethods
            ];
        }

        return $data;
    }

    // lấy tỷ lệ phân bổ dự án theo vai trò bên mời thầu, đầu tư và cả hai
    public function getProjectPercentageByTendererInvestor()
    {
        // Tổng số dự án
        $totalProjects = Project::count();

        // Nếu không có dự án nào
        if ($totalProjects === 0) {
            return [
                ['name' => 'Bên mời thầu', 'value' => 0],
                ['name' => 'Bên đầu tư', 'value' => 0],
                ['name' => 'Cả hai', 'value' => 0]
            ];
        }

        // dự án mà bên mời thầu khác nhà đầu tư
        $tendererCount = Project::whereColumn('tenderer_id', '!=', 'investor_id')->count();

        // dự án mà nhà đầu tư khác bên mời thầu
        $investorCount = Project::whereColumn('investor_id', '!=', 'tenderer_id')->count();

        // dự án mà bên mời thầu và nhà đầu tư là cùng một doanh nghiệp
        $bothCount = Project::whereColumn('tenderer_id', 'investor_id')->count();

        //
        // $tendererPercentage = ($tendererCount / $totalProjects) * 100;
        // $investorPercentage = ($investorCount / $totalProjects) * 100;
        // $bothPercentage = ($bothCount / $totalProjects) * 100;

        return [
            ['name' => 'Bên mời thầu', 'value' => $tendererCount],
            ['name' => 'Bên đầu tư', 'value' => $investorCount],
            ['name' => 'Cả hai', 'value' => $bothCount]
        ];
    }

    // lấy thời gian trung bình thực hiện dự án theo ngành
    public function getAverageProjectDurationByIndustry()
    {
        // lấy ra các ngành với dự án có start_time, end_time
        $industries = Industry::with(['projects' => function ($query) {
            $query->whereNotNull('start_time')->whereNotNull('end_time')
                ->select('start_time', 'end_time');
        }])->get();

        $data = [];
        foreach ($industries as $industry) {
            $totalDuration = 0;
            $projectCount = $industry->projects->count(); // số dự án theo ngành

            // lặp qua từng dự án theo ngành
            foreach ($industry->projects as $project) {
                $startDate = Carbon::parse($project->start_time);
                $endDate = Carbon::parse($project->end_time);
                $duration = $endDate->diffInDays($startDate); // chênh lệch ngày

                $totalDuration += $duration;
            }

            // trung bình theo ngày
            $averageDuration = $projectCount > 0 ? $totalDuration / $projectCount : 0;
            $data[] = [
                'name' => $industry->name,
                'value' => round($averageDuration, 2)
            ];
        }

        // Sắp xếp theo giá trị trung bình giảm dần
        usort($data, function ($a, $b) {
            return $b['value'] <=> $a['value'];
        });

        // Lấy 15 cột đầu tiên
        $top15 = array_slice($data, 0, 10);

        // Tổng hợp các ngành còn lại
        $others = array_slice($data, 10);
        $othersValue = 0;
        foreach ($others as $other) {
            $othersValue += $other['value'];
        }

        // Nếu có ngành "Còn lại", thêm vào mảng dữ liệu
        if (count($others) > 0) {
            $top15[] = [
                'name' => 'Còn lại',
                'value' => round($othersValue, 2)
            ];
        }

        return $top15;
    }


    // số doanh nghiệp nhà nước, ngoài nhà nước
    public function getEnterpriseByOrganizationType()
    {
        // nhà nước
        $stateOwnedCount = Enterprise::where('organization_type', 1)->count();

        // ngoài nhà nước
        $privateOwnedCount = Enterprise::where('organization_type', 2)->count();

        return [
            ['name' => 'Nhà nước', 'value' => $stateOwnedCount],
            ['name' => 'Ngoài nhà nước', 'value' => $privateOwnedCount],
        ];
    }

    // 10 đơn vị mời thầu có tổng gói thầu nhiều nhất theo số lượng
    public function getTopTenderersByProjectCount()
    {
        // đếm tổng số lượng dự án cho từng đơn vị mời thầu
        $topTenderers = Project::with('tenderer.user')
            ->select('tenderer_id')
            ->selectRaw('COUNT(*) as project_count')
            ->groupBy('tenderer_id')
            ->orderByDesc('project_count')
            ->get();

        $topTenderers = $topTenderers->take(10);
        // $otherTenderers = $tenderers->skip(10);

        $data = [];
        foreach ($topTenderers as $tenderer) {
            $data[] = [
                'name' => $tenderer->tenderer->user->name,
                'value' => $tenderer->project_count
            ];
        }

        // tổng số lượng gói thầu của các đơn vị còn lại
        // $otherCount = $otherTenderers->sum('project_count');
        // if ($otherCount > 0) {
        //     $data[] = [
        //         'name' => 'Khác',
        //         'value' => $otherCount
        //     ];
        // }

        return $data;
    }

    // 10 đơn vị mời thầu có tổng gói thầu nhiều nhất theo giá
    public function getTopTenderersByProjectTotalAmount()
    {
        // tổng giá từng dự án theo đơn vị mời thầu
        $topTenderers = Project::with('tenderer.user')
            ->select('tenderer_id')
            ->selectRaw('SUM(total_amount) as total')
            ->groupBy('tenderer_id')
            ->orderByDesc('total')
            ->get();

        $topTenderers = $topTenderers->take(10); //
        // $otherTenderers = $tenderers->skip(10);

        $data = [];
        foreach ($topTenderers as $tenderer) {
            $data[] = [
                'name' => $tenderer->tenderer->user->name,
                'value' => $tenderer->total
            ];
        }

        //
        // $otherTotal = $otherTenderers->sum('total');
        // if ($otherTotal > 0) {
        //     $data[] = [
        //         'name' => 'Khác',
        //         'value' => $otherTotal
        //     ];
        // }

        return $data;
    }

    // 10 đơn vị trúng thầu nhiều nhất theo từng phần
    public function getTopInvestorsByProjectPartial()
    {
        //
        $topInvestors = Project::with('investor.user')
            ->whereNotNull('parent_id')
            ->select('investor_id')
            ->selectRaw('COUNT(investor_id) as investor_count')
            ->groupBy('investor_id')
            ->orderByDesc('investor_count')
            ->take(10)
            ->get();

        $data = [];
        foreach ($topInvestors as $investor) {
            $data[] = [
                'name' => $investor->investor->user->name,
                'value' => $investor->investor_count
            ];
        }

        return $data;
    }

    // 10 đơn vị trúng thầu nhiều nhất theo trọn gói
    public function getTopInvestorsByProjectFull()
    {
        //
        $topInvestors = Project::with('investor.user')
            ->whereNull('parent_id')
            ->select('investor_id')
            ->selectRaw('COUNT(investor_id) as investor_count')
            ->groupBy('investor_id')
            ->orderByDesc('investor_count')
            ->take(10)
            ->get();

        $data = [];
        foreach ($topInvestors as $investor) {
            $data[] = [
                'name' => $investor->investor->user->name,
                'value' => $investor->investor_count
            ];
        }

        return $data;
    }

    // 10 đơn vị trúng thầu nhiều nhất theo giá
    public function getTopInvestorsByProjectTotalAmount()
    {
        //
        $topInvestors = Project::with('investor.user')
            ->select('investor_id')
            ->selectRaw('SUM(total_amount) as total')
            ->groupBy('investor_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $data = [];
        foreach ($topInvestors as $investor) {
            $data[] = [
                'name' => $investor->investor->user->name,
                'value' => $investor->total
            ];
        }

        return $data;
    }


    // Biểu đồ cột: total amount
    public function getBarChartDataTotalAmount($projectIds)
    {
        return $this->model->whereIn('id', $projectIds)
            ->select('id', 'name')
            ->selectRaw('
            CASE
                WHEN parent_id IS NULL THEN COALESCE((SELECT SUM(amount) FROM projects AS child WHERE child.parent_id = projects.id), amount)
                ELSE amount
            END AS total_amount
        ')
            ->get();
    }

    // Biểu đồ cột: construction time
    public function getBarChartDataComparingConstructionTime($projectIds)
    {
        $projects = $this->model->whereIn('id', $projectIds)
            ->select('id', 'name', 'start_time', 'end_time')
            ->get();

        $data = $projects->map(function ($project) {
            $startDate = Carbon::parse($project->start_time);
            $endDate = Carbon::parse($project->end_time);
            $duration = $endDate->diffInDays($startDate);

            return [
                'id' => $project->id,
                'name' => $project->name,
                'duration' => $duration,
            ];
        });

        return $data;
    }

    // Biểu đồ cột: bid submission time
    public function getBarChartDataComparingBidSubmissionTime($projectIds)
    {
        $projects = $this->model->whereIn('id', $projectIds)
            ->select('id', 'name', 'bid_submission_start', 'bid_submission_end')
            ->get();

        $data = $projects->map(function ($project) {
            $startDate = Carbon::parse($project->bid_submission_start);
            $endDate = Carbon::parse($project->bid_submission_end);
            $duration = $endDate->diffInDays($startDate);

            return [
                'id' => $project->id,
                'name' => $project->name,
                'duration' => $duration,
            ];
        });

        return $data;
    }

    // Biểu đồ tròn: total amount
    public function getPieChartDataAmountAndTotalAmount($projectIds)
    {
        $projects = $this->model->whereIn('id', $projectIds)
            ->select('id', 'name', 'parent_id', 'total_amount')
            ->with('children:id,name,parent_id,total_amount')
            ->get();

        $data = $projects->map(function ($project) {
            $projectData = [
                'id' => $project->id,
                'name' => $project->name,
                'value' => $project->children->isNotEmpty() ? $project->children->sum('total_amount') : $project->total_amount,
            ];

            if ($project->children->isNotEmpty()) {
                $projectData['children'] = $project->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'value' => $child->total_amount,
                    ];
                });
            }

            return $projectData;
        });

        return $data;
    }

    public function getBarChartDataBidderCount($projectIds)
    {
        // Get all selected projects
        $projects = $this->model->whereIn('projects.id', $projectIds)
            ->select('projects.id', 'projects.name')
            ->get();

        // Get bidder counts for the selected projects
        $bidderCounts = $this->model->whereIn('projects.id', $projectIds)
            ->leftJoin('bid_documents', 'projects.id', '=', 'bid_documents.project_id')
            ->select('projects.id', DB::raw('COUNT(bid_documents.id) as bidder_count'))
            ->groupBy('projects.id')
            ->pluck('bidder_count', 'projects.id');

        // Merge projects with bidder counts
        $data = $projects->map(function ($project) use ($bidderCounts) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'bidder_count' => $bidderCounts->get($project->id, 0),
            ];
        });

        return $data;
    }

    public function projectsStatusPerMonth($year)
    {
        $data = [
            'completed' => [],
            'approved' => [],
            'opened_bidding' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $startOfMonth = Carbon::createFromDate($year, $i, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, $i, 1)->endOfMonth();

            $data['completed'][] =[
                'Tháng ' . $i => $this->model
                    ->where('end_time', '<', Carbon::now())
                    ->whereYear('end_time', '=', $year)
                    ->whereMonth('end_time', '=', $i)
                    ->count()
            ];

            $data['approved'][] =[
                'Tháng ' . $i => $this->model
                    ->whereBetween('approve_at', [$startOfMonth, $endOfMonth])
                    ->where('status', ProjectStatus::APPROVED->value)
                    ->count()
            ];

            $data['opened_bidding'][] = [
                'Tháng ' . $i => $this->model
                    ->whereBetween('bid_opening_date', [$startOfMonth, $endOfMonth])
                    ->count()
            ];
        }

        return $data;
    }

    public function getProjectByStaff($staff_id, $data) {
        $query = $this->model->query();
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
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

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        return $query->where('staff_id', $staff_id)->orderBy('status', 'ASC')->orderBy('id', 'DESC')->paginate($data['size'] ?? 10);
    }

    public function countProjects(){
        return [
            'total_project' => $this->model->count(),
            'total_await_project' => $this->model->where('status', ProjectStatus::AWAITING->value)->count(),
            'total_reject_project' => $this->model->where('status', ProjectStatus::REJECT->value)->count(),
            'total_approve_project' => $this->model->where('status', ProjectStatus::APPROVED->value)->count()
        ];
    }
}
