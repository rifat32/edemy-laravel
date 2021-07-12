<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function uploadImage(Request $request)
    {


        $img = $request->file('image');
        if ($img !== null) {
            $fileName = time() . '.' . $request->image->extension();
            Storage::disk('google')->put($fileName, file_get_contents($img->getRealPath()), "public");
            $image = Storage::disk('google')->url($fileName);

            return response()->json([
                "image" => $image
            ]);
        }
        return response()->json([
            "image" => "no image"
        ]);
    }
    public function deleteImage(Request $request)
    {
        $imageId = $request->imageId;
        $success =   Storage::disk('google')->delete($imageId);
        return response()->json(["ok" => $success]);
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
}
