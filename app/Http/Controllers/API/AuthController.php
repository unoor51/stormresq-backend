<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //User register
    public function register(Request $request)
    {
         $validated = $request->validate([
            'phone' => 'required|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|min:6',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $name = $request->first_name." ".$request->last_name;
        
        $user = User::create([
            'phone' => $validated['phone'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'verification_token' => Str::random(64),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        try {
            // Mail::to($user->email)->send(new \App\Mail\VerifyRescuerEmail($user));
            return response()->json([
                'message' => 'User registered. Please check your email to verify.',
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User registered. Please. But email could not been sent due to error: '.  $e->getMessage(),
                'token' => $token,
                'user' => $user,
            ], 201);
        }
    }
    
    // User Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }
        // // Account approved
        // if ($user->status == 'pending' || $user->status == 'rejected') {
        //     return response()->json(['message' => 'Your account is not yet approved'], 403);
        // }
        // // Account deactivated
        // if ($user->status == 'deactivated') {
        //     return response()->json(['message' => 'Your account has been deactivated by the admin. Please contact admin at info@stormresq.com.'], 403);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

}
