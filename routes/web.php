<?php

use App\Http\Controllers\DocsController;
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

Route::get('/docs', [DocsController::class, 'show']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
