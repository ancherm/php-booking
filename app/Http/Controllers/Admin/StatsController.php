<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Order;
use App\Models\OrderPassenger;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        $routeStats = DB::table('orders')
            ->join('trips', 'orders.trip_id', '=', 'trips.id')
            ->select('trips.route_id',
                DB::raw('count(*) as total_orders'),
                DB::raw('count(CASE WHEN orders.status = "paid" THEN 1 END) as paid_orders'),
                DB::raw('count(CASE WHEN orders.status = "pending" THEN 1 END) as pending_orders'),
                DB::raw('sum(CASE WHEN orders.status = "paid" THEN orders.total_price ELSE 0 END) as total_revenue')
            )
            ->groupBy('trips.route_id')
            ->orderByDesc('paid_orders')
            ->get();

        $routes = Route::with('bus')->get()->keyBy('id');

        $totalRevenue = Order::where('status', 'paid')->sum('total_price');

        $windowSeatsSold = OrderPassenger::whereHas('order', function($q) {
            $q->where('status', 'paid');
        })
        ->whereHas('order.trip.route', function($q) {
            $q->whereNotNull('id');
        })
        ->get()
        ->filter(function($op) {
            $placeNumber = $op->order->trip->places()->where('passenger_id', $op->passenger_id)->first();
            if (!$placeNumber) return false;
            $positionInRow = (($placeNumber->number_place - 1) % 4) + 1;
            return $positionInRow == 1 || $positionInRow == 4;
        })->count();

        $petTickets = OrderPassenger::whereHas('order', function($q) {
            $q->where('status', 'paid');
        })->where('with_pet', true)->count();

        $totalStats = [
            'total_tickets' => OrderPassenger::count(),
            'paid_tickets' => OrderPassenger::whereHas('order', function($q) {
                $q->where('status', 'paid');
            })->count(),
            'pending_tickets' => OrderPassenger::whereHas('order', function($q) {
                $q->where('status', 'pending');
            })->count(),
            'total_revenue' => $totalRevenue,
            'window_seats_sold' => $windowSeatsSold,
            'pet_tickets' => $petTickets,
        ];

        $weekdayStats = DB::table('orders')
            ->join('trips', 'orders.trip_id', '=', 'trips.id')
            ->select(DB::raw('DAYOFWEEK(trips.date) as day_of_week'),
                DB::raw('count(*) as tickets_count'),
                DB::raw('sum(CASE WHEN orders.status = "paid" THEN orders.total_price ELSE 0 END) as revenue')
            )
            ->whereNotNull('trips.date')
            ->where('orders.status', 'paid')
            ->groupBy('day_of_week')
            ->get();

        $recentStats = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as tickets_count'),
                DB::raw('sum(CASE WHEN status = "paid" THEN total_price ELSE 0 END) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.stats', compact('routeStats', 'routes', 'totalStats', 'weekdayStats', 'recentStats'));
    }
}
