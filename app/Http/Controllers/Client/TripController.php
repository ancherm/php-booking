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
        $now = now();
        $query = Trip::with(['route.bus'])
            ->whereHas('route', function($q) {
                $q->where('approved', true);
            })
            ->where(function($q) use ($now) {
                $q->where('date', '>', $now->toDateString())
                  ->orWhere(function($q2) use ($now) {
                      $q2->whereDate('date', $now->toDateString())
                         ->whereHas('route', function($q3) use ($now) {
                             $q3->whereTime('start', '>', $now->toTimeString());
                         });
                  });
            })
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
