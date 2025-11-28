@extends('layouts.main')

@section('title', 'Создание заказа')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Создание заказа</h1>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Информация о рейсе</h2>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><strong>Маршрут:</strong> {{ $trip->route->from_station }} → {{ $trip->route->to_station }}</div>
        <div><strong>Дата:</strong> {{ $trip->date->format('d.m.Y') }}</div>
        <div><strong>Время:</strong> {{ $trip->route->start }}</div>
        <div><strong>Автобус:</strong> {{ $trip->route->bus->name }}</div>
        <div><strong>Цена:</strong> {{ $trip->route->price }} ₽</div>
        <div><strong>Свободных мест:</strong> {{ $trip->free_places }}</div>
    </div>
</div>

<form method="POST" action="{{ route('client.orders.store') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Выбор пассажиров и мест</h2>

        @if($passengers->count() == 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                <p>У вас нет добавленных пассажиров. <a href="{{ route('client.passengers.index') }}" class="text-indigo-600 underline">Добавить пассажира</a></p>
            </div>
        @endif

        <div id="passenger-container" class="space-y-4">
            <div class="passenger-row flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пассажир</label>
                    <select name="passengers[0][passenger_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Выберите пассажира</option>
                        @foreach($passengers as $passenger)
                            <option value="{{ $passenger->id }}">{{ $passenger->full_name }} ({{ $passenger->passport }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Место</label>
                    <select name="passengers[0][place_number]" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">№</option>
                        @foreach($availablePlaces as $place)
                            <option value="{{ $place }}">{{ $place }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($passengers->count() > 0)
            <button type="button" onclick="addPassenger()" class="mt-4 text-indigo-600 hover:text-indigo-700">
                + Добавить пассажира
            </button>
        @endif
    </div>

    <div class="flex justify-between">
        <a href="{{ route('client.trips.index') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Отмена
        </a>
        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">
            Оформить заказ
        </button>
    </div>
</form>

<script>
let passengerIndex = 1;
function addPassenger() {
    const container = document.getElementById('passenger-container');
    const newRow = document.querySelector('.passenger-row').cloneNode(true);
    
    newRow.querySelectorAll('select').forEach(select => {
        select.name = select.name.replace('[0]', `[${passengerIndex}]`);
        select.value = '';
    });
    
    container.appendChild(newRow);
    passengerIndex++;
}
</script>
@endsection

