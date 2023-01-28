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

        $discoveredPlace = DiscoveredPlace::create($validated);
        return response()->json($discoveredPlace, 201);
    }

    public function getDiscovered(Request $request): JsonResponse
    {
        $places = $request->user()->discoveredPlaces()->get();
        $places->load('place');
        return response()->json($places, 200);
    }
}