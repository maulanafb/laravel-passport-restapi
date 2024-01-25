<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 400);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successfully',
            'user' => Auth::user(),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Logout successfully',
        ]);
    }

    public function profile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Profile retrieved successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
