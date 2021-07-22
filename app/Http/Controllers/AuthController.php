<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {

            return response(['errors' => $validator->errors()->all(), 'status' => 422]);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        User::create($request->toArray());


        return response(["ok" => true], 200);
    }
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials', 'status' => 422]);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $role = auth()->user()->role;
        $roleArr = explode(" ", $role);

        auth()->user()->role = $roleArr;

        return response()->json(['user' => auth()->user(), 'token' => $accessToken,   "ok" => true], 200);
    }
    public function getCurrentUser(Request $request)
    {
        $user = $request->user();
        $userDB =  DB::table('users')
            ->where([
                "id" => $user->id,
            ])->first();
        $role = $userDB->role;
        $roleArr = explode(" ", $role);
        $userDB->role = $roleArr;
        return response()->json(["ok" => true, "user" => $userDB], 200);
    }
    public function sendToken(Request $request)
    {

        // $nowInMilliseconds = (int) ($now->timestamp . str_pad($now->milli, 3, '0', STR_PAD_LEFT));
        $token = $request->token;
        $email = $request->email;
        $userQuery = DB::table('users')
            ->where([
                "email" => $email
            ]);
        if ($userQuery->exists()) {
            $userQuery
                ->update([
                    "password_reset_token" => $token,
                    "updated_at" => \Carbon\Carbon::now(),
                ]);
            return response()->json(["ok" => true]);
        } else {
            return response()->json(["msg" => "user not found"], 401);
        }
    }
    public function verifyToken(Request $request)
    {
        $token = $request->token;
        $email = $request->email;
        $password = $request->password;
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {

            return response(['errors' => $validator->errors()->all(), 'status' => 422]);
        }
        $userQuery = DB::table('users')
            ->where([
                "email" => $email
            ]);
        $user = $userQuery->first();
        $carbonTime =   \Carbon\Carbon::create($user->updated_at);
        $timeNow = \Carbon\Carbon::now();
        $carbonTimeInMilliseconds = (int) ($carbonTime->timestamp . str_pad($carbonTime->milli, 3, '0', STR_PAD_LEFT));
        $timeNowInMilliseconds = (int) ($timeNow->timestamp . str_pad($timeNow->milli, 3, '0', STR_PAD_LEFT));
        $checkTime = $timeNowInMilliseconds -  $carbonTimeInMilliseconds;
        $checkTime = $checkTime / 1000;
        if (300 < $checkTime) {

            return response()->json(["message" => "token expired"], 410);
        } else {
            if ($user->password_reset_token == $token) {
                $userQuery
                    ->update([
                        "password" => Hash::make($password)
                    ]);
                return response()->json(["message" => "password has been updated"], 200);
            } else {
                return response()->json(["message" => "invalid token"], 401);
            }
        }
    }
}
