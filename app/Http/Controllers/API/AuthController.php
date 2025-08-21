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
            // $verificationUrl = url('/user/verify/' . $user->verification_token);
            // Mail::to($user->email)->send(new \App\Mail\VerifyRescuerEmail($user,$verificationUrl));

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

    // Send password reet link to the email
    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:rescuers,email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Generate token and save to password_resets table
        $token = \Str::random(64);
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => \Hash::make($token),
                'created_at' => now()
            ]
        );

        $frontendUrl = url('/user/reset-password?token=' . $token.'&email='.$user->email);

        // Send custom reset email
        // Mail::to($user->email)->send(new RescuerResetPassword($user->first_name, $frontendUrl));

        return response()->json(['message' => 'Reset link sent successfully']);
    }

    // Reset Password Function
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('rescuers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($rescuer, $password) {
                $rescuer->password = bcrypt($password);
                $rescuer->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Invalid token or email'], 400);
    }

    //  User Logout functionlaity
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
