<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;

class LocationController extends Controller
{
    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->get();

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get();

        return response()->json($cities);
    }
}
