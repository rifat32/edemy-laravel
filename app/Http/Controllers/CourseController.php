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
            $mediaQuery->delete();
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
                "slug" => $slug
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
            $user = $request->user();
            DB::table('courses')
                ->insert([
                    "name" => $name,
                    "slug" => $slug,
                    "description" => $description,
                    "price" => $price,
                    "image" => $image,
                    "category" => $category,
                    "paid" => $paid,
                    "instructor_id" =>  $user->id,
                    "instructor_name" => $user->name

                ]);
            return response()->json([
                "message" => "course has been created successfully"
            ], 200);
        }
    }
    public function updateCourse(Request $request)
    {
        $slug = $request->slug;
        $user_id = $request->user()->id;
        $courseQuery = DB::table('courses')
            ->where([
                "instructor_id" => $user_id,
                "slug" => $slug
            ]);
        if ($courseQuery->exists()) {
            $name = $request->name;
            $slugNew = strtolower($name);
            $slugNew = str_replace(" ", "-", $slugNew);
            //  if slug matches it
            if ($slug == $slugNew) {
                $category = $request->category;
                $description = $request->description;
                $image = $request->image;
                $paid = $request->paid;
                if ($paid) {
                    $price = $request->price;
                } else {
                    $price = 0.0;
                }

                $courseQuery
                    ->update([
                        "description" => $description,
                        "price" => $price,
                        "image" => $image,
                        "category" => $category,
                        "paid" => $paid
                    ]);
                return response()->json(["message" => "course has been updated successfully"], 204);
            }
            // else check first slug exists or not
            else {
                $slug_exists =  DB::table('courses')
                    ->where([
                        "slug" => $slugNew
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
                    $courseQuery
                        ->update([
                            "name" => $name,
                            "slug" => $slugNew,
                            "description" => $description,
                            "price" => $price,
                            "image" => $image,
                            "category" => $category,
                            "paid" => $paid
                        ]);
                    return response()->json([
                        "message" => "course has been updated successfully"
                    ], 204);
                }
            }
        } else {
            return response()->json([
                "message" => "no course found"
            ], 404);
        }

        return "update courrt se";
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
        $courses = DB::table('courses')
            ->where([
                "slug" => $slug
            ])
            ->first();
        if (count((array)$courses)) {
            $lessons = DB::table('lessons')
                ->where([
                    "course_id" => $courses->id
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
    public function numLesson(Request $request)
    {
        $user_id = $request->user()->id;
        $courseId = $request->id;
        $lessons =   DB::table('lessons')
            ->where([
                "instructor_id" => $user_id,
                "course_id" => $courseId
            ])
            ->get();
        $number_of_lesson = count($lessons);
        DB::table('courses')
            ->where([
                "instructor_id" => $user_id,
                "id" => $courseId
            ])
            ->update([
                "number_of_lessons" =>
                $number_of_lesson
            ]);
        return response()->json([
            "ok" => true
        ]);
    }
    public function updatePublish(Request $request)
    {
        $courseId = $request->id;
        $user_id = $request->user()->id;
        $published = $request->published;
        DB::table('courses')
            ->where([
                "id" => $courseId,
                "instructor_id" => $user_id
            ])
            ->update([
                "published" => $published
            ]);
        return response()->json([
            "ok" => true
        ]);
    }
}
