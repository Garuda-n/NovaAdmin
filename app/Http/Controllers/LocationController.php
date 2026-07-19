<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Get states by country ID for AJAX dropdowns.
     */
    public function getStates(int $countryId): JsonResponse
    {
        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name', 'state_code']);

        return response()->json($states);
    }

    /**
     * Get cities by state ID for AJAX dropdowns.
     */
    public function getCities(int $stateId): JsonResponse
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }
}
