@extends('layouts.main')

@section('title', 'Мои заказы')
/
@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-6">Мои заказы</h1>

@if($orders->count() > 0)
    <div class="space-y-4">
        @foreach($orders as $order)
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold mb-2">
                            {{ $order->trip->route->from_station }} → {{ $order->trip->route->to_station }}
                        </h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p>Дата: {{ $order->trip->date->format('d.m.Y') }}</p>
                            <p>Автобус: {{ $order->trip->route->bus->name }}</p>
                            <p>Пассажиры: {{ $order->orderPassengers->count() }} чел.</p>
                            <p>Цена: {{ $order->trip->route->price * $order->orderPassengers->count() }} ₽</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('client.orders.show', $order) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-center hover:bg-indigo-700">
                            Подробнее
                        </a>
                        <form method="POST" action="{{ route('client.orders.destroy', $order) }}" onsubmit="return confirm('Отменить заказ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Отменить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@else
    <div class="bg-white shadow rounded-lg p-6 text-center">
        <p class="text-gray-500 mb-4">У вас пока нет заказов</p>
        <a href="{{ route('client.trips.index') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Найти рейс
        </a>
    </div>
@endif
@endsection

