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
}
