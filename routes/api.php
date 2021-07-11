<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [AuthController::class, 'getCurrentUser']);
    Route::post('/make-instructor', [InstructorController::class, 'makeInstructor']);
    Route::get('/current-instructor', [InstructorController::class, 'currentInstructor']);
    Route::post('/course/upload-image', [CourseController::class, 'uploadImage']);
    Route::post('/course/remove-image', [CourseController::class, 'deleteImage']);
});
