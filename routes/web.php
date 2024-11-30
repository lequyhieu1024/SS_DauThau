<?php

use App\Http\Controllers\Api\Admin\AttachmentController;
use App\Models\Enterprise;
use App\Models\Industry;
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

//Route::get('/assignee-role', function () {
//    $industries = Industry::all(); // Lấy danh sách tất cả các ngành nghề
//
//    // Lấy doanh nghiệp theo nhóm 100 bản ghi
//    Enterprise::where('id', '>=', 3241)->chunk(100, function ($enterprises) use ($industries) {
//        foreach ($enterprises as $enterprise) {
//            // Lấy ngẫu nhiên từ 1 đến 5 ngành nghề
//            $randomIndustries = $industries->random(rand(1, 5))->pluck('id')->toArray();
//
//            // Gán ngành nghề cho doanh nghiệp
//            $enterprise->industries()->sync($randomIndustries);
//        }
//    });
//});

