@extends('layouts.main')

@section('title', 'Управление маршрутами')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Маршруты</h1>
    <a href="{{ route('manager.routes.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Создать маршрут
    </a>
</div>

<div class="space-y-4">
    @foreach($routes as $route)
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold mb-2">{{ $route->from_station }} → {{ $route->to_station }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>Автобус: {{ $route->bus->name }}</div>
                        <div>Время отправления: {{ $route->start }}</div>
                        <div>Длительность: {{ $route->duration }} мин</div>
                        <div>Цена: {{ $route->price }} ₽</div>
                    </div>
                    <div class="mt-2">
                        @if($route->approved)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Одобрен</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">На рассмотрении</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    @if(!$route->approved)
                        <a href="{{ route('manager.routes.edit', $route) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-center hover:bg-indigo-700">
                            Редактировать
                        </a>
                        <form method="POST" action="{{ route('manager.routes.approve', $route) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Одобрить
                            </button>
                        </form>
                    @else
                        <span class="px-4 py-2 bg-gray-300 text-gray-600 rounded-md text-center cursor-not-allowed">
                            Одобрен
                        </span>
                    @endif
                    <form method="POST" action="{{ route('manager.routes.destroy', $route) }}" onsubmit="return confirm('Удалить маршрут?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Удалить
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $routes->links() }}
</div>
@endsection

