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
            if ($courses->paid) {
                $lessonsFree = DB::table('lessons')
                    ->where([
                        "course_id" => $courses->id,
                        "free_preview" => true
                    ])
                    ->orderBy("custom_id")
                    ->get();
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
                    ->orderBy("custom_id")
                    ->get();
                $lessons = array_merge($lessonsFree, $lessonsPaid);
            } else {
                $lessons = DB::table('lessons')
                    ->where([
                        "course_id" => $courses->id,
                    ])
                    ->orderBy("custom_id")
                    ->get();
            }

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
