<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientCourseController extends Controller
{
    public function allCourses(Request $request)
    {
        $courses =  DB::table('courses')
            ->where([
                "published" => true
            ])
            ->orderByDesc("total_enrollment")
            ->get();
        return response()->json([
            "courses" => $courses
        ], 200);
    }
    public function singleCourse(Request $request, $slug)
    {
        $courses = DB::table('courses')
            ->where([
                "slug" => $slug,
                "published" => true
            ])
            ->first();
        if (count((array)$courses)) {

            $lessonsFree = DB::table('lessons')
                ->where([
                    "course_id" => $courses->id,
                    "free_preview" => true
                ])
                ->get()->toArray();
            $lessonsPaid =  DB::table('lessons')
                ->where([
                    "course_id" => $courses->id,
                    "free_preview" => false
                ])
                ->select(
                    "id",
                    'title',
                    "slug",
                    "content",
                    "free_preview",
                    "course_id",
                    "instructor_id",
                    "instructor_name",
                    "custom_id",
                    "created_at",
                    "updated_at"
                )
                ->get()->toArray();
            $lessons = array_merge($lessonsFree, $lessonsPaid);


            return response()->json([
                "courses" => $courses,
                "lessons" => $lessons
            ]);
        } else {
            return response()->json([
                "message" => "no course found"
            ], 404);
        }
    }
}
