<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    $meta = Storage::disk('google')->allFiles();
    //$meta = Storage::disk('google')->delete("1-aA8OJQk3l_3GruoLZFh_z8KDIw0M07K");
    dd($meta);
    dd("done");
});
Route::post(
    '/course/upload-image',
    [CourseController::class, 'uploadImage']
);
