<h2>Оплата заказа №{{ $order->id }}</h2>

<p>Стоимость: {{ $order->price }} ₽</p>

@if(session('error'))
    <p style="color:red;">{{ session('error') }}</p>
@endif

<form method="POST">
    @csrf
    <button type="submit">Оплатить</button>
</form>
