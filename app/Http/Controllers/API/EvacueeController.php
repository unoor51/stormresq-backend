<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evacuee;

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
        ]);

        $evacuee = Evacuee::create([
            'phone' => $validated['phone'],
            'people_count' => $validated['peopleCount'],
            'situation' => $validated['situation'],
            'needs_pet' => $validated['needsPet'] ?? false,
            'needs_disabled' => $validated['needsDisabled'] ?? false,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return response()->json([
            'message' => 'Request Submitted Successfully.',
            'data' => $evacuee
        ], 201);
    }
}