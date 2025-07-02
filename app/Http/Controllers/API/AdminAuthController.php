<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Rescuer;
use App\Models\Settings;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Admin Login
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'token' => $token,
            'admin' => $admin,
        ]);
    }
    // Admin register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
        ]);

        $admin = \App\Models\Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin,
        ]);
    }
    // settings
    public function settings(){
        $settings = Settings::all();
        return response()->json([
            'message' => 'Settings fetched successfully',
            'settings' => $settings
        ]);
    }
    // Update admin settings
    public function update_settings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($request->settings as $item) {
            Settings::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value']]
            );
        }
        $settings = Settings::all();
        return response()->json(['message' => 'Setting saved.', 'setting' => $settings]);
    }
    // Admin Dashboard Stats
    public function dashboardStats()
    {
        $evacueeCount = \App\Models\Evacuee::count();
        $rescuerCount = \App\Models\Rescuer::count();
        $pendingRescuer = \App\Models\Rescuer::where('status','pending')->count();
        $approvedRescuer = \App\Models\Rescuer::where('status','approved')->count();
        $rejectedRescuer = \App\Models\Rescuer::where('status','rejected')->count();
        $activeRescues = \App\Models\Evacuee::whereNotNull('rescuer_id')
            ->where('status', 'assigned')
            ->count();

        return response()->json([
            'evacuees' => $evacueeCount,
            'rescuers' => $rescuerCount,
            'active_rescues' => $activeRescues,
            'pendingRescuer' => $pendingRescuer,
            'rejectedRescuer' => $rejectedRescuer,
            'approvedRescuer' => $approvedRescuer,
        ]);
    }
    // Get all rescuers
    public function allRescuers()
    {
        $rescuers = \App\Models\Rescuer::latest()->get();

        return response()->json([
            'rescuers' => $rescuers,
        ]);
    }
    // Approve Rescuer
    public function approveRescuer($id)
    {
        $rescuer = Rescuer::findOrFail($id);
        $rescuer->status = 'approved';
        $rescuer->save();

        return response()->json(['message' => 'Rescuer approved']);
    }
    // Reject Rescuer
    public function rejectRescuer($id)
    {
        $rescuer = Rescuer::findOrFail($id);
        $rescuer->status = 'rejected';
        $rescuer->save();

        return response()->json(['message' => 'Rescuer rejected']);
    }
    // Rescue Requests
    public function rescueRequests(Request $request)
    {
        $status = $request->query('status');

        $query = \App\Models\Evacuee::query();

        if ($status === 'available') {
            $query->whereNull('rescuer_id');
        } elseif ($status === 'assigned') {
            $query->whereNotNull('rescuer_id')->where('status', 'assigned');
        } elseif (in_array($status, ['completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $rescues = $query->latest()->get();

        return response()->json(['rescues' => $rescues]);
    }


    // Admin logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Admin logged out']);
    }
}