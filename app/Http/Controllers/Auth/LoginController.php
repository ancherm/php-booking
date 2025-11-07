<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            $user = auth()->user();

            if ($user->disabled) {
                auth()->logout();
                return back()->with('error', 'Ваш аккаунт заблокирован');
            }

            return match($user->user_type) {
                'admin' => redirect()->route('admin.dashboard'),
                'manager' => redirect()->route('manager.dashboard'),
                'client' => redirect()->route('client.dashboard'),
                default => redirect()->route('home'),
            };
        }

        return back()->with('error', 'Неверный логин или пароль');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Вы успешно вышли из системы');
    }
}
