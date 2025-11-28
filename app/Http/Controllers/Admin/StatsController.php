<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $stats = DB::table('tickets')
            ->select('route_id', DB::raw('count(*) as total'))
            ->where('status', 'paid')
            ->groupBy('route_id')
            ->orderByDesc('total')
            ->get();

        $routes = Route::pluck('name', 'id');

        return view('admin.stats', compact('stats', 'routes'));
    }
}
