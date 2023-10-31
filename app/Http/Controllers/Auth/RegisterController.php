<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function UserRegister(UserRegistrationRequest $request)
    {
        try {

            // Retrieve validated data from the request
            $validatedData = $request->validated(); // Retrieve validated data

            if ($validatedData) {
                // Create a new user using the validated data
                User::create([
                    'name' => $validatedData['name'],
                    'user_name' => $validatedData['user_name'],
                    'email' => $validatedData['email'],
                    'password' => bcrypt($validatedData['password']),
                ]);

                // Return a JSON response indicating a successful user registration
                return response()->json([
                    'status' => 'success',
                    'message' => 'User Is Successfully Registered',
                ]);
            } else {
                // Return a JSON response in case of validation failure, including error messages
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $request->validator->errors()->all(),
                ], 422);
            }
        } catch (\Exception $e) {
            // Return a JSON response in case of an exception, such as a server error
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
