<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ProjectStatus;
use App\Events\ProjectCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectFormRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TreeSelectCollection;
use App\Jobs\sendApproveProjectJob;
use App\Repositories\AttachmentRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public $projectRepository;
    public $attachmentRepository;
    public $enterpriseRepository;
    public $userRepository;
    public $staffRepository;

    public function __construct(ProjectRepository $projectRepository, AttachmentRepository $attachmentRepository, EnterpriseRepository $enterpriseRepository, UserRepository $userRepository, StaffRepository $staffRepository)
    {
        $this->middleware(['permission:list_project'])->only('index');
        $this->middleware(['permission:create_project'])->only(['store']);
        $this->middleware(['permission:update_project'])->only(['update']);
        $this->middleware(['permission:detail_project'])->only('show');
        $this->middleware(['permission:destroy_project'])->only('destroy');
        $this->projectRepository = $projectRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->userRepository = $userRepository;
        $this->staffRepository = $staffRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $data['enterprise_id'] = $this->userRepository->getEnterpriseId(Auth::user()->id);
        $projects = $this->projectRepository->filter($data);
        return response([
            'result' => true,
            'message' => "Lấy danh sách dự án thành công",
            'data' => new ProjectCollection($projects),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectFormRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $project = $this->projectRepository->create($data);
            $this->projectRepository->syncProcurement($data, $project->id);
            $this->projectRepository->syncIndustry($data, $project->id);
            if($request->hasFile('files')) {
                $newNameProject = __(convertVietnameseToEnglish($project->name));
                $this->attachmentRepository->createAttachment($request->file('files'), $project->id, auth()->user()->id, $newNameProject);
            }
            event(new ProjectCreated($project));
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Tạo dự án thành công',
                'data' => new ProjectResource($project),
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $th->getMessage(),
                'errors' => $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $project = $this->projectRepository->findOrFail($id);
            return response([
                'result' => true,
                'message' => 'Lấy thông tin dự án thành công',
                'data' => new ProjectResource($project)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy dự án này'
            ], 404);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'message' => $th->getMessage(),
                'errors' => $th
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectFormRequest $request, string $id)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $this->projectRepository->update($data, $id);
            $project = $this->projectRepository->findOrFail($id);
            $this->projectRepository->syncProcurement($data, $project->id);
            $this->projectRepository->syncIndustry($data, $id);
            if($request->hasFile('files')) {
                $newNameProject = __(convertVietnameseToEnglish($project->name));
                $this->attachmentRepository->createAttachment($request->file('files'), $project->id, auth()->user()->id, $newNameProject);
            }

            DB::commit();
            return response([
                'result' => true,
                'message' => 'Cập nhật dự án thành công',
                'data' => new ProjectResource($project)
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $th->getMessage(),
                'errors' => $th
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $project = $this->projectRepository->findOrFail($id);
            if ($project->children()->count() > 0) {
                return response([
                    'result' => false,
                    'message' => "Không thể xóa dự án khi đã có các gói thầu",
                ], 409);
            }
            $project->delete();
//            $project->industries()->detach();// soft delete nen khong detach
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Xóa dự án thành công',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'result' => false,
                'message' => 'Không tìm thấy dự án này'
            ], 404);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([
                'result' => false,
                'message' => $th->getMessage(),
                'errors' => $th
            ], 500);
        }
    }

    public function getNameAndIds()
    {
        return response([
            'result' => true,
            'message' => "Lấy danh sách dự án và gói thầu thành công",
            'data' => $this->projectRepository->getNameAndIdsProject()// new TreeSelectCollection($this->projectRepository->getNameAndIdsProject())
        ], 200);
    }

    public function approveProject(Request $request, $id)
    {
        $rules = [
            'decision_number_approve' => 'required|max:255',
            'status' => 'required|in:3,2',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $project = $this->projectRepository->findOrFail($id);
        if ($request->status == ProjectStatus::REJECT->value) {
            $this->projectRepository->rejectProject($id);
            sendApproveProjectJob::dispatch($project->investor->user, $project->tenderer->user, $request->notes, ProjectStatus::REJECT->value);
            return response([
                'result' => true,
                'message' => 'Từ chối dự án thành công',
                'data' => [
                    'approve_at' => now(),
                    'status' => "Từ chối"
                ]
            ], 200);
        }
        $this->projectRepository->approveProject($id, $request->decision_number_approve);
        sendApproveProjectJob::dispatch($project->investor->user, $project->tenderer->user, $request->notes, ProjectStatus::APPROVED->value);
        return response([
            'result' => true,
            'message' => 'Phê duyệt dự án thành công',
            'data' => [
                'approve_at' => now(),
                'decision_number_approve' => $request->decision_number_approve,
                'status' => 'Đã phê duyệt'
            ]
        ], 200);
    }

    public function getNameAndIdProjectHasBidingResult()
    {
        return response([
            'result' => true,
            'message' => 'Lấy danh sách dự án và gói thầu đã có kết quả đấu thầu thành công',
            'data' => new TreeSelectCollection($this->projectRepository->getNameAndIdProjectHasBiddingResult()),
        ], 200);
    }

    public function getProjectByStaff(Request $request) {
        $projects = $this->projectRepository->getProjectByStaff(Auth::user()->staff->id ?? 0, $request->all());
        return response([
            'result' => true,
            'message' => "Lấy danh sách dự án được giao cho nhân viên phê duyệt thành công",
            'data' => new ProjectCollection($projects)
        ], 200);
    }
}
