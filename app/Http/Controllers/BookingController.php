<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Seat;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function showBus($routeId, Request $request)
    {
        $route = Route::findOrFail($routeId);
        $seats = Seat::where('bus_id', $route->bus_id)->orderBy('number')->get();
        $travelDate = $request->get('date', Carbon::today()->format('Y-m-d'));

        return view('booking.bus', compact('route', 'seats', 'travelDate'));
    }

    public function reserve(Request $request, $seatId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $seat = Seat::findOrFail($seatId);
        $route = Route::findOrFail($request->route_id);
        $travelDate = Carbon::parse($request->date);

        if ($seat->isBooked($travelDate->format('Y-m-d'))) {
            return back()->with('error', 'Это место уже занято на выбранную дату.');
        }

        $price = $route->price;

        if ($seat->is_window) {
            $price += 200;
        }

        $with_pet = $request->has('with_pet');
        if ($with_pet) {
            $price += 300;
        }

        $dayOfWeek = $travelDate->dayOfWeek;
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            $price *= 1.15;
        }

        $reservedUntil = Carbon::now()->addMinutes(15);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'route_id' => $route->id,
            'seat_id' => $seat->id,
            'with_pet' => $with_pet,
            'price' => round($price, 2),
            'status' => 'pending',
            'reserved_until' => $reservedUntil,
            'travel_date' => $travelDate,
        ]);

        return redirect()->route('client.orders.ticket.payment', $ticket->id);
    }
}
