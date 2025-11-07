<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'patronymic' => 'nullable|string|max:50',
            'login' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:50|unique:clients',
            'phone' => 'required|string|size:12|unique:clients',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'login.unique' => 'Этот логин уже занят',
            'email.unique' => 'Этот email уже зарегистрирован',
            'phone.unique' => 'Этот номер телефона уже зарегистрирован',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'patronymic' => $validated['patronymic'],
                'login' => $validated['login'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'client',
                'disabled' => false,
            ]);

            Client::create([
                'id' => $user->id,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            DB::commit();

            auth()->login($user);

            return redirect()->route('client.dashboard')->with('success', 'Регистрация успешно завершена');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при регистрации: ' . $e->getMessage())->withInput();
        }
    }
}
