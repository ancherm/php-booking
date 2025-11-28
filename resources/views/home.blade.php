@extends('layouts.main')

@section('title', 'Главная - BusBooking')

@section('content')
<div class="text-center">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">
        Добро пожаловать в систему бронирования автобусных билетов
    </h1>
    <p class="text-xl text-gray-600 mb-8">
        Удобное бронирование билетов на автобусные рейсы
    </p>

    @guest
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                Войти
            </a>
            <a href="{{ route('register') }}" class="inline-block bg-white hover:bg-gray-50 text-indigo-600 border border-indigo-600 px-6 py-3 rounded-md font-medium">
                Регистрация
            </a>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Ваш аккаунт</h2>
            <p class="text-gray-600 mb-4">
                Вы вошли как: <strong>{{ auth()->user()->full_name }}</strong>
            </p>
            <p class="text-gray-600 mb-6">
                Роль: <strong class="text-indigo-600">
                    @if(auth()->user()->isAdmin()) Администратор
                    @elseif(auth()->user()->isManager()) Менеджер
                    @else Клиент
                    @endif
                </strong>
            </p>

            @if(auth()->user()->isClient())
                <a href="{{ route('client.trips.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                    Найти рейсы
                </a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                    Панель управления
                </a>
            @elseif(auth()->user()->isManager())
                <a href="{{ route('manager.dashboard') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                    Панель управления
                </a>
            @endif
        </div>
    @endguest
</div>
@endsection

