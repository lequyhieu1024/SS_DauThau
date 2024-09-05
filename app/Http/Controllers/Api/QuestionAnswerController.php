<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionAnswerCollection;
use App\Repositories\QuestionAnswerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionAnswerController extends Controller
{
    protected $questionAnswerRepository;
    public function __construct(QuestionAnswerRepository $questionAnswerRepository)
    {
        $this->questionAnswerRepository = $questionAnswerRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $questionAnswers = $this->questionAnswerRepository->filter($request->all());
        $data = new QuestionAnswerCollection($questionAnswers);
        return response()->json([
            'result' => true,
            'message' => 'Danh sách câu hỏi và câu trả lời.',
            'data' => $data
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
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $questionAnswer = $this->questionAnswerRepository->deleteQuestionAnswer($id);

            if (!$questionAnswer) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Câu hỏi và câu trả lời không tồn tại.',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Xóa câu hỏi và câu trả lời thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Lỗi khi xóa câu hỏi và câu trả lời.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
