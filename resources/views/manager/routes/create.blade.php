@extends('layouts.main')

@section('title', 'Создать маршрут')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Создать новый маршрут</h1>
                <a href="{{ route('manager.routes.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Назад к списку
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('manager.routes.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Автобус <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="bus_id" 
                        id="bus_id" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Выберите автобус</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->name }} ({{ $bus->places }} мест)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="from_station" class="block text-sm font-medium text-gray-700 mb-2">
                            Станция отправления <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="from_station" 
                            id="from_station" 
                            value="{{ old('from_station') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Москва"
                        >
                    </div>

                    <div>
                        <label for="to_station" class="block text-sm font-medium text-gray-700 mb-2">
                            Станция прибытия <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="to_station" 
                            id="to_station" 
                            value="{{ old('to_station') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Санкт-Петербург"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="start" class="block text-sm font-medium text-gray-700 mb-2">
                            Время отправления <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="time" 
                            name="start" 
                            id="start" 
                            value="{{ old('start') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Длительность (мин) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="duration" 
                            id="duration" 
                            value="{{ old('duration') }}"
                            min="1"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="480"
                        >
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Цена (₽) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="price" 
                            id="price" 
                            value="{{ old('price') }}"
                            min="0"
                            step="0.01"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="1500.00"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('manager.routes.index') }}" class="text-gray-600 hover:text-gray-800">
                        Отмена
                    </a>
                    <button 
                        type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium"
                    >
                        Создать маршрут
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

