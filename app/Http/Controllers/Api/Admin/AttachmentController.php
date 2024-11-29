<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachmentFormRequest;
use App\Http\Resources\AttachmentCollection;
use App\Http\Resources\AttachmentResource;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

//use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    protected $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->middleware(['permission:list_attachment'])->only('index');
        $this->middleware(['permission:create_attachment'])->only('store');
        $this->attachmentRepository = $attachmentRepository;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response([
            'result' => 'true',
            'message' => 'Lấy danh sách liệu đính kèm thành công',
            'attachments' => new AttachmentCollection($this->attachmentRepository->filter($request->all()))
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttachmentFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $files = $request->file('files');
            $attachments = [];

            foreach ($files as $file) {
                $newFilename = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $relativePath = "uploads/documents/{$newFilename}";

                $file->move(public_path('uploads/documents'), $newFilename);

                if (!file_exists(public_path('uploads/documents/' . $newFilename))) {
                    throw new \Exception('Không tìm thấy tập tin sau khi di chuyển.');
                }

                $data = [
                    'user_id' => auth()->id(),
                    'project_id' => $request->input('project_id'),
                    'name' => $newFilename,
                    'path' => $relativePath,
                    'type' => $file->getClientOriginalExtension(),
                    'size' => filesize(public_path('uploads/documents/' . $newFilename)),
                    'is_active' => true,
                ];

                $attachments[] = new AttachmentResource($this->attachmentRepository->create($data));
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Tài liệu đính kèm đã được tải lên thành công',
                'data' => $attachments,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tải lên tài liệu đính kèm.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function serveDocumentFile($filename)
    {
        $path = public_path().'/uploads/documents/'.$filename;

        if (!File::exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        $response->header("Content-Disposition", 'inline; filename="'.basename($path).'"');

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
