<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function uploadImage(Request $request)
    {

        $user = $request->user();
        $img = $request->file('image');
        if ($img !== null) {
            $fileName = time() . '.' . $request->image->extension();
            Storage::disk('google')->put($fileName, file_get_contents($img->getRealPath()), "public");
            $image = Storage::disk('google')->url($fileName);
            parse_str(parse_url($image, PHP_URL_QUERY), $array);
            $imageId =  $array["id"];
            DB::table('media')
                ->insert([
                    "link" => $imageId,
                    "instructor" => $user->email
                ]);

            return response()->json([
                "image" => $image,
                "imageId" => $imageId
            ]);
        }
        return response()->json([
            "image" => "no image"
        ]);
    }
    public function deleteImage(Request $request)
    {
        $imageId = $request->imageId;
        $user = $request->user();
        $mediaQuery =  DB::table('media')
            ->where([
                "link" => $imageId,
                "instructor" => $user->email
            ]);
        if ($mediaQuery->exists()) {
            $success =   Storage::disk('google')->delete($imageId);
            return response()->json(["ok" => $success]);
        } else {

            return response()->json(["message" => "do not cheat"], 401);
        }
    }
    public function createCourse(Request $request)
    {
        $name = $request->name;
        $slug = strtolower($name);
        $slug = str_replace(" ", "-", $slug);
        $slug_exists =  DB::table('courses')
            ->where([
                "slug" => "$slug"
            ])
            ->exists();
        if ($slug_exists) {
            return response()->json([
                "message" => "This name is already taken"
            ], 409);
        } else {
            $category = $request->category;
            $description = $request->description;
            $image = $request->image;
            $paid = $request->paid;
            if ($paid) {
                $price = $request->price;
            } else {
                $price = 0.0;
            }
            $user_id = $request->user()->id;
            DB::table('courses')
                ->insert([
                    "name" => $name,
                    "slug" => $slug,
                    "description" => $description,
                    "price" => $price,
                    "image" => $image,
                    "category" => $category,
                    "paid" => $paid,
                    "instructor_id" =>  $user_id,
                ]);
            return response()->json([
                "message" => "course has been created successfully"
            ], 200);
        }
    }
    public function allCourses(Request $request)
    {

        $user_id = $request->user()->id;
        // DB::table('lessons')->insert([
        //     "name" => 'dsgsdgs',
        //     "slug" => "fdgfg",
        //     "content" => "contentdfgfrh",
        //     "video" => "videofhfh",
        //     "free_preview" => true,
        //     "course_slug" => "aaa",
        //     "instructor_id" => $user_id
        // ]);
        // return "hey";
        $courses = DB::table('courses')
            ->where([
                "instructor_id" => $user_id
            ])
            ->get();


        return response()->json([
            "courses" => $courses
        ], 200);
    }
    public function singleCourse(Request $request, $slug)
    {
        $user_id = $request->user()->id;
        $courses = DB::table('courses')
            ->where([
                "instructor_id" => $user_id,
                "slug" => $slug
            ])
            ->first();
        return response()->json([
            "courses" => $courses
        ]);
    }
}
