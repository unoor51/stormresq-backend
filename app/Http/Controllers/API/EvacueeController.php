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

        $evacuee = Evacuee::create([
            'phone' => $validated['phone'],
            'people_count' => $validated['peopleCount'],
            'situation' => $validated['situation'],
            'needs_pet' => $validated['needsPet'] ?? false,
            'needs_disabled' => $validated['needsDisabled'] ?? false,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => 'pending',
            'address' => $validated['address'] ?? null,
            'request_for' => $validated['request_for'],
        ]);

        $success_message = Settings::where('key', 'evacuee_success_message')->first();
        return response()->json([
            'message' => $success_message ? $success_message->value : 'Request submitted successfully.',
            'data' => $evacuee
        ], 201);
    }
}