@extends('layouts.main')

@section('title', 'Добавить автобус')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Добавить новый автобус</h1>
                <a href="{{ route('manager.buses.index') }}" class="text-indigo-600 hover:text-indigo-800">
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

            <form method="POST" action="{{ route('manager.buses.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="id" class="block text-sm font-medium text-gray-700 mb-2">
                        ID автобуса <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="id" 
                        id="id" 
                        value="{{ old('id') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Введите уникальный ID"
                    >
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Название автобуса <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Например: Mercedes Sprinter A101"
                    >
                </div>

                <div>
                    <label for="places" class="block text-sm font-medium text-gray-700 mb-2">
                        Количество мест <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="places" 
                        id="places" 
                        value="{{ old('places') }}"
                        min="1"
                        max="100"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Введите количество мест"
                    >
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('manager.buses.index') }}" class="text-gray-600 hover:text-gray-800">
                        Отмена
                    </a>
                    <button 
                        type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium"
                    >
                        Создать автобус
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

