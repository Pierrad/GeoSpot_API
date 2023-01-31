<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceController extends Controller
{

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'geolocation' => 'required|string',
            'image' => 'required|image',
        ]);

        $validated['image'] = $request->file('image')->store('public');
        $validated['geolocation'] = json_decode($validated['geolocation']);
        $validated['creator'] = $request->user()->id;

        $place = Place::create($validated);
        return response()->json($place, 201);
    }

    public function update(Request $request, Place $place): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string',
            'geolocation' => 'string',
            'image' => 'image',
        ]);

        if (isset($validated['geolocation'])) {
            $validated['geolocation'] = json_decode($validated['geolocation']);
        }
        if (isset($validated['image'])) {
            $validated['image'] = $request->file('image')->store('image');
        }
        $place->update($validated);
        return response()->json($place, 200);
    }

    public function delete(Request $request, Place $place): JsonResponse
    {
        $place->delete();
        return response()->json(null, 204);
    }

    public function get(Request $request, Place $place): JsonResponse
    {
        return response()->json($place, 200);
    }

    public function getAround(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'geolocation' => 'required|string',
            'radius' => 'required|integer',
        ]);

        $validated['geolocation'] = json_decode($validated['geolocation']);
        $places = (new Place)->getAround($validated['geolocation'], $validated['radius']);

        // remove the places already discovered by the user and the places created by the user
        $places = array_filter($places, function ($place) use ($request) {
            return !$request->user()->discoveredPlaces->contains($place) && $place->creator != $request->user()->id;
        });

        $places = array_values($places);

        return response()->json($places, 200);
    }

    public function getAllCreated(Request $request): JsonResponse
    {
        $places = $request->user()->createdPlaces()->get();
        return response()->json($places, 200);
    }

    public function getCreated(Request $request, Place $place): JsonResponse
    {
        return response()->json($place, 200);
    }
}
