<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Trip;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'buses' => Bus::count(),
            'routes' => Route::count(),
            'approved_routes' => Route::where('approved', true)->count(),
            'pending_routes' => Route::where('approved', false)->count(),
            'upcoming_trips' => Trip::where('date', '>=', now())->count(),
        ];

        return view('manager.dashboard', compact('stats'));
    }
}
