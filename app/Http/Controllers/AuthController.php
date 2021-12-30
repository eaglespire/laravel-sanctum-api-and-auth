<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);

        /*
         * Create a new user
         */
        $user = User::create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'password'=>bcrypt($fields['password'])
        ]);
        /*
         * create a token
         */
        $token = $user->createToken('myAppToken')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token
        ];
        return response($response, 201);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string'
        ]);

        /*
         * Check the user's email
         */
        $user = User::where('email', $fields['email'])->first();

        /*
         * Check the password
         */
        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message'=>'Bad Credentials',
            ], 401);
        }

        /*
         * create a token
         */
        $token = $user->createToken('myAppToken')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token,
            'message'=>'Logged in Success'
        ];
        return response($response, 201);
    }

    public function logout(Request $request){
      auth()->user()->tokens()->delete();
      return ['message'=>'Logged out'];
    }
}
