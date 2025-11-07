<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Bus;
use App\Models\RouteSchedule;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::with('bus')->paginate(10);
        return view('manager.routes.index', compact('routes'));
    }

    public function create()
    {
        $buses = Bus::all();
        return view('manager.routes.create', compact('buses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'from_station' => 'required|string|max:255',
            'to_station' => 'required|string|max:255',
            'start' => 'required',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $route = Route::create($validated);

        return redirect()->route('manager.routes.index')->with('success', 'Маршрут создан');
    }

    public function edit(Route $route)
    {
        if ($route->approved) {
            return redirect()->route('manager.routes.index')
                ->with('error', 'Нельзя редактировать одобренный маршрут');
        }

        $buses = Bus::all();
        $route->load('schedule');
        return view('manager.routes.edit', compact('route', 'buses'));
    }

    public function update(Request $request, Route $route)
    {
        if ($route->approved) {
            return redirect()->route('manager.routes.index')
                ->with('error', 'Нельзя редактировать одобренный маршрут');
        }

        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'from_station' => 'required|string|max:255',
            'to_station' => 'required|string|max:255',
            'start' => 'required',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $route->update($validated);

        return redirect()->route('manager.routes.index')->with('success', 'Маршрут обновлен');
    }

    public function approve(Route $route)
    {
        $route->approved = true;
        $route->save();

        return back()->with('success', 'Маршрут одобрен');
    }

    public function storeSchedule(Request $request, Route $route)
    {
        if ($route->approved) {
            return back()->with('error', 'Нельзя изменять расписание одобренного маршрута');
        }

        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'period' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            RouteSchedule::updateOrCreate(
                ['route_id' => $route->id],
                $validated
            );

            $this->generateTrips($route, $validated);

            DB::commit();

            return back()->with('success', 'Расписание создано и поездки сгенерированы');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    private function generateTrips(Route $route, array $schedule)
    {
        $startDate = Carbon::parse($schedule['from_date']);
        $endDate = Carbon::parse($schedule['to_date']);
        $period = $schedule['period'];

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $shouldCreateTrip = false;

            switch ($period) {
                case 'daily':
                    $shouldCreateTrip = true;
                    break;
                case 'even':
                    $shouldCreateTrip = $currentDate->day % 2 == 0;
                    break;
                case 'odd':
                    $shouldCreateTrip = $currentDate->day % 2 != 0;
                    break;
                default:
                    $days = explode(',', $period);
                    $shouldCreateTrip = in_array($currentDate->dayOfWeek, $days);
                    break;
            }

            if ($shouldCreateTrip) {
                Trip::firstOrCreate(
                    [
                        'route_id' => $route->id,
                        'date' => $currentDate->toDateString(),
                    ],
                    [
                        'free_places' => $route->bus->places,
                    ]
                );
            }

            $currentDate->addDay();
        }
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('manager.routes.index')->with('success', 'Маршрут удален');
    }
}
