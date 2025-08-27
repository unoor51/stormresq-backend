<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evacuee;
use App\Models\Settings;

class EvacueeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'peopleCount' => 'required|integer|min:1',
            'situation' => 'required|string',
            'needsPet' => 'boolean',
            'needsDisabled' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'required|string',
            'request_for' => 'required|in:myself,someone',
        ]);

        $evacueeData = [
            'phone'        => $validated['phone'],
            'people_count' => $validated['peopleCount'],
            'situation'    => $validated['situation'],
            'needs_pet'    => $validated['needsPet'] ?? false,
            'needs_disabled' => $validated['needsDisabled'] ?? false,
            'latitude'     => $validated['latitude'] ?? null,
            'longitude'    => $validated['longitude'] ?? null,
            'status'       => 'pending',
            'address'      => $validated['address'] ?? null,
            'request_for'  => $validated['request_for'],
        ];

        // Attach user_id if logged in
        if (!empty($request->user_id)) {
            $evacueeData['user_id'] =  $request->user_id;
        }

        $evacuee = Evacuee::create($evacueeData);

        $success_message = Settings::where('key', 'evacuee_success_message')->first();

        return response()->json([
            'message' => $success_message ? $success_message->value : 'Request submitted successfully.',
            'data'    => $evacuee,
        ], 201);
    }
    // User requests
    public function myRequests(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status', 'pending'); // default pending

        $requests = Evacuee::where('user_id', $user->id)
            ->where('status', $status)
            ->latest()
            ->get();

        return response()->json([
            'requests' => $requests
        ]);
    }

}