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
    // instructor middleware
    Route::middleware(["instructor"])->group(function () {
        Route::post('/course/upload-image', [CourseController::class, 'uploadImage']);
        Route::post('/course/remove-image', [CourseController::class, 'deleteImage']);
        Route::post('/course', [CourseController::class, 'createCourse']);
        Route::get('/instructor-courses', [CourseController::class, 'allCourses']);
    });
});
Route::post('/send-token', [AuthController::class, 'sendToken']);
Route::post('/verify-token', [AuthController::class, 'verifyToken']);
