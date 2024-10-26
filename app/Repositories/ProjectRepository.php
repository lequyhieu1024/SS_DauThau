<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
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
        if ($data['enterprise_id'] == null) {
            $query = $this->model->with('children')->whereNull('parent_id');
        } else { // login với tài khoản doanh nghiệp thì chỉ xem được dự án của doanh nghiệp đó
            $query = $this->model->with('children')->whereNull('parent_id')->where(function ($query) use ($data) {
                $query->where('investor_id', $data['enterprise_id'])
                    ->orWhere('tenderer_id', $data['enterprise_id']);
            });
        }
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

    public function getProjectPercentageByIndustry()
    {
        // Lấy tổng số dự án
        $totalProjects = $this->model::count();

        // Nếu không có dự án nào
        if ($totalProjects === 0) {
            return [
                'result' => true,
                'message' => 'Không có dự án nào',
                'data' => []
            ];
        }

        // Lấy số lượng dự án theo ngành
        $industries = Industry::withCount('projects')->get();
        $data = [];
        foreach ($industries as $industry) {
            $projectCount = $industry->projects_count;
            $percentage = ($projectCount / $totalProjects) * 100;
            $data[$industry->name] = round($percentage, 2);
        }

        return $data;
    }

    public function getProjectPercentageByFundingSource()
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

        // Lấy số lượng dự án theo nguồn vốn
        $fundingSources = FundingSource::withCount('projects')->get();
        $data = [];
        foreach ($fundingSources as $fundingSource) {
            $projectCount = $fundingSource->projects_count;
            $percentage = ($projectCount / $totalProjects) * 100;
            $data[$fundingSource->name] = round($percentage, 2);
        }

        return $data;
    }

    public function getDomesticPercentage()
    {
        // Tổng số dự án
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                'Trong nước' => 0,
                'Quốc tế' => 0,
            ];
        }

        // trong nước
        $domesticCount = $this->model::where('is_domestic', true)->count();
        $domesticPercentage = ($domesticCount / $totalProjects) * 100;

        // quốc tế
        $internationalPercentage = 100 - $domesticPercentage;

        return [
            'Trong nước' => round($domesticPercentage, 2),
            'Quốc tế' => round($internationalPercentage, 2),
        ];
    }

    public function getProjectPercentageBySubmissionMethod()
    {
        // Tổng số dự án
        $totalProjects = $this->model::count();

        if ($totalProjects === 0) {
            return [
                'Online' => 0,
                'Trực tiếp' => 0,
            ];
        }

        // Online
        $onlineCount = $this->model::where('submission_method', 'online')->count();
        $onlinePercentage = ($onlineCount / $totalProjects) * 100;

        // Trực tiếp
        $inPersonPercentage = 100 - $onlinePercentage;
        return [
            'Online' => round($onlinePercentage, 2),
            'Trực tiếp' => round($inPersonPercentage, 2),
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

        // Lấy tỷ lệ dự án theo nguồn vốn
        $selectionMethods = SelectionMethod::withCount('projects')->get();
        $data = [];
        foreach ($selectionMethods as $selectionMethod) {
            $projectCount = $selectionMethod->projects_count;
            $percentage = ($projectCount / $totalProjects) * 100;
            $data[$selectionMethod->method_name] = round($percentage, 2);
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
                'Bên mời thầu' => 0,
                'Bên đầu tư' => 0,
                'Cả hai' => 0,
            ];
        }

        // dự án mà bên mời thầu khác nhà đầu tư
        $tendererCount = Project::whereColumn('tenderer_id', '!=', 'investor_id')->count();

        // dự án mà nhà đầu tư khác bên mời thầu
        $investorCount = Project::whereColumn('investor_id', '!=', 'tenderer_id')->count();

        // dự án mà bên mời thầu và nhà đầu tư là cùng một doanh nghiệp
        $bothCount = Project::whereColumn('tenderer_id', 'investor_id')->count();

        // 
        $tendererPercentage = ($tendererCount / $totalProjects) * 100;
        $investorPercentage = ($investorCount / $totalProjects) * 100;
        $bothPercentage = ($bothCount / $totalProjects) * 100;

        return [
            'Bên mời thầu' => round($tendererPercentage, 2),
            'Bên đầu tư' => round($investorPercentage, 2),
            'Cả hai' => round($bothPercentage, 2)
        ];
    }
}
