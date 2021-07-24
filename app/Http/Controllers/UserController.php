<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function  allCourses(Request $request)
    {
        $user = $request->user();
        $userDB = DB::table('users')->where(["email" => $user->email])->first();
        $userCourses = $userDB->courses;
        $coursesArr = explode(" ", $userCourses);
        $courses = DB::table('courses')
            ->whereIn("slug", $coursesArr)
            ->get();
        return response()->json(["courses" => $courses]);
    }
    public function  singleCourse(Request $request, $slug)
    {
        $user = $request->user();
        $userDB = DB::table('users')->where(["email" => $user->email])->first();
        $userCourses = $userDB->courses;
        $coursesArr = explode(" ", $userCourses);
        if (in_array($slug, $coursesArr)) {
            $courses = DB::table('courses')
                ->where([
                    "slug" => $slug,
                ])
                ->first();
            if (count((array)$courses)) {
                $lessons = DB::table('lessons')
                    ->where([
                        "course_id" => $courses->id,
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
        } else {
            return response()->json(["message" => "course not found"], 404);
        }
    }
}
