@extends('layouts.main')

@section('title', 'Управление пользователями')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Пользователи</h1>
    <a href="{{ route('admin.users.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Добавить пользователя
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <form method="GET" class="flex gap-4">
        <select name="user_type" class="px-3 py-2 border border-gray-300 rounded-md">
            <option value="">Все роли</option>
            <option value="client" {{ request('user_type') == 'client' ? 'selected' : '' }}>Клиенты</option>
            <option value="manager" {{ request('user_type') == 'manager' ? 'selected' : '' }}>Менеджеры</option>
            <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Администраторы</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск..." class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
        <button type="submit" class="px-6 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Поиск</button>
    </form>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ФИО</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Логин</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Роль</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->id }}</td>
                    <td class="px-6 py-4">{{ $user->full_name }}</td>
                    <td class="px-6 py-4">{{ $user->login }}</td>
                    <td class="px-6 py-4">{{ ucfirst($user->user_type) }}</td>
                    <td class="px-6 py-4">
                        @if($user->disabled)
                            <span class="text-red-600">Заблокирован</span>
                        @else
                            <span class="text-green-600">Активен</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Редактировать</a>
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                {{ $user->disabled ? 'Разблокировать' : 'Заблокировать' }}
                            </button>
                        </form>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Удалить пользователя?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection

