<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionAnswerRequest;
use App\Http\Resources\QuestionAnswerCollection;
use App\Http\Resources\QuestionAnswerResource;
use App\Http\Resources\ReputationCollection;
use App\Repositories\QuestionAnswerRepository;
use Illuminate\Http\Request;

class QuestionAnswerController extends Controller
{
    protected $questionAnswerRepository;
    public function __construct(QuestionAnswerRepository $questionAnswerRepository){
        // $this->middleware(['permission:list_question_answer'])->only('index');
        // $this->middleware(['permission:create_question_answer'])->only(['store']);
        // $this->middleware(['permission:update_question_answer'])->only(['update', 'changeActive']);
        // $this->middleware(['permission:detail_question_answer'])->only('edit');
        // $this->middleware(['permission:destroy_question_answer'])->only('destroy');

        $this->questionAnswerRepository = $questionAnswerRepository;
    }

    public function index(Request $request){
        $questionsAnswers = $this->questionAnswerRepository->filter($request->all());
        if(empty($questionsAnswers)){
            return response([
                'result' => true,
                'message' => "Chưa có câu hỏi/câu trả lời nào!",
                'data' => []
            ], 200);
        }
        return response([
            'result' => true,
            'message' => "Lấy câu hỏi/ câu trả lời thành công!",
            'data' => new QuestionAnswerCollection($questionsAnswers)
        ], 200);
    }

    public function store(QuestionAnswerRequest $req){
        try {
            $data = $this->questionAnswerRepository->create($req->all());
            return response([
                'result' => true,
                'message' => "Tạo mới câu hỏi/ câu trả lời thành công!",
                'data' => $data
            ], 201);
        } catch (\Throwable $qs){
            return response([
                'result' => false,
                'message' => "Có lỗi từ server!",
            ], 500);
        }
    }

    public function show(string $id)
    {
        $questionAnswer = $this->questionAnswerRepository->find($id);
        $data = new QuestionAnswerResource($questionAnswer);

        if (!$questionAnswer) {
            return response()->json([
                'result' => false,
                'message' => "Câu hỏi/ câu trả lời không tồn tại",
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Lấy thông tin câu hỏi/ câu trả lời thành công.',
            'data' => $data
        ], 200);
    }

    public function edit(string $id)
    {
        $questionAnswer = $this->questionAnswerRepository->findOrFail($id);
        if (empty($questionAnswer)) {
            return response([
                'result' => true,
                'message' => "Câu hỏi/ câu trả lời không tồn tại",
                'data' => []
            ], status: 404);
        }
        return response([
            'result' => true,
            'message' => "Lấy danh sách câu hỏi/ câu trả lời thành công",
            'data' => new QuestionAnswerResource(resource: $questionAnswer)
        ], 200);
    }

    public function update(QuestionAnswerRequest $request, string $id)
    {
        try {
            $questionAnswer = $this->questionAnswerRepository->find($id);
            if (!$questionAnswer) {
                return response()->json([
                    'result' => false,
                    'message' => "Câu hỏi/câu trả lời không tồn tại",
                ], 404);
            }

            $this->questionAnswerRepository->update($request->all(), $id);
            return response([
                'result' => true,
                'message' => 'Cập nhật câu hỏi/câu trả lời thành công',
                'data' => new QuestionAnswerResource($this->questionAnswerRepository->findOrFail($id))
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $questionAnswer = $this->questionAnswerRepository->find($id);
            if (!$questionAnswer) {
                return response()->json([
                    'result' => false,
                    'message' => "Câu hỏi/câu trả lời không tồn tại",
                ], 404);
            }


            $this->questionAnswerRepository->delete($id);
            return response()->json([
                'result' => true,
                'message' => "Xóa câu hỏi/câu trả lời thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'result' => false,
                'message' => "Có lỗi từ server",
            ], 500);
        }
    }


}
