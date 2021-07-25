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
        $userQuery = DB::table('users')
            ->where([
                "id" => $user->id,
            ]);
        $userDB =  $userQuery->get();
        $role = $userDB[0]->role;
        $roleArr = explode(" ", $role);

        if (in_array("instructor", $roleArr)) {
            $userQuery
                ->update([
                    'bikashNumber' => $num
                ]);
        } else {
            $userQuery
                ->update([
                    'bikashNumber' => $num,
                    'role' => $role . ' instructor'
                ]);
        }

        return response()->json(["ok" => true], 200);
    }
    public function currentInstructor(Request $request)
    {
        $user = $request->user();
        $userDB =  DB::table('users')
            ->where([
                "id" => $user->id,
            ])->first();
        $role = $userDB->role;
        $roleArr = explode(" ", $role);

        if (in_array("instructor", $roleArr)) {
            $userDB->role = $roleArr;
            $userDB->password_reset_token = "";
            return response()->json(["ok" => true, "user" => $userDB], 200);
        } else {
            return response()->json(["ok" => false], 403);
        }
    }
}
