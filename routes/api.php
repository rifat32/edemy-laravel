<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientCourseController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;

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

        Route::post('/course/upload-video', [LessonController::class, 'uploadVideo']);
        Route::post('/course/remove-video', [LessonController::class, 'removeVideo']);
        Route::post('/course/lesson', [LessonController::class, 'createLesson']);
        Route::put('/course/lesson', [LessonController::class, 'updateLesson']);
        Route::post('/number-of-lesson', [CourseController::class, 'numLesson']);
        Route::put('/course', [CourseController::class, 'updateCourse']);
        Route::post('/lesson/delete ', [LessonController::class, 'deleteLesson']);
        Route::put('/course/publish', [CourseController::class, 'updatePublish']);
        Route::get('/courses/{slug}', [CourseController::class, 'singleCourse']);
    });
    Route::get('/check-enrollment/{slug}', [CourseController::class, 'checkEnrollment']);
    Route::post('/free-enrollment/{slug}', [CourseController::class, 'freeEnrollment']);
});
Route::post('/send-token', [AuthController::class, 'sendToken']);
Route::post('/verify-token', [AuthController::class, 'verifyToken']);
Route::get('all-courses', [ClientCourseController::class, 'allCourses']);
Route::get('/client-courses/{slug}', [ClientCourseController::class, 'singleCourse']);
