@extends('layouts.main')

@section('title', 'Оплата заказа - BusBooking')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Оплата заказа №{{ $order->id }}</h2>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Информация о бронировании -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-lg text-blue-900 mb-3">Информация о бронировании</h3>
        <div class="space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
                <span class="font-medium">Маршрут:</span>
                <span>{{ $order->route->from_station }} → {{ $order->route->to_station }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Место:</span>
                <span>№{{ $order->seat->number }} 
                    @if($order->seat->is_window)
                        <span class="text-blue-600">(у окна)</span>
                    @endif
                    @if($order->seat->allows_pet)
                        <span class="text-purple-600">(с животным)</span>
                    @endif
                </span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Дата поездки:</span>
                <span>{{ $order->travel_date ? \Carbon\Carbon::parse($order->travel_date)->format('d.m.Y') : 'Не указана' }}</span>
            </div>
            @if($order->with_pet)
                <div class="flex justify-between">
                    <span class="font-medium">С животным:</span>
                    <span class="text-purple-600">Да (+300 ₽)</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Таймер резервирования -->
    @if($order->reserved_until)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg text-yellow-900 mb-1">Время на оплату</h3>
                    <p class="text-sm text-gray-600">Место зарезервировано до:</p>
                </div>
                <div class="text-right">
                    <div id="timer" class="text-2xl font-bold text-yellow-700"></div>
                    <div class="text-xs text-gray-500 mt-1">осталось времени</div>
                </div>
            </div>
            <div class="mt-3 w-full bg-yellow-200 rounded-full h-2">
                <div id="progressBar" class="bg-yellow-600 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>
    @endif

    <!-- Стоимость -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="font-semibold text-lg text-gray-900 mb-3">Детализация стоимости</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Базовая цена:</span>
                <span>{{ $order->route->price }} ₽</span>
            </div>
            @if($order->seat->is_window)
                <div class="flex justify-between text-blue-600">
                    <span>+ Место у окна:</span>
                    <span>200 ₽</span>
                </div>
            @endif
            @if($order->with_pet)
                <div class="flex justify-between text-purple-600">
                    <span>+ С животным:</span>
                    <span>300 ₽</span>
                </div>
            @endif
            @php
                $travelDate = $order->travel_date ? \Carbon\Carbon::parse($order->travel_date) : null;
                $isWeekend = $travelDate && ($travelDate->dayOfWeek == 0 || $travelDate->dayOfWeek == 6);
            @endphp
            @if($isWeekend)
                <div class="flex justify-between text-orange-600">
                    <span>+ Выходной день (15%):</span>
                    <span>{{ number_format(($order->price - ($order->route->price + ($order->seat->is_window ? 200 : 0) + ($order->with_pet ? 300 : 0))) * 100 / 115, 2) }} ₽</span>
                </div>
            @endif
            <div class="border-t border-gray-300 pt-2 mt-2">
                <div class="flex justify-between font-bold text-lg">
                    <span>Итого к оплате:</span>
                    <span class="text-indigo-600">{{ number_format($order->price, 2) }} ₽</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма оплаты -->
    <form method="POST" action="{{ route('client.orders.ticket.payment.process', $order->id) }}" id="paymentForm">
        @csrf
        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold mb-3">Данные для оплаты</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Номер карты</label>
                    <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="0000 0000 0000 0000" maxlength="19" pattern="[0-9\s]{13,19}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Срок действия</label>
                        <input type="text" name="expiry" id="expiry" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="MM/YY" maxlength="5" pattern="[0-9]{2}/[0-9]{2}" required>
                        <p class="text-xs text-red-500 mt-1 hidden" id="expiry-error">Срок действия карты истек</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                        <input type="text" name="cvv" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="123" maxlength="3" pattern="[0-9]{3}" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" id="payButton" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Оплатить {{ number_format($order->price, 2) }} ₽
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">Отменить бронирование</a>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    function validateExpiryDate(value, input, errorElement) {
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
        input.classList.remove('border-red-500');
        
        if (!value || value.length === 0) {
            return true;
        }
        
        if (value.length < 5) {
            return true;
        }
        
        const [month, year] = value.split('/');
        const expiryMonth = parseInt(month, 10);
        const expiryYear = 2000 + parseInt(year, 10);
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1;
        
        if (isNaN(expiryMonth) || expiryMonth < 1 || expiryMonth > 12) {
            if (errorElement) {
                errorElement.textContent = 'Неверный месяц (должен быть от 01 до 12)';
                errorElement.classList.remove('hidden');
            }
            input.classList.add('border-red-500');
            return false;
        }
        
        if (isNaN(expiryYear) || expiryYear < 2000 || expiryYear > 2100) {
            if (errorElement) {
                errorElement.textContent = 'Неверный год';
                errorElement.classList.remove('hidden');
            }
            input.classList.add('border-red-500');
            return false;
        }
        
        if (expiryYear < currentYear || (expiryYear === currentYear && expiryMonth < currentMonth)) {
            if (errorElement) {
                errorElement.textContent = 'Срок действия карты истек';
                errorElement.classList.remove('hidden');
            }
            input.classList.add('border-red-500');
            return false;
        }
        
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
        input.classList.remove('border-red-500');
        return true;
    }
    
    function initExpiryInput() {
        const expiryInput = document.getElementById('expiry');
        const expiryError = document.getElementById('expiry-error');
        
        if (!expiryInput) {
            return;
        }
        
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            if (e.target.value !== value) {
                const cursorPos = e.target.selectionStart;
                e.target.value = value;
                
                if (value.length === 3 && cursorPos === 2) {
                    e.target.setSelectionRange(3, 3);
                } else if (cursorPos > value.length) {
                    e.target.setSelectionRange(value.length, value.length);
                }
            }
            
            validateExpiryDate(value, expiryInput, expiryError);
        });
        
        expiryInput.addEventListener('blur', function(e) {
            validateExpiryDate(e.target.value, expiryInput, expiryError);
        });
        
        expiryInput.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 3 && this.value.charAt(2) === '/') {
                this.value = this.value.substring(0, 2);
                e.preventDefault();
            }
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExpiryInput);
    } else {
        initExpiryInput();
    }
    
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const expiryInput = document.getElementById('expiry');
            const expiryError = document.getElementById('expiry-error');
            
            if (!expiryInput) {
                return;
            }
            
            const expiryValue = expiryInput.value;
            
            if (expiryValue.length !== 5 || !expiryValue.includes('/')) {
                e.preventDefault();
                if (expiryError) {
                    expiryError.textContent = 'Введите срок действия в формате MM/YY';
                    expiryError.classList.remove('hidden');
                }
                expiryInput.classList.add('border-red-500');
                expiryInput.focus();
                return false;
            }
            
            if (!validateExpiryDate(expiryValue, expiryInput, expiryError)) {
                e.preventDefault();
                expiryInput.focus();
                return false;
            }
        });
    }
})();

@if($order->reserved_until)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reservedUntil = new Date('{{ $order->reserved_until->toIso8601String() }}').getTime();
    const timerElement = document.getElementById('timer');
    const progressBar = document.getElementById('progressBar');
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    
    function updateTimer() {
        const now = new Date().getTime();
        const distance = reservedUntil - now;
        
        if (distance < 0) {
            timerElement.textContent = '00:00';
            progressBar.style.width = '0%';
            payButton.disabled = true;
            payButton.textContent = 'Время истекло';
            payButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            
            // Перенаправляем на главную страницу через 2 секунды
            setTimeout(() => {
                window.location.href = '{{ route("home") }}';
            }, 2000);
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timerElement.textContent = 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
        
        // Обновляем прогресс-бар (15 минут = 900000 мс)
        const totalTime = 15 * 60 * 1000;
        const elapsed = totalTime - distance;
        const progress = Math.max(0, Math.min(100, (elapsed / totalTime) * 100));
        progressBar.style.width = (100 - progress) + '%';
        
        // Меняем цвет при приближении к истечению
        if (distance < 5 * 60 * 1000) { // Меньше 5 минут
            timerElement.classList.remove('text-yellow-700');
            timerElement.classList.add('text-red-600');
            progressBar.classList.remove('bg-yellow-600');
            progressBar.classList.add('bg-red-600');
        }
    }
    
    updateTimer();
    const interval = setInterval(updateTimer, 1000);
    
    // Очистка интервала при уходе со страницы
    window.addEventListener('beforeunload', function() {
        clearInterval(interval);
    });
});
</script>
@endif
@endsection
