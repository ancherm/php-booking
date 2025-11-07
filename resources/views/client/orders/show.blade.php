@extends('layouts.main')

@section('title', 'Заказ #' . $order->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('client.orders.index') }}" class="text-indigo-600 hover:text-indigo-700">&larr; Назад к заказам</a>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-6">Заказ #{{ $order->id }}</h1>

    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Информация о рейсе</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>Маршрут:</strong> {{ $order->trip->route->from_station }} → {{ $order->trip->route->to_station }}</div>
            <div><strong>Дата:</strong> {{ $order->trip->date->format('d.m.Y') }}</div>
            <div><strong>Время:</strong> {{ $order->trip->route->start }}</div>
            <div><strong>Автобус:</strong> {{ $order->trip->route->bus->name }}</div>
            <div><strong>Цена за место:</strong> {{ $order->trip->route->price }} ₽</div>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">Пассажиры</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пассажир</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Паспорт</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Номер билета</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->orderPassengers as $op)
                    <tr>
                        <td class="px-6 py-4">{{ $op->passenger->full_name }}</td>
                        <td class="px-6 py-4">{{ $op->passenger->passport }}</td>
                        <td class="px-6 py-4">{{ $op->ticket }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-right">
        <p class="text-xl font-bold">Итого: {{ $order->trip->route->price * $order->orderPassengers->count() }} ₽</p>
    </div>
</div>
@endsection

