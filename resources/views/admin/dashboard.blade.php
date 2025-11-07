@extends('layouts.main')

@section('title', 'Панель администратора')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-6">Панель администратора</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Всего пользователей</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Клиенты</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ $stats['clients'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Менеджеры</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['managers'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Заблокированные</h3>
        <p class="text-3xl font-bold text-red-600">{{ $stats['disabled_users'] }}</p>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Последние пользователи</h2>
        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-700">Все пользователи →</a>
    </div>
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Логин</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Роль</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($recentUsers as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->full_name }}</td>
                    <td class="px-6 py-4">{{ $user->login }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($user->user_type === 'admin') bg-red-100 text-red-800
                            @elseif($user->user_type === 'manager') bg-green-100 text-green-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($user->user_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->disabled)
                            <span class="text-red-600">Заблокирован</span>
                        @else
                            <span class="text-green-600">Активен</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

