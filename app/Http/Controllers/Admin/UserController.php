<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('login', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'patronymic' => 'nullable|string|max:50',
            'login' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'user_type' => 'required|in:client,admin,manager',
            'email' => 'required_if:user_type,client|email|max:50|unique:clients',
            'phone' => 'required_if:user_type,client|string|size:12|unique:clients',
            'position' => 'required_if:user_type,admin|string|max:30',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'patronymic' => $validated['patronymic'] ?? null,
                'login' => $validated['login'],
                'password' => Hash::make($validated['password']),
                'user_type' => $validated['user_type'],
            ]);

            if ($validated['user_type'] === 'client') {
                Client::create([
                    'id' => $user->id,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            } elseif ($validated['user_type'] === 'admin') {
                Admin::create([
                    'id' => $user->id,
                    'position' => $validated['position'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Пользователь создан');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(User $user)
    {
        $user->load('client', 'admin');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'patronymic' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'email' => 'required_if:user_type,client|email|max:50|unique:clients,email,' . $user->id . ',id',
            'phone' => 'required_if:user_type,client|string|size:12|unique:clients,phone,' . $user->id . ',id',
            'position' => 'required_if:user_type,admin|string|max:30',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'patronymic' => $validated['patronymic'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            if ($user->user_type === 'client' && $user->client) {
                $user->client->update([
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            } elseif ($user->user_type === 'admin' && $user->admin) {
                $user->admin->update([
                    'position' => $validated['position'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Пользователь обновлен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка: ' . $e->getMessage())->withInput();
        }
    }

    public function toggleStatus(User $user)
    {
        $user->disabled = !$user->disabled;
        $user->save();

        $status = $user->disabled ? 'заблокирован' : 'разблокирован';
        return back()->with('success', "Пользователь {$status}");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить свой аккаунт');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Пользователь удален');
    }
}
