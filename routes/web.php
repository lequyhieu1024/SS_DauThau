<?php

use App\Http\Controllers\Api\Admin\AttachmentController;
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

Route::get('documents/{filename}', [AttachmentController::class, 'serveDocumentFile']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
