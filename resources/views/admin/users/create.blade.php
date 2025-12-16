@extends('layouts.main')

@section('title', 'Создание пользователя')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Создание пользователя</h1>
</div>

<form method="POST" action="{{ route('admin.users.store') }}" class="bg-white shadow rounded-lg p-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('first_name') border-red-500 @enderror">
            @error('first_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Фамилия *</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('last_name') border-red-500 @enderror">
            @error('last_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Отчество</label>
            <input type="text" name="patronymic" value="{{ old('patronymic') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('patronymic') border-red-500 @enderror">
            @error('patronymic')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Логин *</label>
            <input type="text" name="login" value="{{ old('login') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('login') border-red-500 @enderror">
            @error('login')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Пароль *</label>
            <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Тип пользователя *</label>
            <select name="user_type" id="user_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('user_type') border-red-500 @enderror" onchange="toggleFields()">
                <option value="">Выберите тип</option>
                <option value="client" {{ old('user_type') == 'client' ? 'selected' : '' }}>Клиент</option>
                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Администратор</option>
                <option value="manager" {{ old('user_type') == 'manager' ? 'selected' : '' }}>Менеджер</option>
            </select>
            @error('user_type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div id="client_fields" style="display: none;">
        <h3 class="text-lg font-semibold mb-4">Данные клиента</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+79991234567" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div id="admin_fields" style="display: none;">
        <h3 class="text-lg font-semibold mb-4">Данные администратора</h3>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Должность *</label>
            <input type="text" name="position" value="{{ old('position') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('position') border-red-500 @enderror">
            @error('position')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between">
        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Отмена
        </a>
        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">
            Создать пользователя
        </button>
    </div>
</form>

<script>
function toggleFields() {
    const userType = document.getElementById('user_type').value;
    const clientFields = document.getElementById('client_fields');
    const adminFields = document.getElementById('admin_fields');

    if (userType === 'client') {
        clientFields.style.display = 'block';
        adminFields.style.display = 'none';
    } else if (userType === 'admin') {
        clientFields.style.display = 'none';
        adminFields.style.display = 'block';
    } else {
        clientFields.style.display = 'none';
        adminFields.style.display = 'none';
    }
}

if (document.getElementById('user_type').value) {
    toggleFields();
}
</script>
@endsection

