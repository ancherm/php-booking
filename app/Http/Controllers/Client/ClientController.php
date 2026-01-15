<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $client = $user->client;
        $recentOrders = $client->orders()->with(['trip.route.bus'])->latest()->take(5)->get();
        
        return view('client.dashboard', compact('recentOrders'));
    }

    public function passengers()
    {
        $user = auth()->user();
        $client = $user->client;
        $passengers = $client->passengers;
        
        return view('client.passengers', compact('passengers'));
    }

    public function storePassenger(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'passport' => [
                'required',
                'string',
                'regex:/^\d{4}\s?\d{6}$/',
            ],
        ], [
            'passport.regex' => 'Паспорт должен быть в формате: 1234 567890 (серия 4 цифры, номер 6 цифр)',
        ]);

        $user = auth()->user();
        $client = $user->client;

        try {
            $passport = preg_replace('/^(\d{4})(\d{6})$/', '$1 $2', $validated['passport']);
            
            $passenger = Passenger::create([
                'client_id' => $client->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'passport' => $passport,
            ]);

            // Если передан trip_id, возвращаемся на страницу создания заказа
            if ($request->has('trip_id')) {
                return redirect()->route('client.orders.create', ['trip' => $request->trip_id])
                    ->with('success', 'Пассажир успешно добавлен. Теперь вы можете выбрать его для бронирования.');
            }

            return redirect()->route('client.passengers.index')->with('success', 'Пассажир успешно добавлен');
        } catch (\Exception $e) {
            if ($request->has('trip_id')) {
                return redirect()->route('client.orders.create', ['trip' => $request->trip_id])
                    ->with('error', 'Ошибка при добавлении пассажира: ' . $e->getMessage())
                    ->withInput();
            }
            return back()->with('error', 'Ошибка при добавлении пассажира: ' . $e->getMessage())->withInput();
        }
    }

    public function deletePassenger(Passenger $passenger)
    {
        $user = auth()->user();
        $client = $user->client;
        
        if ($passenger->client_id !== $client->id) {
            abort(403);
        }

        $passenger->delete();

        return redirect()->route('client.passengers.index')->with('success', 'Пассажир удален');
    }
}
