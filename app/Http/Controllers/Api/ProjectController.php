<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\ProjectRepository;
use App\Http\Requests\ProjectFormRequest;
use App\Http\Resources\ProjectCollection;

class ProjectController extends Controller
{
    public $projectRepository;
    public $attachmentRepository;

    public function __construct(ProjectRepository $projectRepository, AttachmentRepository $attachmentRepository)
    {
        $this->middleware(['permission:list_project'])->only('index', 'getNameAndIds');
        $this->middleware(['permission:create_project'])->only(['store']);
        $this->middleware(['permission:update_project'])->only(['update']);
        $this->middleware(['permission:detail_project'])->only('show');
        $this->middleware(['permission:destroy_project'])->only('destroy');
        $this->projectRepository = $projectRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $this->projectRepository->filter($request->all());
        // dd($projects);
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
        $data = $request->all();
        try {
            DB::beginTransaction();
            $project = $this->projectRepository->create($data);
            $this->projectRepository->syncProcurement($data, $project->id);
            $this->projectRepository->syncIndustry($data, $project->id);
//            dd(auth()->user);
            $this->attachmentRepository->createAttachment($data['files'], $project->id, auth()->user()->id);
            if (isset($data['children'])) {
                foreach ($data['children'] as $child) {
                    $child['staff_id'] = $project->staff_id;
                    $child['decision_number_issued'] = $project->decision_number_issued;
                    $child['total_amount'] = $project->total_amount;
                    $child['is_domestic'] = $project->is_domestic;
                    $newChild = $project->children()->create($child);
                    $this->projectRepository->syncIndustry($child, $newChild->id);
                }
            }
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
            $this->attachmentRepository->createAttachment($data['files'], $project->id, auth()->user()->id);

            $newChildIds = [];

            if (isset($data['children'])) {
                foreach ($data['children'] as $child) {
                    if (isset($child['id']) && $child['id']) {
                        $project->children()->updateOrCreate(
                            ['id' => $child['id']],
                            array_merge($child, [
                                'staff_id' => $project->staff_id,
                                'decision_number_issued' => $project->decision_number_issued,
                                'total_amount' => $project->total_amount,
                                'is_domestic' => $project->is_domestic,
                            ])
                        );
                        $newChildIds[] = $child['id'];
                    } else {
                        $newChild = $project->children()->create(array_merge($child, [
                            'staff_id' => $project->staff_id,
                            'decision_number_issued' => $project->decision_number_issued,
                            'total_amount' => $project->total_amount,
                            'is_domestic' => $project->is_domestic,
                        ]));
                        $newChildIds[] = $newChild->id;
                    }
                    $this->projectRepository->syncIndustry($child, $child['id']);
                }
            }

            $project->children()->whereNotIn('id', $newChildIds)->delete();

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
        $projects = $this->projectRepository->getNameAndIds();
        return response([
            'result' => true,
            'message' => "Lấy danh sách dự án và gói thầu thành công",
            'data' => $projects
        ], 200);
    }
}
