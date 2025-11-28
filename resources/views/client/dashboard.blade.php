@extends('layouts.main')

@section('title', 'Личный кабинет клиента')

@section('content')
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Добро пожаловать, {{ auth()->user()->full_name }}!</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('client.trips.index') }}" class="bg-indigo-50 p-6 rounded-lg hover:bg-indigo-100 transition">
                <h3 class="text-lg font-semibold text-indigo-900 mb-2">Найти рейс</h3>
                <p class="text-indigo-700">Поиск доступных автобусных рейсов</p>
            </a>

            <a href="{{ route('client.orders.index') }}" class="bg-green-50 p-6 rounded-lg hover:bg-green-100 transition">
                <h3 class="text-lg font-semibold text-green-900 mb-2">Мои заказы</h3>
                <p class="text-green-700">Просмотр и управление заказами</p>
            </a>

            <a href="{{ route('client.passengers.index') }}" class="bg-purple-50 p-6 rounded-lg hover:bg-purple-100 transition">
                <h3 class="text-lg font-semibold text-purple-900 mb-2">Пассажиры</h3>
                <p class="text-purple-700">Управление списком пассажиров</p>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Последние заказы</h2>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                            <li>
                                <a href="{{ route('client.orders.show', $order) }}" class="block hover:bg-gray-50 px-4 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-indigo-600">
                                                {{ $order->trip->route->from_station }} → {{ $order->trip->route->to_station }}
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Дата: {{ $order->trip->date->format('d.m.Y') }} | 
                                                Автобус: {{ $order->trip->route->bus->name }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ $order->trip->route->price }} ₽</p>
                                            <p class="text-xs text-gray-500">{{ $order->orderPassengers->count() }} чел.</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

