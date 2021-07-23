<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function allPayment()
    {
        $payments =   DB::table('payments')
            ->where([
                "status" => "pending"
            ])
            ->get();
        return response()->json([
            "payments" => $payments
        ], 200);
    }
    public function confirmPayment(Request $request)
    {
        return response()->json(["confirmed" => true]);
        //  it will update by payment id
        $id = $request->id;
        $user_id = $request->user_id;
        $course_slug = $request->course_slug;
        $price = $request->price;
        $paypentQuery =   DB::table('payments')
            ->where([
                "id" => $id
            ]);
        $paypentQuery->update([
            "status" => "success"
        ]);
        // it will update users courses array,
        $userQuery =  DB::table('users')
            ->where([
                "id" => $user_id,
            ]);
        $userDB = $userQuery->first();
        $courses = $userDB->courses;
        $userQuery
            ->update([
                "courses" => $courses . " " . $course_slug
            ]);
        // it will update instructors balance,
        $coursesQuery =  DB::table('courses')
            ->where([
                "slug" => $course_slug,
            ]);
        $course = $coursesQuery->first();

        $coursesQuery->update([
            "total_enrollment" => $course->total_enrollment + 1,
            "balance" => $course->balance + (($price * 70) / 100)
        ]);
        // it will update courses table total_enrollment, total_earning_course

    }
    public function makeAdmin(Request $request)
    {
        $email = $request->email;
        $userQuery = DB::table('users')
            ->where([
                "email" => $email,
            ]);

        $userDB =  $userQuery->first();
        if (count((array)$userDB)) {
            $role = $userDB->role;
            $roleArr = explode(" ", $role);

            if (in_array("admin", $roleArr)) {
                return response()->json(["ok" => true], 200);
            } else {
                $userQuery
                    ->update([
                        'role' => $role . ' admin'
                    ]);
                return response()->json(["ok" => true], 200);
            }
        } else {
            return response()->json(["message" => "User not found"], 404);
        }
    }
    public function currentAdmin(Request $request)
    {
        $user = $request->user();
        $userDB =  DB::table('users')
            ->where([
                "id" => $user->id,
            ])->get();
        $role = $userDB[0]->role;
        $roleArr = explode(" ", $role);

        if (in_array("admin", $roleArr)) {
            $userDB[0]->role = $roleArr;
            return response()->json(["ok" => true, "user" => $userDB[0]], 200);
        } else {
            return response()->json(["ok" => false], 403);
        }
    }
}
