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

<form method="POST" action="{{ route('client.orders.store') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Выбор пассажиров</h2>

        @if($passengers->count() == 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
            <p>У вас нет добавленных пассажиров. <a href="{{ route('client.passengers.index') }}" class="text-indigo-600 underline">Добавить пассажира</a></p>
        </div>
        @endif

        <div id="passenger-container" class="space-y-4 mb-6">
            <div class="passenger-row border border-gray-200 rounded-lg p-4" data-passenger-index="0">
                <div class="flex gap-4 mb-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Пассажир</label>
                        <select name="passengers[0][passenger_id]" required class="passenger-select w-full px-3 py-2 border border-gray-300 rounded-md" onchange="onPassengerChange(0)">
                            <option value="">Выберите пассажира</option>
                            @foreach($passengers as $passenger)
                            <option value="{{ $passenger->id }}">{{ $passenger->full_name }} ({{ $passenger->passport }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Выбранное место</label>
                        <div class="flex gap-2">
                            <div class="selected-place-display flex-1 px-3 py-2 border-2 border-gray-300 rounded-md bg-gray-50 text-center text-gray-500" id="selected-place-0">
                                Не выбрано
                            </div>
                            <button type="button" onclick="clearPlace(0)" class="clear-place-btn px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-sm hidden" id="clear-place-0">
                                ✕
                            </button>
                        </div>
                        <input type="hidden" name="passengers[0][place_number]" class="place-input" value="">
                    </div>
                </div>
                <div class="flex items-center mb-2">
                    <input type="checkbox" name="passengers[0][with_pet]" id="passenger_0_with_pet" value="1" class="pet-checkbox mr-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" onchange="calculateTotal()">
                    <label for="passenger_0_with_pet" class="text-sm text-gray-700">Проезд с животным (+300 ₽)</label>
                </div>
                <div class="text-sm text-gray-600">
                    <span class="passenger-price">Цена: <span class="font-semibold passenger-price-value">{{ number_format($trip->route->price, 2) }}</span> ₽</span>
                    <span class="ml-4 passenger-price-details text-xs text-gray-500"></span>
                </div>
            </div>
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
                $seatsPerRow = 4; // 2+2 конфигурация
                $totalRows = ceil($totalPlaces / $seatsPerRow);

                // Распределение мест: в каждом ряду 4 места
                // Левая сторона (окна): места 1, 2, 5, 6, 9, 10... (нечетные ряды: 1,2; четные ряды: 5,6...)
                // Правая сторона (проход): места 3, 4, 7, 8, 11, 12...
                $leftSidePlaces = [];
                $rightSidePlaces = [];

                for ($i = 1; $i <= $totalPlaces; $i++) {
                $row = ceil($i / $seatsPerRow);
                $positionInRow = (($i - 1) % $seatsPerRow) + 1;

                // В каждом ряду: позиции 1,2 - левая сторона, позиции 3,4 - правая сторона
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
                    $isWindow = ($positionInRow == 1); // Позиция 1 в ряду - левый край (окно)
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
                    $isWindow = ($positionInRow == $seatsPerRow); // Позиция 4 в ряду - правый край (окно)
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
    let passengerIndex = 1;
    let currentSelectedPassengerIndex = null;
    let selectedPlaces = new Set();
    const basePrice = {{ $trip->route->price }};
    const isWeekend = {{ ($trip->date->dayOfWeek == 0 || $trip->date->dayOfWeek == 6) ? 'true' : 'false' }};
    const weekendMultiplier = 1.15;
    const windowSeatPrice = 200;
    const petPrice = 300;

    // Инициализация обработчиков кликов по местам
    document.addEventListener('DOMContentLoaded', function() {
        const seatButtons = document.querySelectorAll('.seat-button.available');
        seatButtons.forEach(button => {
            button.addEventListener('click', function() {
                const placeNumber = parseInt(this.dataset.placeNumber);
                selectPlaceForPassenger(placeNumber);
            });
        });

        // Инициализация расчета при загрузке страницы
        calculateTotal();
    });

    function onPassengerChange(index) {
        const select = document.querySelector(`[data-passenger-index="${index}"] .passenger-select`);
        if (select && select.value) {
            currentSelectedPassengerIndex = index;
            // Если у этого пассажира уже выбрано место, выделяем его на схеме
            const placeInput = document.querySelector(`[data-passenger-index="${index}"] .place-input`);
            if (placeInput && placeInput.value) {
                highlightPlaceOnMap(placeInput.value, index);
            }
        } else {
            currentSelectedPassengerIndex = null;
        }
        calculateTotal();
    }

    function highlightPlaceOnMap(placeNumber, passengerIndex) {
        // Убираем выделение со всех мест
        document.querySelectorAll('.seat-button.selected').forEach(btn => {
            btn.classList.remove('selected');
        });

        // Выделяем место этого пассажира
        const seatButton = document.querySelector(`[data-place-number="${placeNumber}"] .seat-button`);
        if (seatButton) {
            seatButton.classList.add('selected');
            seatButton.setAttribute('data-passenger-index', passengerIndex);
        }
    }

    function selectPlaceForPassenger(placeNumber) {
        const seatWrapper = document.querySelector(`[data-place-number="${placeNumber}"]`);
        if (seatWrapper && seatWrapper.dataset.isOccupied === '1') {
            alert('Это место уже занято');
            return;
        }

        let targetPassengerIndex = currentSelectedPassengerIndex;

        if (targetPassengerIndex === null) {
            const passengerRows = document.querySelectorAll('.passenger-row');
            for (let i = 0; i < passengerRows.length; i++) {
                const passengerSelect = passengerRows[i].querySelector('.passenger-select');
                const placeInput = passengerRows[i].querySelector('.place-input');
                if (passengerSelect && passengerSelect.value && (!placeInput || !placeInput.value)) {
                    targetPassengerIndex = i;
                    break;
                }
            }
        }

        if (targetPassengerIndex === null) {
            alert('Сначала выберите пассажира');
            return;
        }

        const passengerSelect = document.querySelector(`[data-passenger-index="${targetPassengerIndex}"] .passenger-select`);
        if (!passengerSelect || !passengerSelect.value) {
            alert('Сначала выберите пассажира');
            return;
        }

        const placeInput = document.querySelector(`[data-passenger-index="${targetPassengerIndex}"] .place-input`);
        const currentPlace = placeInput ? parseInt(placeInput.value) : null;

        if (currentPlace === placeNumber) {
            clearPlace(targetPassengerIndex);
            return;
        }

        // Проверяем, не выбрано ли это место другим пассажиром
        const passengerRows = document.querySelectorAll('.passenger-row');
        for (let i = 0; i < passengerRows.length; i++) {
            if (i === targetPassengerIndex) continue;
            const otherPlaceInput = passengerRows[i].querySelector('.place-input');
            if (otherPlaceInput && parseInt(otherPlaceInput.value) === placeNumber) {
                clearPlace(i);
                break;
            }
        }

        // Освобождаем предыдущее место, если было
        if (currentPlace) {
            selectedPlaces.delete(currentPlace);
            updateSeatVisualState(currentPlace, false, null);
        }

        // Занимаем новое место
        placeInput.value = placeNumber;
        selectedPlaces.add(placeNumber);

        // Обновляем отображение
        const placeDisplay = document.getElementById(`selected-place-${targetPassengerIndex}`);
        placeDisplay.textContent = `Место №${placeNumber}`;
        placeDisplay.classList.add('has-place');

        const clearBtn = document.getElementById(`clear-place-${targetPassengerIndex}`);
        if (clearBtn) {
            clearBtn.classList.remove('hidden');
        }

        updateSeatVisualState(placeNumber, true, targetPassengerIndex);
        calculateTotal();
    }

    function updateSeatVisualState(placeNumber, isSelected, passengerIndex) {
        const seatButton = document.querySelector(`[data-place-number="${placeNumber}"] .seat-button`);
        if (seatButton) {
            if (isSelected) {
                seatButton.classList.add('selected');
                if (passengerIndex !== null) {
                    seatButton.setAttribute('data-passenger-index', passengerIndex);
                }
            } else {
                seatButton.classList.remove('selected');
                seatButton.removeAttribute('data-passenger-index');
            }
        }
    }

    function clearPlace(passengerIndex) {
        const placeInput = document.querySelector(`[data-passenger-index="${passengerIndex}"] .place-input`);
        if (!placeInput || !placeInput.value) {
            return;
        }

        const placeNumber = parseInt(placeInput.value);
        selectedPlaces.delete(placeNumber);
        placeInput.value = '';

        // Обновляем отображение
        const placeDisplay = document.getElementById(`selected-place-${passengerIndex}`);
        if (placeDisplay) {
            placeDisplay.textContent = 'Не выбрано';
            placeDisplay.classList.remove('has-place');
        }

        // Скрываем кнопку очистки
        const clearBtn = document.getElementById(`clear-place-${passengerIndex}`);
        if (clearBtn) {
            clearBtn.classList.add('hidden');
        }

        updateSeatVisualState(placeNumber, false, null);
        calculateTotal();
    }

    function addPassenger() {
        const container = document.getElementById('passenger-container');
        const newRow = document.querySelector('.passenger-row').cloneNode(true);
        newRow.setAttribute('data-passenger-index', passengerIndex);

        newRow.querySelectorAll('select, input, label').forEach(element => {
            if (element.name) {
                element.name = element.name.replace('[0]', `[${passengerIndex}]`);
            }
            if (element.id) {
                element.id = element.id.replace('_0_', `_${passengerIndex}_`);
            }
            if (element.htmlFor) {
                element.htmlFor = element.htmlFor.replace('_0_', `_${passengerIndex}_`);
            }
            if (element.classList.contains('passenger-select')) {
                element.onchange = () => onPassengerChange(passengerIndex);
                element.value = '';
            }
            if (element.classList.contains('place-input')) {
                element.value = '';
            }
            if (element.classList.contains('pet-checkbox')) {
                element.onchange = calculateTotal;
                element.checked = false;
            }
        });

        // Обновляем отображение места
        const placeDisplay = newRow.querySelector('.selected-place-display');
        placeDisplay.id = `selected-place-${passengerIndex}`;
        placeDisplay.textContent = 'Не выбрано';
        placeDisplay.classList.remove('has-place');

        // Обновляем кнопку очистки
        const clearBtn = newRow.querySelector('.clear-place-btn');
        if (clearBtn) {
            clearBtn.id = `clear-place-${passengerIndex}`;
            clearBtn.onclick = () => clearPlace(passengerIndex);
            clearBtn.classList.add('hidden');
        }

        // Обновляем отображение цены
        const priceValue = newRow.querySelector('.passenger-price-value');
        if (priceValue) {
            priceValue.textContent = basePrice.toFixed(2);
        }

        const priceDetails = newRow.querySelector('.passenger-price-details');
        if (priceDetails) {
            priceDetails.textContent = '';
        }

        container.appendChild(newRow);
        passengerIndex++;
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        let breakdownHtml = '';
        const rows = document.querySelectorAll('.passenger-row');
        let passengerCount = 0;
        let windowSeatsCount = 0;
        let petCount = 0;

        rows.forEach((row, index) => {
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
            let price = basePrice;
            let details = [];

            // Базовое отображение
            if (priceDisplay) {
                priceDisplay.textContent = basePrice.toFixed(2);
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
                price += windowSeatPrice;
                windowSeatsCount++;
                details.push('окно +' + windowSeatPrice + ' ₽');
            }

            // Проверка опции с животным
            if (petCheckbox && petCheckbox.checked) {
                price += petPrice;
                petCount++;
                details.push('животное +' + petPrice + ' ₽');
            }

            // Учет выходного дня
            if (isWeekend) {
                price *= weekendMultiplier;
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
                <span class="label">Базовая стоимость (${passengerCount} × ${basePrice.toFixed(2)} ₽):</span>
                <span class="value">${(basePrice * passengerCount).toFixed(2)} ₽</span>
            </div>
        ` + breakdownHtml;
        }

        // Добавляем доплаты
        if (windowSeatsCount > 0) {
            breakdownHtml += `
            <div class="price-breakdown-item sub-item">
                <span class="label">Места у окна (${windowSeatsCount} × ${windowSeatPrice} ₽):</span>
                <span class="value text-blue-600">+${(windowSeatsCount * windowSeatPrice).toFixed(2)} ₽</span>
            </div>
        `;
        }

        if (petCount > 0) {
            breakdownHtml += `
            <div class="price-breakdown-item sub-item">
                <span class="label">Проезд с животным (${petCount} × ${petPrice} ₽):</span>
                <span class="value text-purple-600">+${(petCount * petPrice).toFixed(2)} ₽</span>
            </div>
        `;
        }

        // Добавляем наценку за выходной день
        if (isWeekend && passengerCount > 0) {
            const baseWithExtras = (basePrice * passengerCount) + (windowSeatsCount * windowSeatPrice) + (petCount * petPrice);
            const weekendSurcharge = baseWithExtras * (weekendMultiplier - 1);
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
        document.getElementById('price-breakdown').innerHTML = breakdownHtml;
        document.getElementById('total-price').textContent = total.toFixed(2) + ' ₽';
    }

    // Валидация формы перед отправкой
    document.querySelector('form').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.passenger-row');
        let hasErrors = false;
        const errors = [];

        rows.forEach((row, index) => {
            const passengerSelect = row.querySelector('.passenger-select');
            const placeInput = row.querySelector('.place-input');

            if (!passengerSelect.value) {
                hasErrors = true;
                errors.push(`Пассажир #${index + 1} не выбран`);
            }

            if (!placeInput.value) {
                hasErrors = true;
                errors.push(`Место для пассажира #${index + 1} не выбрано`);
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Ошибки:\n' + errors.join('\n'));
            return false;
        }

        // Проверка на дубликаты мест
        const selectedPlacesArray = Array.from(selectedPlaces);
        if (selectedPlacesArray.length !== new Set(selectedPlacesArray).size) {
            e.preventDefault();
            alert('Ошибка: одно и то же место выбрано для нескольких пассажиров');
            return false;
        }

        return true;
    });
</script>
@endsection
