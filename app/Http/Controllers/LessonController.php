<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function uploadVideo(Request $request)
    {
        return response()->json(['messege' => "initial"]);
    }
}
