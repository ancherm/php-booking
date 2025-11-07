<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Route;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with(['route.bus'])
            ->whereHas('route', function($q) {
                $q->where('approved', true);
            })
            ->where('date', '>=', now()->toDateString())
            ->where('free_places', '>', 0);

        if ($request->filled('from_station')) {
            $query->whereHas('route', function($q) use ($request) {
                $q->where('from_station', 'like', '%' . $request->from_station . '%');
            });
        }

        if ($request->filled('to_station')) {
            $query->whereHas('route', function($q) use ($request) {
                $q->where('to_station', 'like', '%' . $request->to_station . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $trips = $query->orderBy('date')->paginate(10);

        return view('client.trips.index', compact('trips'));
    }

    public function show(Trip $trip)
    {
        $trip->load(['route.bus', 'places']);
        
        return view('client.trips.show', compact('trip'));
    }
}
