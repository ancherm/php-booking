@extends('layouts.main')

@section('title', 'Панель менеджера')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-6">Панель менеджера</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Автобусы</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['buses'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Маршруты</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ $stats['routes'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Одобренные</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['approved_routes'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">На рассмотрении</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_routes'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="{{ route('manager.buses.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Управление автобусами</h3>
        <p class="text-gray-600">Просмотр, добавление и редактирование автобусов</p>
    </a>

    <a href="{{ route('manager.routes.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Управление маршрутами</h3>
        <p class="text-gray-600">Создание маршрутов и настройка расписания</p>
    </a>
</div>
@endsection

