<?php

namespace App\Http\Controllers\api;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400);
        }

        auth()->once($request->only('email', 'password'));
        if (!auth()->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username and Password invalid!'
            ], 401);
        }

        $user = User::find(auth()->user()->id);
        $token =  JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 200);

    }

    public function register(Request $request)
    {
        $validator = $request->validate([
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required',
        ]);

        if (!$validator) {
            return response()->json([
                'error'  => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400);
        }


        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 200);
    }

}
