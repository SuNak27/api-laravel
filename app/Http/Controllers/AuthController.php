<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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
        $validate = Validator::make([
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validator error',
                'error' => $validate->errors()
            ];

            return response()->json($response, Response::HTTP_OK);
        } else {
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                $response = [
                    'success' => false,
                    'message' => 'Unauthorized',
                ];

                return response()->json($response, Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception('Error in login');
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()
                ->json(['message' => 'Hi ' . $user->name . ', welcome to home', 'access_token' => $token, 'token_type' => 'Bearer',]);
        }
    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
