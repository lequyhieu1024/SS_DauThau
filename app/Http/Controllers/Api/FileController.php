<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Http\Resources\File\FileResource;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;


class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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


    public function store(Request $request)
    {
        $userId = Auth::id();
        $uploadedFiles = [];

        DB::beginTransaction();

        try {

            $files = $request->file('files');

            // Ensure $files is an array
            if (!is_array($files)) {
                $files = [$files];
            }

            // Process all files
            foreach ($files as $file) {
                $type = $this->determineFileType($file);

                $fileModel = $this->fileService->upload($file, $type, $userId);
                $uploadedFiles[] = new FileResource($fileModel);
            }

            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Tải lên thành công',
                'data' => $uploadedFiles,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi tải lên tệp',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function determineFileType($file): string
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'document';
        }
    }

    /**
     * Display the specified resource.
     */// app/Http/Controllers/Api/FileController.php

    public function show($id)
    {
    }

    public function serveImageFile($filename)
    {
        $path = public_path().'/uploads/images/'.$filename;

        if (!File::exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function serveVideoFile($path)
    {
        $pathFile = public_path().'/uploads/videos/'.$path;

        if (!File::exists($pathFile)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $file = File::get($pathFile);
        $type = File::mimeType($pathFile);

        $response = Response::make($file, 200);
        $response->header("Content-Type", "video/mp4");
        $response->header("Content-Disposition", 'inline; filename="'.basename($pathFile).'"');
        $response->header("Accept-Ranges", "bytes");

        return $response;
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
