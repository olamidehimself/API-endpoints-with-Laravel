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
    //
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        /**
         * If the form is not validated via
         * the Api throw an error with the
         * error message to the user
         * @param:validator error
         * @return:error
         */


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400);
        }

        /**
         * If the user is not authenticated via
         * the Api throw an error with the
         * error message to the user
         * @param:validator error
         * @return:error
         */

        auth()->once($request->only('email', 'password'));
        if (!auth()->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username and Password invalid!'
            ], 401);
        }

        /**
         * If the user is found and Authenticated
         * then render the details and generate an
         * access token for the user to access the site
         * @param: access token
         * @return: success 200
         */

        $user = User::find(auth()->user()->id);
        $token =  JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $user,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ], 200);

    }

    public function register(Request $request)
    {
        $validator = $request->validate([
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required',
        ]);

        // Validate

        if (!$validator) {
            return response()->json([
                'error'  => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400);
        }

        /**
         * Now if the validation passes
         * get the user/by creating the user
         * and then return token
         */

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /**
         * Generate token for the user
         * For data to be persisted as
         * API's don't use session Data
         */

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $user,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ], 200);
    }

    public function user(Request $request)
    {
        $user = User::with('users_courses.course')->find(auth()->user()->id);

        return response()->json($user, 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'Successfully logged out']);
    }

}
