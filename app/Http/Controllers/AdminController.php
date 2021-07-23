<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function confirmPayment(Request $request)
    {
        //  it will update by payment id
        // it will update users courses array,
        // it will update instructors balance,
        // it will update courses table totalCell
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
