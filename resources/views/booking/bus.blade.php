<h2>Выбор места: маршрут {{ $route->name }}</h2>

<form action="" method="POST">
    @csrf

    <div class="bus-seats">
        @foreach($seats as $seat)
            <label class="seat 
                {{ $seat->is_window ? 'window-seat' : '' }}
                {{ $seat->isBooked() ? 'booked' : '' }}"
            >
                @if(!$seat->isBooked())
                    <input type="radio" name="seat_id" value="{{ $seat->id }}">
                @endif
                {{ $seat->number }}
            </label>
        @endforeach
    </div>

    <label>
        <input type="checkbox" name="with_pet">
        Путешествую с животным (+300 ₽)
    </label>

    <button type="submit">Забронировать</button>
</form>

<style>
.bus-seats {
    display: grid;
    grid-template-columns: repeat(4, 60px);
    gap: 10px;
}
.seat {
    padding: 12px;
    border: 1px solid #333;
    text-align: center;
    cursor: pointer;
}
.window-seat { background: #e6f7ff; }
.booked { background: #ccc; pointer-events: none; }
</style>
