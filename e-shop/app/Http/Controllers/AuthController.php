<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login', 'register']]);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed',
                'error' => $validator->errors()
            ], 422);
        }

        try {
            $user = new User;
            $user->username = $request->input('username');
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed',
                'error' => $e->getMessage() // Include the error message in the response
            ], 409);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $credentials = request(['username','password']);

        if(!$token = auth()->attempt($credentials)){
            return response()->json([
                'success' => 0,
                'message' => 'Your Username & Password Not Registered',
            ], 403);
        } 

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']); 
    }

    public function getResponseUser()
    {
        $response = [
            'user' => auth()->user()
        ];

        return response()->json([
            'data' => $response,
            'success' => 1,
            'message' => 'Your action has been completed successfully.',
        ]);
    }
}
