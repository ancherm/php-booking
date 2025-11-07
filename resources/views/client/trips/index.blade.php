@extends('layouts.main')

@section('title', 'Поиск рейсов')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Поиск рейсов</h1>
</div>

<!-- Форма поиска -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('client.trips.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Откуда</label>
            <input type="text" name="from_station" value="{{ request('from_station') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Город отправления">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Куда</label>
            <input type="text" name="to_station" value="{{ request('to_station') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Город назначения">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Дата</label>
            <input type="date" name="date" value="{{ request('date') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Найти
            </button>
        </div>
    </form>
</div>

<!-- Результаты -->
@if($trips->count() > 0)
    <div class="space-y-4">
        @foreach($trips as $trip)
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $trip->route->from_station }} → {{ $trip->route->to_station }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>Дата: {{ $trip->date->format('d.m.Y') }}</div>
                            <div>Время: {{ $trip->route->start }}</div>
                            <div>Длительность: {{ $trip->route->duration }} мин</div>
                            <div>Автобус: {{ $trip->route->bus->name }}</div>
                            <div>Свободных мест: {{ $trip->free_places }}</div>
                            <div>Цена: <strong>{{ $trip->route->price }} ₽</strong></div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('client.orders.create', $trip) }}" 
                            class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                            Забронировать
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $trips->links() }}
    </div>
@else
    <div class="bg-white shadow rounded-lg p-6 text-center">
        <p class="text-gray-500">Рейсы не найдены. Попробуйте изменить параметры поиска.</p>
    </div>
@endif
@endsection

