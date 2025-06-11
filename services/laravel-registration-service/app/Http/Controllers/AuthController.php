<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // 'unique:users' checks MySQL
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400); // Return validation errors
        }

        try {
            // 2. Save User Data to MySQL
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
            ]);

            // 3. Publish "user.registered" Event to Redis
            // We'll send a JSON string with user details for the Notification Service
            Redis::publish('user.registered', json_encode([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'timestamp' => now()->toDateTimeString(),
            ]));

            return response()->json(['message' => 'User registered successfully and notification event sent!', 'user' => $user], 201);

        } catch (\Exception $e) {
            // Log the error and return a generic error response
            \Log::error('Error during user registration: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'User registration failed. Please try again later.'], 500);
        }
    }
}
