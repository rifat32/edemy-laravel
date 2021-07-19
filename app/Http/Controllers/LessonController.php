<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function uploadVideo(Request $request)
    {
        $user = $request->user();
        $video = $request->file('video');
        if ($video !== null) {
            $fileName = time() . '.' . $request->video->extension();
            Storage::disk('google')->put($fileName, file_get_contents($video->getRealPath()), "public");
            $vid = Storage::disk('google')->url($fileName);
            parse_str(parse_url($vid, PHP_URL_QUERY), $array);
            $videoId =  $array["id"];
            DB::table('media')
                ->insert([
                    "link" => $videoId,
                    "instructor" => $user->email
                ]);
            return response()->json([
                "video" => $vid,
                "videoId" => $videoId
            ]);
        } else {
            return response()->json([
                "video" => "no video"
            ]);
        }

        return response()->json(['messege' => "initial"]);
    }

    public function removeVideo(Request $request)
    {
        $videoId = $request->videoId;
        $user = $request->user();
        $mediaQuery =  DB::table('media')
            ->where([
                "link" => $videoId,
                "instructor" => $user->email
            ]);
        if ($mediaQuery->exists()) {
            $success =   Storage::disk('google')->delete($videoId);
            $mediaQuery->delete();
            return response()->json(["ok" => $success]);
        } else {
            return response()->json(["message" => "do not cheat"], 401);
        }
    }
    public function createLesson(Request $request)
    {
        $courseId = (int) $request->id;
        $user = $request->user();
        $title = $request->title;
        $lessonSlug = strtolower($title);
        $lessonSlug = str_replace(" ", "-", $lessonSlug);
        $content = $request->content;
        $video = $request->video;
        parse_str(parse_url($video, PHP_URL_QUERY), $array);
        $videoId =  $array["id"];
        $courseQuery =  DB::table('courses')
            ->where([
                "id" => $courseId,
                "instructor_id" => $user->id
            ]);
        $mediaQuery =  DB::table('media')
            ->where([
                "link" => $videoId,
                "instructor" => $user->email
            ]);
        if ($courseQuery->exists()) {
            if ($mediaQuery->exists()) {
                $lessonQuery = DB::table('lessons');
                $lessonId =  $lessonQuery
                    ->insertGetId([
                        "title" => $title,
                        "slug" => $lessonSlug,
                        "content" => $content,
                        "video" => $video,
                        "course_id" => $courseId,
                        "instructor_id" => $user->id,
                        "instructor_name" => $user->name
                    ]);
                $lessonQuery
                    ->where([
                        'id' => $lessonId
                    ])
                    ->update([
                        "custom_id" => $lessonId
                    ]);
                return response()->json(["lesson" => "inserted"], 200);
            } else {
                return response()->json(["message" => "do not cheat"], 401);
            }
        } else {
            return response()->json(["message" => "do not cheat"], 401);
        }
    }
    public function updateLesson(Request $request)
    {
        $lessonId = $request->id;
        $courseId = $request->course_id;
        $user = $request->user();
        $lessonQuery = DB::table('lessons')
            ->where([
                'id' => $lessonId,
                "instructor_id" => $user->id,
                "course_id" => $courseId,
            ]);
        $lesson =   $lessonQuery->first();
        if (count((array)$lesson)) {
            $video = $request->video;
            parse_str(parse_url($video, PHP_URL_QUERY), $arrayNew);
            $videoIdNew =  $arrayNew["id"];
            $mediaQuery =  DB::table('media')
                ->where([
                    "link" => $videoIdNew,
                    "instructor" => $user->email
                ]);
            if ($mediaQuery->exists()) {
                $content = $request->content;
                $title = $request->title;
                $slug = strtolower($title);
                $slug = str_replace(" ", "-", $slug);
                $free_preview = $request->free_preview;
                parse_str(parse_url($lesson->video, PHP_URL_QUERY), $arrayMain);
                $videoIdMain =  $arrayMain["id"];
                if ($videoIdMain !== $videoIdNew) {

                    DB::table('media')
                        ->where([
                            "link" => $videoIdMain,
                        ])
                        ->delete();
                    Storage::disk('google')->delete($videoIdMain);
                }
                $lessonQuery
                    ->update([
                        "title" => $title,
                        "slug" => $slug,
                        "content" => $content,
                        "video" => $video,
                        "free_preview" => $free_preview
                    ]);
                return response()->json(["message" => " lesson updated"], 200);
            } else {
                return response()->json(["message" => "do not cheat"], 401);
            }
        } else {
            return response()->json(["message" => "do not cheat"], 401);
        }
    }
    public function deleteLesson(Request $request)
    {


        $lessonId = $request->id;
        $user = $request->user();
        $lessonQuery = DB::table('lessons')
            ->where([
                'id' => $lessonId,
                "instructor_id" => $user->id
            ]);
        $lesson =   $lessonQuery->get();

        if (count($lesson)) {
            parse_str(parse_url($lesson[0]->video, PHP_URL_QUERY), $array);
            $videoId =  $array["id"];
            DB::table('media')
                ->where([
                    "link" => $videoId,
                ])
                ->delete();
            $courseQuery =   DB::table('courses')
                ->where([
                    "id" => $lesson[0]->course_id
                ]);
            $course =   $courseQuery->first();
            $courseQuery
                ->update([
                    "number_of_lessons" => $course->number_of_lessons - 1
                ]);
            $lessonQuery->delete();

            $success =   Storage::disk('google')->delete($videoId);
            return response()->json(["ok" => $success]);
        } else {
            return response()->json(["message" => "do not cheat"], 401);
        }
    }
}
