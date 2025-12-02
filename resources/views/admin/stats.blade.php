<h2>Статистика продаж по маршрутам</h2>

<table border="1" cellpadding="6">
    <tr>
        <th>Маршрут</th>
        <th>Количество проданных билетов</th>
    </tr>

    @foreach($stats as $row)
    <tr>
        <td>{{ $routes[$row->route_id] ?? 'Не найдено' }}</td>
        <td>{{ $row->total }}</td>
    </tr>
    @endforeach
</table>
