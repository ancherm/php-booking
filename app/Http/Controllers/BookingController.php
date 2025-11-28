<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Seat;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function showBus($routeId)
    {
        $route = Route::findOrFail($routeId);
        $seats = Seat::where('bus_id', $route->bus_id)->get();

        return view('booking.bus', compact('route', 'seats'));
    }

    public function reserve(Request $request, $seatId)
    {
        $seat = Seat::findOrFail($seatId);
        $route = $seat->bus->routes()->first();

        $price = $route->base_price;

        // Место у окна
        if ($seat->is_window) {
            $price += 150;
        }

        // Животное
        $with_pet = $request->has('with_pet');
        if ($with_pet) {
            $price += 300;
        }

        // Выходные
        $day = Carbon::parse($request->date)->dayOfWeek;
        if ($day == 0 || $day == 6) {
            $price *= 1.15;
        }

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'route_id' => $route->id,
            'seat_id' => $seat->id,
            'with_pet' => $with_pet,
            'price' => $price,
            'status' => 'pending'
        ]);

        return redirect()->route('payment.page', $ticket->id);
    }
}
