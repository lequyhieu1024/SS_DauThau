<?php

use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('files/images/{filename}', [FileController::class, 'serveImageFile']);
Route::get('files/videos/{filename}', [FileController::class, 'serveVideoFile']);
Route::get('files/documents/{filename}', [FileController::class, 'serveDocumentFile']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
