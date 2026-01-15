@extends('layouts.main')

@section('title', 'Создание заказа')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Создание заказа</h1>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Информация о рейсе</h2>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><strong>Маршрут:</strong> {{ $trip->route->from_station }} → {{ $trip->route->to_station }}</div>
        <div><strong>Дата:</strong> {{ $trip->date->format('d.m.Y') }}</div>
        <div><strong>Время:</strong> {{ $trip->route->start }}</div>
        <div><strong>Автобус:</strong> {{ $trip->route->bus->name }}</div>
        <div><strong>Цена:</strong> {{ $trip->route->price }} ₽</div>
        <div><strong>Свободных мест:</strong> {{ $trip->free_places }}</div>
    </div>
</div>

<form method="POST" action="{{ route('client.orders.store') }}" class="space-y-6" id="orderForm">
    @csrf
    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Выбор пассажиров</h2>
            <button type="button" onclick="openAddPassengerModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                + Добавить нового пассажира
            </button>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
        @endif

        @if($passengers->count() == 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
            <p>У вас нет добавленных пассажиров. <button type="button" onclick="openAddPassengerModal()" class="text-indigo-600 underline">Добавить пассажира</button></p>
        </div>
        @endif

        <div id="passenger-container" class="space-y-4 mb-6">
            <!-- Пассажиры будут добавляться сюда -->
        </div>

        @if($passengers->count() > 0)
        <button type="button" onclick="addPassenger()" class="mb-6 text-indigo-600 hover:text-indigo-700">
            + Добавить пассажира
        </button>
        @endif
    </div>

    <!-- Схема автобуса -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Выберите места в автобусе</h2>

        <!-- Легенда -->
        <div class="mb-6 flex flex-wrap gap-4 justify-center p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 border-2 border-green-500 rounded"></div>
                <span class="text-sm">Свободно</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 border-2 border-blue-500 rounded"></div>
                <span class="text-sm">У окна (+200 ₽)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-300 border-2 border-gray-500 rounded"></div>
                <span class="text-sm">Занято</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 border-2 border-yellow-500 rounded"></div>
                <span class="text-sm">Выбрано</span>
            </div>
        </div>

        <!-- Схема автобуса -->
        <div class="bus-container">
            <!-- Кабина водителя -->
            <div class="text-center mb-4">
                <div class="inline-block bg-gray-800 text-white px-6 py-2 rounded-t-lg">
                    <span class="text-sm">Кабина водителя</span>
                </div>
            </div>

            <!-- Места автобуса -->
            <div class="bus-layout">
                @php
                $totalPlaces = $trip->route->bus->places;
                $occupiedPlaces = $trip->places()->whereNotNull('passenger_id')->pluck('number_place')->toArray();
                $seatsPerRow = 4;

                $leftSidePlaces = [];
                $rightSidePlaces = [];

                for ($i = 1; $i <= $totalPlaces; $i++) {
                $positionInRow = (($i - 1) % $seatsPerRow) + 1;
                if ($positionInRow <= 2) {
                $leftSidePlaces[] = $i;
                } else {
                $rightSidePlaces[] = $i;
                }
                }
                @endphp

                <!-- Левая сторона (окна) -->
                <div class="bus-side bus-left">
                    @foreach($leftSidePlaces as $placeNum)
                    @php
                    $isOccupied = in_array($placeNum, $occupiedPlaces);
                    $positionInRow = (($placeNum - 1) % $seatsPerRow) + 1;
                    $isWindow = ($positionInRow == 1);
                    @endphp
                    <div class="seat-wrapper"
                         data-place-number="{{ $placeNum }}"
                         data-is-window="{{ $isWindow ? '1' : '0' }}"
                         data-is-occupied="{{ $isOccupied ? '1' : '0' }}">
                        <button type="button"
                                class="seat-button {{ $isWindow ? 'window-seat' : '' }} {{ $isOccupied ? 'booked' : 'available' }}"
                                data-place-number="{{ $placeNum }}"
                                {{ $isOccupied ? 'disabled' : '' }}>
                        <div class="seat-number">{{ $placeNum }}</div>
                        @if($isWindow)
                        <div class="seat-icon">🪟</div>
                        @endif
                        </button>
                    </div>
                    @endforeach
                </div>

                <!-- Проход -->
                <div class="bus-aisle">
                    <div class="aisle-line"></div>
                </div>

                <!-- Правая сторона (проход) -->
                <div class="bus-side bus-right">
                    @foreach($rightSidePlaces as $placeNum)
                    @php
                    $isOccupied = in_array($placeNum, $occupiedPlaces);
                    $positionInRow = (($placeNum - 1) % $seatsPerRow) + 1;
                    $isWindow = ($positionInRow == $seatsPerRow);
                    @endphp
                    <div class="seat-wrapper"
                         data-place-number="{{ $placeNum }}"
                         data-is-window="{{ $isWindow ? '1' : '0' }}"
                         data-is-occupied="{{ $isOccupied ? '1' : '0' }}">
                        <button type="button"
                                class="seat-button {{ $isWindow ? 'window-seat' : '' }} {{ $isOccupied ? 'booked' : 'available' }}"
                                data-place-number="{{ $placeNum }}"
                                {{ $isOccupied ? 'disabled' : '' }}>
                        <div class="seat-number">{{ $placeNum }}</div>
                        @if($isWindow)
                        <div class="seat-icon">🪟</div>
                        @endif
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Задняя часть автобуса -->
            <div class="text-center mt-4">
                <div class="inline-block bg-gray-300 px-6 py-2 rounded-b-lg">
                    <span class="text-sm text-gray-600">Задняя часть</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Детализация стоимости в реальном времени -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Детализация стоимости</h2>

        <div class="mb-4 space-y-2" id="price-breakdown">
            <!-- Детализация будет заполнена через JavaScript -->
        </div>

        <div class="border-t border-gray-300 pt-4 mt-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Итого к оплате:</p>
                    <p class="text-3xl font-bold text-indigo-600" id="total-price">{{ number_format($trip->route->price, 2) }} ₽</p>
                </div>
            </div>
        </div>

        <div class="mt-4 text-xs text-gray-500">
            <p>* Места у окна (крайние места в ряду): +200 ₽</p>
            <p>* Проезд с животным: +300 ₽</p>
            @php
            $isWeekend = $trip->date->dayOfWeek == 0 || $trip->date->dayOfWeek == 6;
            @endphp
            @if($isWeekend)
            <p class="text-orange-600 font-semibold">* Выходной день: цена увеличена на 15%</p>
            @endif
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('client.trips.index') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Отмена
        </a>
        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">
            Оформить заказ
        </button>
    </div>
</form>

<!-- Шаблон для пассажира -->
<template id="passenger-template">
    <div class="passenger-row border border-gray-200 rounded-lg p-4">
        <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Пассажир</label>
                <div class="flex gap-2">
                    <select name="passengers[__INDEX__][passenger_id]" required
                            class="passenger-select w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Выберите пассажира</option>
                        @foreach($passengers as $passenger)
                        @php
                        $isPaid = in_array($passenger->id, $paidPassengerIds ?? []);
                        @endphp
                        <option value="{{ $passenger->id }}" {{ $isPaid ? 'disabled' : '' }}>
                        {{ $passenger->full_name }} ({{ $passenger->passport }}){{ $isPaid ? ' - уже имеет билет на этот рейс' : '' }}
                        </option>
                        @endforeach
                    </select>
                    <button type="button" class="set-active-btn px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-md text-sm whitespace-nowrap" title="Выбрать активным для выбора места">
                        Выбрать активным
                    </button>
                </div>
            </div>
            <div class="w-48 ml-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Выбранное место</label>
                <div class="flex gap-2">
                    <div class="selected-place-display flex-1 px-3 py-2 border-2 border-gray-300 rounded-md bg-gray-50 text-center text-gray-500">
                        Не выбрано
                    </div>
                    <button type="button" class="clear-place-btn px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-sm" title="Очистить место">
                        ✕
                    </button>
                </div>
                <input type="hidden" name="passengers[__INDEX__][place_number]" class="place-input" value="">
            </div>
        </div>
        <div class="flex justify-between items-center mb-3">
            <div class="flex items-center">
                <input type="checkbox" name="passengers[__INDEX__][with_pet]" class="pet-checkbox mr-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" value="1">
                <label class="text-sm text-gray-700">Проезд с животным (+300 ₽)</label>
            </div>
            <button type="button" class="remove-passenger-btn text-xs text-red-600 hover:text-red-800">
                Удалить пассажира
            </button>
        </div>
        <div class="text-sm text-gray-600">
            <span class="passenger-price">Цена: <span class="font-semibold passenger-price-value">{{ number_format($trip->route->price, 2) }}</span> ₽</span>
            <span class="ml-4 passenger-price-details text-xs text-gray-500"></span>
        </div>
        <div class="mt-2 text-xs text-indigo-600 font-semibold active-passenger-indicator hidden">
            ✓ Активный пассажир для выбора места
        </div>
    </div>
</template>

<!-- Модальное окно для добавления пассажира -->
<div id="addPassengerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Добавить нового пассажира</h3>
                <button type="button" onclick="closeAddPassengerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('client.passengers.store') }}" id="addPassengerForm">
                @csrf
                <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('first_name') }}">
                        @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Фамилия</label>
                        <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('last_name') }}">
                        @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Паспорт</label>
                        <input type="text" name="passport" required placeholder="1234 567890" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('passport') }}">
                        <p class="mt-1 text-xs text-gray-500">Формат: 1234 567890 (серия 4 цифры, номер 6 цифр)</p>
                        @error('passport')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeAddPassengerModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Добавить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bus-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 80vh;
        overflow-y: auto;
    }

    .bus-layout {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin: 1rem 0;
    }

    .bus-side {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .bus-aisle {
        width: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .aisle-line {
        width: 2px;
        height: 100%;
        background: repeating-linear-gradient(
            to bottom,
            #d1d5db 0px,
            #d1d5db 8px,
            transparent 8px,
            transparent 16px
        );
    }

    .seat-wrapper {
        position: relative;
    }

    .seat-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 50px;
        max-height: 60px;
        aspect-ratio: 1;
        border: 2px solid #4b5563;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        background: #f3f4f6;
        position: relative;
        padding: 4px;
    }

    .seat-button.available:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .seat-button.window-seat {
        background: #dbeafe;
        border-color: #3b82f6;
    }

    .seat-button.booked {
        background: #d1d5db;
        border-color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .seat-button.selected {
        background: #fef3c7;
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
    }

    .seat-button:disabled {
        cursor: not-allowed;
    }

    .seat-number {
        font-weight: bold;
        font-size: 0.75rem;
        color: #1f2937;
        line-height: 1;
    }

    .seat-icon {
        font-size: 0.65rem;
        margin-top: 1px;
        line-height: 1;
    }

    .selected-place-display {
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .selected-place-display.has-place {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
        font-weight: bold;
    }

    .passenger-row.active-passenger {
        border-color: #4f46e5;
        border-width: 2px;
        background-color: #eef2ff;
    }

    .active-passenger-indicator {
        display: block;
    }

    .price-breakdown-item {
        display: flex;
        justify-content: space-between;
        padding: 2px 0;
    }

    .price-breakdown-item .label {
        color: #6b7280;
    }

    .price-breakdown-item .value {
        font-weight: 500;
    }

    .price-breakdown-item .sub-item {
        padding-left: 20px;
        font-size: 0.9em;
    }

    .price-breakdown-item.total {
        border-top: 1px solid #e5e7eb;
        margin-top: 8px;
        padding-top: 8px;
        font-weight: bold;
        color: #1f2937;
    }

    .set-active-btn.active {
        background-color: #4f46e5;
        color: white;
    }

    @media (max-width: 768px) {
        .bus-container {
            padding: 0.75rem;
            max-height: 70vh;
        }

        .bus-layout {
            flex-direction: column;
            gap: 0.5rem;
        }

        .bus-aisle {
            width: 100%;
            height: 15px;
        }

        .aisle-line {
            width: 100%;
            height: 2px;
        }

        .seat-button {
            min-height: 45px;
            max-height: 55px;
        }

        .seat-number {
            font-size: 0.7rem;
        }

        .seat-icon {
            font-size: 0.6rem;
        }
    }
</style>

<script>
    class OrderFormManager {
        constructor() {
            this.passengerCount = 0;
            this.currentActivePassenger = null;
            this.selectedPlaces = new Set();
            this.basePrice = {{ $trip->route->price }};
            this.isWeekend = {{ ($trip->date->dayOfWeek == 0 || $trip->date->dayOfWeek == 6) ? 'true' : 'false' }};
            this.weekendMultiplier = 1.15;
            this.windowSeatPrice = 200;
            this.petPrice = 300;
            this.paidPassengerIds = @json($paidPassengerIds ?? []);

            this.init();
        }

        init() {
            this.setupEventListeners();
            this.addFirstPassenger();
        }

        setupEventListeners() {
            // Делегирование событий для контейнера пассажиров
            const passengerContainer = document.getElementById('passenger-container');
            if (passengerContainer) {
                passengerContainer.addEventListener('change', (e) => {
                    if (e.target.classList.contains('passenger-select')) {
                        this.onPassengerSelectChange(e);
                    } else if (e.target.classList.contains('pet-checkbox')) {
                        this.calculateTotal();
                    }
                });

                passengerContainer.addEventListener('click', (e) => {
                    if (e.target.classList.contains('set-active-btn')) {
                        this.setActivePassenger(e.target.closest('.passenger-row'));
                    } else if (e.target.classList.contains('clear-place-btn')) {
                        this.clearPlace(e.target.closest('.passenger-row'));
                    } else if (e.target.classList.contains('remove-passenger-btn')) {
                        this.removePassenger(e.target.closest('.passenger-row'));
                    }
                });
            }

            // Обработчики для мест в автобусе
            document.querySelectorAll('.seat-button.available').forEach(button => {
                button.addEventListener('click', (e) => {
                    const placeNumber = parseInt(e.target.closest('.seat-button').dataset.placeNumber);
                    this.selectPlace(placeNumber);
                });
            });

            // Модальное окно
            const modal = document.getElementById('addPassengerModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeAddPassengerModal();
                    }
                });
            }

            // Валидация формы
            const orderForm = document.getElementById('orderForm');
            if (orderForm) {
                orderForm.addEventListener('submit', (e) => this.validateForm(e));
            }
        }

        addFirstPassenger() {
            this.addPassenger();
        }

        addPassenger() {
            const template = document.getElementById('passenger-template');
            if (!template) return;

            const passengerRow = template.content.cloneNode(true);
            const index = this.passengerCount;

            // Заменяем плейсхолдеры
            const htmlString = new XMLSerializer().serializeToString(passengerRow);
            const processedHtml = htmlString.replace(/__INDEX__/g, index);

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = processedHtml;
            const newRow = tempDiv.firstElementChild;

            // Добавляем data-атрибут для идентификации
            newRow.dataset.passengerIndex = index;

            // Добавляем в контейнер
            document.getElementById('passenger-container').appendChild(newRow);

            this.passengerCount++;

            // Если это первый пассажир, делаем его активным по умолчанию
            if (this.passengerCount === 1) {
                this.setActivePassenger(newRow);
            }

            // Обновляем UI
            this.updateRemoveButtons();
            this.calculateTotal();
        }

        setActivePassenger(passengerRow) {
            // Сбрасываем активность у всех пассажиров
            document.querySelectorAll('.passenger-row').forEach(row => {
                row.classList.remove('active-passenger');
                const indicator = row.querySelector('.active-passenger-indicator');
                if (indicator) indicator.classList.add('hidden');

                const btn = row.querySelector('.set-active-btn');
                if (btn) {
                    btn.classList.remove('active');
                    btn.textContent = 'Выбрать активным';
                }
            });

            // Устанавливаем активного пассажира
            passengerRow.classList.add('active-passenger');
            const indicator = passengerRow.querySelector('.active-passenger-indicator');
            if (indicator) indicator.classList.remove('hidden');

            const btn = passengerRow.querySelector('.set-active-btn');
            if (btn) {
                btn.classList.add('active');
                btn.textContent = 'Активный ✓';
            }

            this.currentActivePassenger = passengerRow;

            // Выделяем место активного пассажира на схеме
            const placeInput = passengerRow.querySelector('.place-input');
            if (placeInput && placeInput.value) {
                this.highlightPlaceOnMap(parseInt(placeInput.value), passengerRow);
            } else {
                // Снимаем выделение со всех мест
                document.querySelectorAll('.seat-button.selected').forEach(btn => {
                    btn.classList.remove('selected');
                });
            }
        }

        onPassengerSelectChange(event) {
            const passengerRow = event.target.closest('.passenger-row');
            const passengerId = parseInt(event.target.value);

            // Проверяем, не оплачен ли пассажир
            if (this.paidPassengerIds.includes(passengerId)) {
                alert('Этот пассажир уже имеет оплаченный билет на этот рейс');
                event.target.value = '';
                return;
            }

            // Если у пассажира уже есть выбранное место, выделяем его
            const placeInput = passengerRow.querySelector('.place-input');
            if (placeInput && placeInput.value) {
                this.highlightPlaceOnMap(parseInt(placeInput.value), passengerRow);
            }

            this.calculateTotal();
        }

        selectPlace(placeNumber) {
            const seatWrapper = document.querySelector(`[data-place-number="${placeNumber}"]`);
            if (!seatWrapper || seatWrapper.dataset.isOccupied === '1') {
                alert('Это место уже занято');
                return;
            }

            // Находим активного пассажира
            const activePassenger = this.currentActivePassenger;
            if (!activePassenger) {
                alert('Сначала выберите активного пассажира (нажмите "Выбрать активным" рядом с нужным пассажиром)');
                return;
            }

            // Проверяем, что у активного пассажира выбран пассажир из списка
            const passengerSelect = activePassenger.querySelector('.passenger-select');
            if (!passengerSelect || !passengerSelect.value) {
                alert('Сначала выберите пассажира из списка');
                return;
            }

            const passengerId = parseInt(passengerSelect.value);
            if (this.paidPassengerIds.includes(passengerId)) {
                alert('Этот пассажир уже имеет оплаченный билет на этот рейс и не может выбрать место повторно');
                return;
            }

            // Проверяем, не занято ли это место другим пассажиром
            const allPlaceInputs = document.querySelectorAll('.place-input');
            for (const input of allPlaceInputs) {
                if (parseInt(input.value) === placeNumber) {
                    // Находим строку этого пассажира
                    const otherRow = input.closest('.passenger-row');
                    if (otherRow !== activePassenger) {
                        // Очищаем место у другого пассажира
                        this.clearPlace(otherRow);
                    }
                    break;
                }
            }

            // Очищаем предыдущее место, если было
            const currentPlaceInput = activePassenger.querySelector('.place-input');
            const currentPlace = currentPlaceInput ? parseInt(currentPlaceInput.value) : null;

            if (currentPlace === placeNumber) {
                this.clearPlace(activePassenger);
                return;
            }

            if (currentPlace) {
                this.selectedPlaces.delete(currentPlace);
                this.updateSeatVisualState(currentPlace, false);
            }

            // Занимаем новое место
            if (currentPlaceInput) {
                currentPlaceInput.value = placeNumber;
            }
            this.selectedPlaces.add(placeNumber);

            // Обновляем отображение
            const placeDisplay = activePassenger.querySelector('.selected-place-display');
            if (placeDisplay) {
                placeDisplay.textContent = `Место №${placeNumber}`;
                placeDisplay.classList.add('has-place');
            }

            // Показываем кнопку очистки
            const clearBtn = activePassenger.querySelector('.clear-place-btn');
            if (clearBtn) {
                clearBtn.classList.remove('hidden');
            }

            this.updateSeatVisualState(placeNumber, true, activePassenger);
            this.calculateTotal();
        }

        clearPlace(passengerRow) {
            const placeInput = passengerRow.querySelector('.place-input');
            if (!placeInput || !placeInput.value) return;

            const placeNumber = parseInt(placeInput.value);
            this.selectedPlaces.delete(placeNumber);
            placeInput.value = '';

            // Обновляем отображение
            const placeDisplay = passengerRow.querySelector('.selected-place-display');
            if (placeDisplay) {
                placeDisplay.textContent = 'Не выбрано';
                placeDisplay.classList.remove('has-place');
            }

            // Скрываем кнопку очистки
            const clearBtn = passengerRow.querySelector('.clear-place-btn');
            if (clearBtn) {
                clearBtn.classList.add('hidden');
            }

            this.updateSeatVisualState(placeNumber, false);
            this.calculateTotal();
        }

        removePassenger(passengerRow) {
            // Нельзя удалить последнего пассажира
            const passengerRows = document.querySelectorAll('.passenger-row');
            if (passengerRows.length <= 1) {
                alert('Должен остаться хотя бы один пассажир');
                return;
            }

            // Очищаем место, если было
            this.clearPlace(passengerRow);

            // Если удаляемый пассажир был активным, выбираем другого активного
            if (this.currentActivePassenger === passengerRow) {
                const otherRows = Array.from(passengerRows).filter(row => row !== passengerRow);
                if (otherRows.length > 0) {
                    this.setActivePassenger(otherRows[0]);
                }
            }

            // Удаляем строку
            passengerRow.remove();

            // Обновляем UI
            this.updateRemoveButtons();
            this.calculateTotal();
        }

        updateRemoveButtons() {
            const passengerRows = document.querySelectorAll('.passenger-row');
            const canRemove = passengerRows.length > 1;

            passengerRows.forEach(row => {
                const removeBtn = row.querySelector('.remove-passenger-btn');
                if (removeBtn) {
                    removeBtn.style.display = canRemove ? 'block' : 'none';
                }
            });
        }

        updateSeatVisualState(placeNumber, isSelected, passengerRow = null) {
            const seatButton = document.querySelector(`[data-place-number="${placeNumber}"] .seat-button`);
            if (seatButton) {
                if (isSelected) {
                    seatButton.classList.add('selected');
                    if (passengerRow) {
                        seatButton.dataset.passengerIndex = passengerRow.dataset.passengerIndex;
                    }
                } else {
                    seatButton.classList.remove('selected');
                    delete seatButton.dataset.passengerIndex;
                }
            }
        }

        highlightPlaceOnMap(placeNumber, passengerRow) {
            // Снимаем выделение со всех мест
            document.querySelectorAll('.seat-button.selected').forEach(btn => {
                btn.classList.remove('selected');
            });

            // Выделяем место
            const seatButton = document.querySelector(`[data-place-number="${placeNumber}"] .seat-button`);
            if (seatButton) {
                seatButton.classList.add('selected');
                seatButton.dataset.passengerIndex = passengerRow.dataset.passengerIndex;
            }
        }

        calculateTotal() {
            let total = 0;
            let breakdownHtml = '';
            const passengerRows = document.querySelectorAll('.passenger-row');
            let passengerCount = 0;
            let windowSeatsCount = 0;
            let petCount = 0;

            passengerRows.forEach((row, index) => {
                const passengerSelect = row.querySelector('.passenger-select');
                const placeInput = row.querySelector('.place-input');
                const petCheckbox = row.querySelector('.pet-checkbox');
                const priceDisplay = row.querySelector('.passenger-price-value');
                const priceDetails = row.querySelector('.passenger-price-details');

                if (!passengerSelect || !passengerSelect.value) {
                    if (priceDisplay) {
                        priceDisplay.textContent = '0.00';
                    }
                    if (priceDetails) {
                        priceDetails.textContent = '';
                    }
                    return;
                }

                passengerCount++;
                let price = this.basePrice;
                let details = [];

                // Базовое отображение
                if (priceDisplay) {
                    priceDisplay.textContent = this.basePrice.toFixed(2);
                }

                if (!placeInput || !placeInput.value) {
                    if (priceDetails) {
                        priceDetails.textContent = 'место не выбрано';
                    }
                    return;
                }

                const placeNumber = parseInt(placeInput.value);
                const seatWrapper = document.querySelector(`[data-place-number="${placeNumber}"]`);

                // Проверка места у окна
                if (seatWrapper && seatWrapper.dataset.isWindow === '1') {
                    price += this.windowSeatPrice;
                    windowSeatsCount++;
                    details.push('окно +' + this.windowSeatPrice + ' ₽');
                }

                // Проверка опции с животным
                if (petCheckbox && petCheckbox.checked) {
                    price += this.petPrice;
                    petCount++;
                    details.push('животное +' + this.petPrice + ' ₽');
                }

                // Учет выходного дня
                if (this.isWeekend) {
                    price *= this.weekendMultiplier;
                    details.push('выходной +15%');
                }

                price = Math.round(price * 100) / 100;

                if (priceDisplay) {
                    priceDisplay.textContent = price.toFixed(2);
                }

                if (priceDetails) {
                    priceDetails.textContent = details.join(', ');
                }

                total += price;

                // Добавляем строку в детализацию
                const passengerName = passengerSelect.options[passengerSelect.selectedIndex].text.split(' (')[0];
                breakdownHtml += `
            <div class="price-breakdown-item">
                <span class="label">Пассажир ${passengerName} (место ${placeNumber}):</span>
                <span class="value">${price.toFixed(2)} ₽</span>
            </div>
        `;
            });

            // Добавляем базовую стоимость
            if (passengerCount > 0) {
                breakdownHtml = `
            <div class="price-breakdown-item">
                <span class="label">Базовая стоимость (${passengerCount} × ${this.basePrice.toFixed(2)} ₽):</span>
                <span class="value">${(this.basePrice * passengerCount).toFixed(2)} ₽</span>
            </div>
        ` + breakdownHtml;
            }

            // Добавляем доплаты
            if (windowSeatsCount > 0) {
                breakdownHtml += `
            <div class="price-breakdown-item sub-item">
                <span class="label">Места у окна (${windowSeatsCount} × ${this.windowSeatPrice} ₽):</span>
                <span class="value text-blue-600">+${(windowSeatsCount * this.windowSeatPrice).toFixed(2)} ₽</span>
            </div>
        `;
            }

            if (petCount > 0) {
                breakdownHtml += `
            <div class="price-breakdown-item sub-item">
                <span class="label">Проезд с животным (${petCount} × ${this.petPrice} ₽):</span>
                <span class="value text-purple-600">+${(petCount * this.petPrice).toFixed(2)} ₽</span>
            </div>
        `;
            }

            // Добавляем наценку за выходной день
            if (this.isWeekend && passengerCount > 0) {
                const baseWithExtras = (this.basePrice * passengerCount) + (windowSeatsCount * this.windowSeatPrice) + (petCount * this.petPrice);
                const weekendSurcharge = baseWithExtras * (this.weekendMultiplier - 1);
                breakdownHtml += `
            <div class="price-breakdown-item sub-item">
                <span class="label">Выходной день (+15%):</span>
                <span class="value text-orange-600">+${weekendSurcharge.toFixed(2)} ₽</span>
            </div>
        `;
            }

            // Добавляем итог
            breakdownHtml += `
        <div class="price-breakdown-item total">
            <span class="label">Итого:</span>
            <span class="value text-indigo-600">${total.toFixed(2)} ₽</span>
        </div>
    `;

            // Обновляем детализацию и общую сумму
            const priceBreakdown = document.getElementById('price-breakdown');
            if (priceBreakdown) {
                priceBreakdown.innerHTML = breakdownHtml;
            }

            const totalPrice = document.getElementById('total-price');
            if (totalPrice) {
                totalPrice.textContent = total.toFixed(2) + ' ₽';
            }
        }

        validateForm(e) {
            const passengerRows = document.querySelectorAll('.passenger-row');
            let hasErrors = false;
            const errors = [];

            passengerRows.forEach((row, index) => {
                const passengerSelect = row.querySelector('.passenger-select');
                const placeInput = row.querySelector('.place-input');

                if (!passengerSelect.value) {
                    hasErrors = true;
                    errors.push(`Пассажир #${index + 1} не выбран`);
                } else {
                    const passengerId = parseInt(passengerSelect.value);
                    if (this.paidPassengerIds.includes(passengerId)) {
                        hasErrors = true;
                        const passengerName = passengerSelect.options[passengerSelect.selectedIndex].text.split(' (')[0];
                        errors.push(`Пассажир ${passengerName} уже имеет оплаченный билет на этот рейс`);
                    }
                }

                if (!placeInput.value) {
                    hasErrors = true;
                    errors.push(`Место для пассажира #${index + 1} не выбрано`);
                }
            });

            // Проверка на дубликаты мест
            const selectedPlacesArray = Array.from(this.selectedPlaces);
            if (selectedPlacesArray.length !== new Set(selectedPlacesArray).size) {
                hasErrors = true;
                errors.push('Одно и то же место выбрано для нескольких пассажиров');
            }

            if (hasErrors) {
                e.preventDefault();
                alert('Ошибки:\n' + errors.join('\n'));
                return false;
            }

            return true;
        }

        openAddPassengerModal() {
            document.getElementById('addPassengerModal').classList.remove('hidden');
        }

        closeAddPassengerModal() {
            document.getElementById('addPassengerModal').classList.add('hidden');
            const form = document.getElementById('addPassengerForm');
            if (form) {
                form.reset();
            }
        }
    }

    // Глобальные функции для кнопок
    function addPassenger() {
        if (window.orderFormManager) {
            window.orderFormManager.addPassenger();
        }
    }

    function openAddPassengerModal() {
        if (window.orderFormManager) {
            window.orderFormManager.openAddPassengerModal();
        }
    }

    function closeAddPassengerModal() {
        if (window.orderFormManager) {
            window.orderFormManager.closeAddPassengerModal();
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        window.orderFormManager = new OrderFormManager();

        // Если есть сообщение об успехе после добавления пассажира, обновляем страницу
    @php
        $successMessage = session('success');
        $shouldReload = $successMessage && (strpos($successMessage, 'Пассажир успешно добавлен') !== false);
    @endphp
    @if($shouldReload)
            setTimeout(function() {
                window.location.reload();
            }, 1500);
    @endif
    });
</script>
@endsection
