<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Route;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'clients' => User::where('user_type', 'client')->count(),
            'managers' => User::where('user_type', 'manager')->count(),
            'admins' => User::where('user_type', 'admin')->count(),
            'disabled_users' => User::where('disabled', true)->count(),
            'total_orders' => Order::count(),
            'total_routes' => Route::count(),
        ];

        $recentUsers = User::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
