<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Check if the 'email' input field is not empty
            if (!empty($request->input('email'))) {
                $credentials = $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                // Attempt to authenticate the user using provided credentials
                if (Auth::attempt($credentials)) {
                    // Get the authenticated user
                    $user = auth()->user();
                    // Create a response containing user information and access token
                    $tokenResponse = $this->createTokenResponse($user);
                    $userResource = new UserResource($user);

                    // Return a JSON response with successful login information
                    return response()->json([
                        'message' => 'Login successful',
                        'data' => [
                            'user' => $userResource,
                            'access_token' => $tokenResponse['access_token'],
                            'token_type' => $tokenResponse['token_type'],
                        ],
                    ]);
                }

                // Return a JSON response for invalid credentials
                return response()->json(['message' => 'Invalid credentials'], 401);
            } else {
                // Return a JSON response indicating that 'email' data is required

                return response()->json([
                    'status' => 'error',
                    'message' => 'send the data',
                ],
                );
            }
        } catch (\Exception $e) {

            // Return a JSON response in case of an exception, such as a server error
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function createTokenResponse($user)
    {
        // Create a new access token for the user and get the plain text token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return an array containing the access token and token type
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function revoke(Request $request)
    {
        // Revoke the current user's token, effectively logging them out
        $request->user()->token()->revoke();
        
        // Return a JSON response indicating that the token has been revoked
        return response()->json(['message' => 'Token revoked']);
    }
}
