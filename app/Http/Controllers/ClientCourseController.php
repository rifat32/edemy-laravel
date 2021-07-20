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
            $lessons = DB::table('lessons')
                ->where([
                    "course_id" => $courses->id,
                    "paid" => false
                ])
                ->orderBy("custom_id")
                ->get();
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
