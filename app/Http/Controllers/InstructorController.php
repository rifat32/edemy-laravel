<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    public function makeInstructor(Request $request)
    {
        $user = $request->user();
        $num = $request->num;
        $role = array(['subscriber', 'instructor']);
        $serializedRole = serialize($role);
        DB::table('users')
            ->where([
                "id" => $user->id,
            ])
            ->update([
                'bikashNumber' => $num,
                'role' => $serializedRole
            ]);
        return response()->json(["ok" => true], 200);
    }
    public function currentInstructor(Request $request)
    {
        $user = $request->user();
        $userDB =  DB::table('users')
            ->where([
                "id" => $user->id,
            ])->get();
        $role = unserialize($userDB[0]->role);
        $userDB[0]->role = unserialize($userDB[0]->role);
        if (in_array("instructor", $role[0])) {
            return response()->json(["ok" => true, "user" => $userDB[0]], 200);
        } else {
            return response()->json(["ok" => false], 403);
        }
    }
}
