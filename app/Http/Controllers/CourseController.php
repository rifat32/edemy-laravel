<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CourseController extends Controller
{
    public function uploadImage(Request $request)
    {


        $img = $request->file('image');
        if ($img !== null) {
            $fileName = time() . '.' . $request->image->extension();
            $success =   Storage::disk('google')->put($fileName, file_get_contents($img->getRealPath()), "public");
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
}
