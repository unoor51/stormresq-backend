<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rescuer;
use App\Models\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\ClickSendService;

class RescuerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|unique:rescuers',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:rescuers',
            'password' => 'required|string|min:6',
        ]);

        $rescuer = Rescuer::create([
            'phone' => $validated['phone'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
        ]);

        $token = $rescuer->createToken('auth_token')->plainTextToken;
        $success_message = Settings::where('key', 'rescuer_success_message')->first();
        return response()->json([
            'message' => $success_message ? $success_message->value : 'Request submitted successfully.',
            'token' => $token,
            'rescuer' => $rescuer,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $rescuer = Rescuer::where('phone', $request->phone)->first();

        if (!$rescuer || !Hash::check($request->password, $rescuer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($rescuer->status !== 'approved') {
            return response()->json(['message' => 'Your account is not yet approved'], 403);
        }

        $token = $rescuer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'rescuer' => $rescuer,
        ]);
    }
    // Get Profile
    public function profile(Request $request)
    {
        return response()->json([
            'rescuer' => $request->user(),
        ]);
    }
    // Get Rescuer Dashboard Stats
    public function dashboardStats(Request $request)
    {
        $rescuer = $request->user();
        $user_name = $rescuer->first_name." ".$rescuer->last_name;
        $assigned_rescues = $rescuer->evacuees()->where('status','assigned')->count();
        $completed_rescues = $rescuer->evacuees()->where('status','completed')->count();
        $cancelled_rescues = $rescuer->evacuees()->where('status','cancelled')->count();
        
        return response()->json([
            'user_name' => $user_name,
            'assigned_rescues' => $assigned_rescues,
            'completed_rescues' => $completed_rescues,
            'cancelled_rescues' => $cancelled_rescues,
        ]);
    }
    // Update Profile
    public function updateProfile(Request $request)
    {
        $rescuer = $request->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string',
            'password' => 'nullable|min:6',
        ]);

        $rescuer->first_name = $validated['first_name'];
        $rescuer->last_name = $validated['last_name'];
        $rescuer->phone = $validated['phone'];

        if (!empty($validated['password'])) {
            $rescuer->password = Hash::make($validated['password']);
        }

        $rescuer->save();

        return response()->json(['message' => 'Profile updated']);
    }

    // Assigned Rescues Requests
    public function assignedRescues(Request $request)
    {
        $rescuer = $request->user();

        $evacuees = $rescuer->evacuees()->where('status','assigned')->latest()->get();

        return response()->json([
            'rescues' => $evacuees
        ]);
    }
    // completedRescues Requests
    public function completedRescues(Request $request)
    {
        $rescuer = $request->user();

        $evacuees = $rescuer->evacuees()->where('status','completed')->latest()->get();

        return response()->json([
            'rescues' => $evacuees
        ]);
    }
    // cancelledRescues Requests
    public function cancelledRescues(Request $request)
    {
        $rescuer = $request->user();

        $evacuees = $rescuer->evacuees()->where('status','cancelled')->latest()->get();

        return response()->json([
            'rescues' => $evacuees
        ]);
    }
    
    public function notifyRescuer(Request $request, ClickSendService $clickSend)
    {
        // Sample data - in real use, you’d fetch from DB or form
        $phone = $request->input('phone'); // Example: +15555555555
        $message = 'You have been assigned a new rescue request. Please check your dashboard.';

        $response = $clickSend->sendSMS($phone, $message);

        return response()->json($response);
    }
    // availableRescues
    public function availableRescues()
    {
        $evacuees = \App\Models\Evacuee::whereNull('rescuer_id')->where('status','pending')->latest()->get();

        return response()->json([
            'rescues' => $evacuees
        ]);
    }
    // allEvacuees
    public function allEvacuees()
    {
        return response()->json([
            'evacuees' => \App\Models\Evacuee::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get(),
        ]);
    }

    // Assigned to me
    public function assignRescue($id)
    {
        $rescuer = auth()->user();

        $evacuee = \App\Models\Evacuee::where('id', $id)
            ->whereNull('rescuer_id')
            ->first();

        if (!$evacuee) {
            return response()->json(['message' => 'Rescue not available or already assigned.'], 404);
        }

        $evacuee->rescuer_id = $rescuer->id;
        $evacuee->status = 'assigned';
        $evacuee->save();
         //3. Send SMS to Rescuer
        // $clickSend->sendSMS(
        //     $rescuer->phone,
        //     "New Assignment:\nEvacuee: {$evacuee->first_name} {$evacuee->last_name}\nPhone: {$evacuee->phone}\nLocation: {$evacuee->address}"
        // );

        // 📲 4. Send SMS to Evacuee
        // $clickSend->sendSMS(
        //     $evacuee->phone,
        //     "Rescuer Assigned:\nName: {$rescuer->first_name} {$rescuer->last_name}\nPhone: {$rescuer->phone}\nThey're on their way!"
        // );
        return response()->json(['message' => 'Rescue successfully assigned.']);
    }

    public function cancelRescue($id)
    {
        $rescuer = auth()->user();

        $rescue = \App\Models\Evacuee::where('id', $id)
            ->where('rescuer_id', $rescuer->id)
            ->first();

        if (!$rescue) {
            return response()->json(['message' => 'Rescue not found or not assigned to you'], 404);
        }

        $rescue->status = 'cancelled';
        $rescue->save();

        return response()->json(['message' => 'Rescue marked as cancelled']);
    }

    public function completeRescue($id)
    {
        $rescuer = auth()->user();

        $rescue = \App\Models\Evacuee::where('id', $id)
            ->where('rescuer_id', $rescuer->id)
            ->first();

        if (!$rescue) {
            return response()->json(['message' => 'Rescue not found or not assigned to you'], 404);
        }

        $rescue->status = 'completed'; // Optional: add `status` column to track completion
        $rescue->save();

        return response()->json(['message' => 'Rescue marked as completed']);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}