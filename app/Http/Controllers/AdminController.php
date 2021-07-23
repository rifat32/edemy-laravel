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
        $user = $request->user();
        $userQuery = DB::table('users')
            ->where([
                "email" => $user->email,
            ]);
        $userDB =  $userQuery->get();
        $role = $userDB[0]->role;
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
    }
}
