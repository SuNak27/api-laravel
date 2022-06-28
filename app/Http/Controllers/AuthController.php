<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',]);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $response = [
                "satus" => 'Error',
                "message" => "Validate Error",
                "error" => $validate->errors(),
                "content" => null
            ];

            return response()->json($response, 200);
        } else {
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                // return Auth::attempt($credentials);
                $response = [
                    'status' => 'error',
                    'message' => "Unathorized",
                ];

                return response()->json($response, 401);
            }
            $user = User::where('email', $request->email)->firstOrFail();
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception('Error in Login');
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            // return $user;
            return response()
                ->json(['message' => 'Hi ' . $user->name . ', welcome to home', 'access_token' => $token, 'token_type' => 'Bearer',]);
        }
    }

    // method for user logout and delete token
    public function logout(Request $request)
    {
        return auth()->user()->tokens()->delete();
        $response = [
            'status' => 'success',
            'message' => 'Logout success',
        ];

        // return response()->json($response, 200);
    }
}
