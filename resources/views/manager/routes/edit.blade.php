@extends('layouts.main')

@section('title', 'Редактировать маршрут')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Основная информация о маршруте -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Редактировать маршрут #{{ $route->id }}
                    @if($route->approved)
                        <span class="ml-2 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Одобрен</span>
                    @endif
                </h1>
                <a href="{{ route('manager.routes.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Назад к списку
                </a>
            </div>

            @if($route->approved)
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        <strong>Внимание:</strong> Этот маршрут уже одобрен администратором и не может быть изменен. Вы можете только удалить его.
                    </p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$route->approved)
            <form method="POST" action="{{ route('manager.routes.update', $route) }}" class="space-y-6">
                @csrf
                @method('PUT')

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
                            <option value="{{ $bus->id }}" {{ old('bus_id', $route->bus_id) == $bus->id ? 'selected' : '' }}>
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
                            value="{{ old('from_station', $route->from_station) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
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
                            value="{{ old('to_station', $route->to_station) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
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
                            value="{{ old('start', $route->start) }}"
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
                            value="{{ old('duration', $route->duration) }}"
                            min="1"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
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
                            value="{{ old('price', $route->price) }}"
                            min="0"
                            step="0.01"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <form method="POST" action="{{ route('manager.routes.destroy', $route) }}" 
                          onsubmit="return confirm('Вы уверены, что хотите удалить этот маршрут? Все связанные рейсы также будут удалены.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                            Удалить маршрут
                        </button>
                    </form>

                    <div class="flex space-x-4">
                        <a href="{{ route('manager.routes.index') }}" class="text-gray-600 hover:text-gray-800">
                            Отмена
                        </a>
                        <button 
                            type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium"
                        >
                            Сохранить изменения
                        </button>
                    </div>
                </div>
            </form>
            @else
                <div class="flex justify-center">
                    <form method="POST" action="{{ route('manager.routes.destroy', $route) }}" 
                          onsubmit="return confirm('Вы уверены, что хотите удалить этот маршрут? Все связанные рейсы также будут удалены.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium">
                            Удалить маршрут
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if(!$route->approved)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Расписание</h2>

            @if($route->schedule)
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                    <p class="text-sm text-green-800">
                        <strong>Текущее расписание:</strong><br>
                        С {{ \Carbon\Carbon::parse($route->schedule->from_date)->format('d.m.Y') }} 
                        по {{ \Carbon\Carbon::parse($route->schedule->to_date)->format('d.m.Y') }}<br>
                        Период: 
                        @if($route->schedule->period == 'daily')
                            Ежедневно
                        @elseif($route->schedule->period == 'even')
                            Четные дни
                        @elseif($route->schedule->period == 'odd')
                            Нечетные дни
                        @else
                            {{ $route->schedule->period }}
                        @endif
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('manager.routes.schedule.store', $route) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Дата начала <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="from_date" 
                            id="from_date" 
                            value="{{ old('from_date', $route->schedule?->from_date) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <div>
                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Дата окончания <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="to_date" 
                            id="to_date" 
                            value="{{ old('to_date', $route->schedule?->to_date) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>

                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                        Периодичность <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="period" 
                        id="period" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Выберите период</option>
                        <option value="daily" {{ old('period', $route->schedule?->period) == 'daily' ? 'selected' : '' }}>
                            Ежедневно
                        </option>
                        <option value="even" {{ old('period', $route->schedule?->period) == 'even' ? 'selected' : '' }}>
                            Четные дни месяца
                        </option>
                        <option value="odd" {{ old('period', $route->schedule?->period) == 'odd' ? 'selected' : '' }}>
                            Нечетные дни месяца
                        </option>
                        <option value="1,3,5" {{ old('period', $route->schedule?->period) == '1,3,5' ? 'selected' : '' }}>
                            Понедельник, Среда, Пятница
                        </option>
                        <option value="2,4,6" {{ old('period', $route->schedule?->period) == '2,4,6' ? 'selected' : '' }}>
                            Вторник, Четверг, Суббота
                        </option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium"
                    >
                        {{ $route->schedule ? 'Обновить расписание' : 'Создать расписание' }}
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

