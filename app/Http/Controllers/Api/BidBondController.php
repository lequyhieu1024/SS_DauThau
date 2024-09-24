<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BidBondFormRequest;
use App\Http\Resources\BidBondCollection;
use App\Http\Resources\BidBondResource;
use App\Repositories\BidBondRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rmunate\Utilities\SpellNumber;

class BidBondController extends Controller
{
    protected $bidBondRepository;
    protected $projectRepository;
    public function __construct(BidBondRepository $bidBondRepository, ProjectRepository $projectRepository)
    {
        $this->bidBondRepository = $bidBondRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bidBonds = $this->bidBondRepository->filter($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Lấy danh sách bảo lãnh dự thầu thành công.',
            'data' =>  new BidBondCollection($bidBonds)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BidBondFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $issueDate = $this->projectRepository->find($request->project_id)->submission_deadline;
            $expiryValidation = $this->validateExpiryDate($request->expiry_date, $issueDate);
            if (!$expiryValidation['result']) {
                return response()->json($expiryValidation, 422);
            }

            $data['bond_amount_in_words'] = SpellNumber::value($request->bond_amount)->toLetters();
            $data['issue_date'] = $issueDate;
            $bidBond = $this->bidBondRepository->create($data);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Tạo bảo lãnh dự thầu thành công.",
                "data" => $bidBond
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Tạo bảo lãnh dự thầu không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bidBond = $this->bidBondRepository->find($id);

        if (!$bidBond) {
            return response()->json([
                'result' => false,
                'message' => 'Bảo lãnh dự thầu không tồn tại',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin bảo lãnh dự thầu thành công.',
            'data' => new BidBondResource($bidBond)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BidBondFormRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $issueDate = $this->projectRepository->find($request->project_id)->submission_deadline;
            $expiryValidation = $this->validateExpiryDate($request->expiry_date, $issueDate);
            if (!$expiryValidation['result']) {
                return response()->json($expiryValidation, 422);
            }

            $data['bond_amount_in_words'] = SpellNumber::value($request->bond_amount)->toLetters();
            $data['issue_date'] = $issueDate;
            $bidBond = $this->bidBondRepository->update($data, $id);

            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Cập nhật bảo lãnh dự thầu thành công",
                "data" => $bidBond
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Cập nhật bảo lãnh dự thầu không thành công.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->bidBondRepository->delete($id);

            return response()->json([
                'result' => true,
                'message' => 'Xóa bảo lãnh dự thầu thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa bảo lãnh dự thầu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function validateExpiryDate($expiryDate, $submissionDeadline)
    {
        if (strtotime($expiryDate) <= strtotime($submissionDeadline)) {
            return [
                'result' => false,
                'message' => "Ngày hết hạn phải lớn hơn ngày phát hành."
            ];
        }
        return ['result' => true];
    }
}
