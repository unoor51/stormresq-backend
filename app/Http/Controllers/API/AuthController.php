<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Password;

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
            'peopleCount' => 'required|integer|min:1',
            'needsPet' => 'boolean',
            'needsDisabled' => 'boolean',
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
            'people_count' => $validated['peopleCount'],
            'pets' => $validated['needsPet'] ?? false,
            'disabled' => $validated['needsDisabled'] ?? false,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        try {
            $verificationUrl = url('/user/verify/' . $user->verification_token);
            Mail::to($user->email)->send(new \App\Mail\VerifyEmail($user,$verificationUrl,'user'));

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
            'email' => 'required|email|exists:users,email',
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
        Mail::to($user->email)->send(new ResetPassword($user->name, $frontendUrl));

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

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Invalid token or email'], 400);
    }

    // Verfiy email after registration
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return response()->view('rescuer.verify-failed', [], 400); // Optional: create this view for failed cases
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return view('user.verify-success'); // Show success page
    }

    // Get Profile
    public function profile(Request $request)
    {
        return response()->json([
            'rescuer' => $request->user(),
        ]);
    }

     // Update Profile
    public function updateProfile(Request $request)
    {
        $rescuer = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string',
            'password' => 'nullable|min:6',
            'people_count' => 'required|integer|min:1',
            'pets' => 'boolean',
            'disabled' => 'boolean',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rescuer->name = $validated['name'];
        $rescuer->phone = $validated['phone'];
        $rescuer->people_count = $validated['people_count'];
        $rescuer->pets =  $validated['pets'] ?? false;
        $rescuer->disabled =  $validated['disabled'] ?? false;
        $rescuer->address =  $validated['address'];
        $rescuer->latitude =  $validated['latitude'];
        $rescuer->longitude =  $validated['longitude'];

        if (!empty($validated['password'])) {
            $rescuer->password = Hash::make($validated['password']);
        }

        $rescuer->save();

        return response()->json(['message' => 'Profile updated']);
    }
    
    //  User Logout functionlaity
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
