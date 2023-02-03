<?php

namespace App\Http\Controllers;

use App\Models\DiscoveredPlace;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscoveredPlaceController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_place' => 'required|integer',
        ]);

        $validated['id_user'] = $request->user()->id;
        $validated['date'] = now();

        if (DiscoveredPlace::where('id_user', $validated['id_user'])->where('id_place', $validated['id_place'])->exists()) {
            return response()->json([
                'status' => '409',
                'message' => 'Already discovered'
            ], 409);
        }

        $discoveredPlace = DiscoveredPlace::create($validated);
        return response()->json($discoveredPlace, 201);
    }

    public function getAllDiscovered(Request $request): JsonResponse
    {
        $places = $request->user()->discoveredPlaces()->get();
        $places->load('place');
        return response()->json($places, 200);
    }

    public function getDiscovered(Request $request, DiscoveredPlace $discoveredPlace): JsonResponse
    {
        $discoveredPlace->load('place');
        return response()->json($discoveredPlace, 200);
    }
}
