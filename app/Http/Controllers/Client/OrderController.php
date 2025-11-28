<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Trip;
use App\Models\Place;
use App\Models\OrderPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $client = auth()->user()->client;
        $orders = $client->orders()
            ->with(['trip.route.bus', 'orderPassengers.passenger'])
            ->latest()
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function create(Trip $trip)
    {
        $trip->load(['route.bus', 'places']);
        $client = auth()->user()->client;
        $passengers = $client->passengers;
        $occupiedPlaces = $trip->places()->whereNotNull('passenger_id')->pluck('number_place')->toArray();
        $totalPlaces = $trip->route->bus->places;
        $availablePlaces = [];
        
        for ($i = 1; $i <= $totalPlaces; $i++) {
            if (!in_array($i, $occupiedPlaces)) {
                $availablePlaces[] = $i;
            }
        }

        return view('client.orders.create', compact('trip', 'passengers', 'availablePlaces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.passenger_id' => 'required|exists:passengers,id',
            'passengers.*.place_number' => 'required|integer|min:1',
        ]);

        $client = auth()->user()->client;
        $trip = Trip::findOrFail($validated['trip_id']);

        if (!$trip->hasAvailablePlaces(count($validated['passengers']))) {
            return back()->with('error', 'Недостаточно свободных мест')->withInput();
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'trip_id' => $trip->id,
                'client_id' => $client->id,
            ]);

            foreach ($validated['passengers'] as $passengerData) {
                $passenger = $client->passengers()->findOrFail($passengerData['passenger_id']);
                
                $place = Place::firstOrCreate([
                    'trip_id' => $trip->id,
                    'number_place' => $passengerData['place_number'],
                ]);

                if ($place->passenger_id !== null) {
                    throw new \Exception('Место ' . $passengerData['place_number'] . ' уже занято');
                }

                $place->passenger_id = $passenger->id;
                $place->save();

                OrderPassenger::create([
                    'order_id' => $order->id,
                    'passenger_id' => $passenger->id,
                    'ticket' => Str::random(10) . '-' . $order->id,
                ]);
            }

            $trip->reservePlaces(count($validated['passengers']));

            DB::commit();

            return redirect()->route('client.orders.show', $order)->with('success', 'Заказ успешно создан');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при создании заказа: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        $client = auth()->user()->client;
        
        if ($order->client_id !== $client->id) {
            abort(403);
        }

        $order->load(['trip.route.bus', 'orderPassengers.passenger']);

        return view('client.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $client = auth()->user()->client;
        
        if ($order->client_id !== $client->id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $placesCount = $order->orderPassengers->count();
            Place::where('trip_id', $order->trip_id)
                ->whereIn('passenger_id', $order->orderPassengers->pluck('passenger_id'))
                ->update(['passenger_id' => null]);

            $trip = $order->trip;
            $trip->free_places += $placesCount;
            $trip->save();

            $order->delete();

            DB::commit();

            return redirect()->route('client.orders.index')->with('success', 'Заказ отменен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при отмене заказа: ' . $e->getMessage());
        }
    }
}
