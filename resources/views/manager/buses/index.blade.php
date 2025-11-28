@extends('layouts.main')

@section('title', 'Управление автобусами')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Автобусы</h1>
    <a href="{{ route('manager.buses.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Добавить автобус
    </a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Мест</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($buses as $bus)
                <tr>
                    <td class="px-6 py-4">{{ $bus->id }}</td>
                    <td class="px-6 py-4">{{ $bus->name }}</td>
                    <td class="px-6 py-4">{{ $bus->places }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('manager.buses.edit', $bus) }}" class="text-indigo-600 hover:text-indigo-900">Редактировать</a>
                        <form method="POST" action="{{ route('manager.buses.destroy', $bus) }}" class="inline" onsubmit="return confirm('Удалить автобус?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $buses->links() }}
</div>
@endsection

