<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $role = $user->role;
        $roleArr = explode(" ", $role);
        $user->role = $roleArr;
        return response()->json(["ok" => true, "user" => $user], 200);
    }
}
