<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
//    public function register(): void
//    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
//    }

    public function register()
    {
        $this->renderable(function (PostTooLargeException $e, $request) {
            return response()->json([
                'result' => false,
                'message' => 'Dung lượng file tải lên vượt quá giới hạn cho phép.',
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        });
    }
}
