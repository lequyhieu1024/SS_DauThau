<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerFormRequest;
use App\Http\Resources\BannerCollection;
use App\Repositories\BannerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public $bannerRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->middleware(['permission:list_banner'])->only(['index']);
        $this->middleware(['permission:create_banner'])->only('store');
        $this->middleware(['permission:update_banner'])->only(['update', 'toggleActiveStatus']);
        $this->middleware(['permission:detail_banner'])->only('show');
        $this->middleware(['permission:destroy_banner'])->only('destroy');

        $this->bannerRepository = $bannerRepository;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/banners",
     *     tags={"Banners"},
     *     summary="Get all Banners",
     *     description="Get all Banners",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="size",
     *         in="query",
     *         description="Size items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Name of banner",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Get Banners successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Get Banners successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banners",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="Banners 1"
     *                         ),
     *                         @OA\Property(
     *                             property="path",
     *                             type="string",
     *                             example="banner.png"
     *                         ),
     *                         @OA\Property(
     *                             property="is_active",
     *                             type="boolean",
     *                             example=true
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="2021-09-01T00:00:00.000000Z"
     *                         ),
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(
     *                         property="currentPage",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="pageSize",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="totalItems",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="totalPages",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="hasNextPage",
     *                         type="boolean",
     *                         example=false
     *                     ),
     *                     @OA\Property(
     *                         property="hasPreviousPage",
     *                         type="boolean",
     *                         example=false
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $banners = $this->bannerRepository->filter($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách banner thành công.',
            'data' => new BannerCollection($banners)
        ], 200);
    }

    public function getBannersLandipage()
    {
        $banners = $this->bannerRepository->getBannersLangipage();

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách banner thành công.',
            'data' => $banners
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/banners",
     *     tags={"Banners"},
     *     summary="Create a new banner",
     *     description="Create a new banner",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "is_active"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="banner 1"
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     format="binary",
     *                     nullable=true,
     *                     description="The file to upload",
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="integer",
     *                     example=1
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="banner created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="banner created successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="banner 1"
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     example="banner.png"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function store(BannerFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            if ($request->hasFile('path')) {
                $data['path'] = upload_image($request->file('path'));
            }
            $banner = $this->bannerRepository->create($data);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo banner thành công.",
                "data" => $banner
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Tạo banner không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/banners/{id}",
     *     tags={"Banners"},
     *     summary="Get banner by ID",
     *     description="Get banner by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of banner",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="banner retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="banner retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="banner 1"
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     example="banner.png"
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2021-09-01T00:00:00.000000Z"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $banner = $this->bannerRepository->find($id);

        if (!$banner) {
            return response()->json([
                'result' => false,
                'message' => 'Banner không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin banner thành công.',
            'data' => $banner
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/admin/banners/{id}",
     *     tags={"Banners"},
     *     summary="Update banner by ID",
     *     description="Update a banner by its ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the banner",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "is_active", "_method"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Banner 1",
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     example="PATCH",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banner updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="result",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Banner updated successfully"
     *             )
     *         )
     *     ),
     * )
     */

    public function update(BannerFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            if ($request->hasFile('path')) {
                $data['path'] = upload_image($request->file('path'));
                isset($this->bannerRepository->findOrFail($id)->path) && file_exists($this->bannerRepository->findOrFail($id)->path) ? unlink($this->bannerRepository->findOrFail($id)->path) : "";
            } else {
                $data['path'] = $this->bannerRepository->findOrFail($id)->path;
            }
            $banner = $this->bannerRepository->update($data, $id);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật banner thành công",
                "data" => $banner
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Cập nhật banner không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/banners/{id}",
     *     tags={"Banners"},
     *     summary="Delete banner by ID",
     *     description="Delete banner by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of banner",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="banner deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (count($this->bannerRepository->filter([])) < 2) {
                return response()->json([
                    'result' => true,
                    'message' => 'Phải để lại ít nhất 1 banner.',
                ], 200);
            }
            $banner = $this->bannerRepository->deleteBanner($id);
            isset($banner->path) && file_exists($banner->path) ? unlink($banner->path) : "";

            if (!$banner) {
                return response()->json([
                    'result' => false,
                    'message' => 'Banner không tồn tại',
                ], 404);
            }
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa banner thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa banner.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/banners/{id}/toggle-status",
     *     tags={"Banners"},
     *     summary="Toggle active status of banner by ID",
     *     description="Toggle active status of banner by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of banner",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="banner status toggled successfully"
     *     )
     * )
     */
    public function toggleActiveStatus($id)
    {
        $banner = $this->bannerRepository->findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();
        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Thay đổi trạng thái thành công',
            'is_active' => $banner->is_active
        ], 200);
    }
}
