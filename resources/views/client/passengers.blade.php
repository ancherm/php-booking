@extends('layouts.main')

@section('title', 'Мои пассажиры')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-6">Мои пассажиры</h1>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Добавить пассажира</h2>
    <form method="POST" action="{{ route('client.passengers.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
            <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Фамилия</label>
            <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Паспорт</label>
            <input type="text" name="passport" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div class="md:col-span-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Добавить
            </button>
        </div>
    </form>
</div>

@if($passengers->count() > 0)
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ФИО</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Паспорт</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($passengers as $passenger)
                    <tr>
                        <td class="px-6 py-4">{{ $passenger->full_name }}</td>
                        <td class="px-6 py-4">{{ $passenger->passport }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('client.passengers.destroy', $passenger) }}" onsubmit="return confirm('Удалить пассажира?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="bg-white shadow rounded-lg p-6 text-center">
        <p class="text-gray-500">У вас пока нет добавленных пассажиров</p>
    </div>
@endif
@endsection

