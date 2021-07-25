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
            ->where([
                "published" => true
            ])
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
                    "published" => true
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
    public function completeLesson(Request $request)
    {
        // complete__lessons
        $user = $request->user();
        $course_slug = $request->course_slug;
        $lesson_id = $request->lesson_id;

        $complete =  DB::table('complete__lessons')
            ->where([
                "course_slug" => $course_slug,
                "lesson_id" => $lesson_id,
                "user_id" => $user->id,
            ])
            ->first();
        if (count((array)$complete)) {
            return response()->json(["ok" => true]);
        } else {
            DB::table('complete__lessons')->insert([
                "course_slug" => $course_slug,
                "lesson_id" => $lesson_id,
                "user_id" => $user->id,
            ]);
            return response()->json(["ok" => true]);
        }
    }
    public function incompleteLesson(Request $request)
    {
        // complete__lessons
        $user = $request->user();
        $course_slug = $request->course_slug;
        $lesson_id = $request->lesson_id;
        DB::table('complete__lessons')
            ->where([
                "course_slug" => $course_slug,
                "lesson_id" => $lesson_id,
                "user_id" => $user->id,
            ])
            ->delete();
        return response()->json(["ok" => true]);
    }
    public function listCompleted(Request $request)
    {
        $user = $request->user();
        $course_slug = $request->course_slug;
        $lessons =  DB::table('complete__lessons')
            ->where([
                "course_slug" => $course_slug,
                "user_id" => $user->id,
            ])
            ->select("lesson_id")
            ->get();
        return response()->json(["lessons" => $lessons]);
    }
}
