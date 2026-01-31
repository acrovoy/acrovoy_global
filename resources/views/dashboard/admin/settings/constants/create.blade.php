@extends('dashboard.admin.settings.layout')

@section('settings-content')
<h1 class="text-2xl font-bold mb-4">Добавить константу</h1>

<form action="{{ route('admin.settings.constants.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block mb-1">Название</label>
        <input type="text" name="name" class="border rounded px-2 py-1 w-full" required>
    </div>
    <div>
        <label class="block mb-1">Значение</label>
        <input type="text" name="value" class="border rounded px-2 py-1 w-full" required>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Сохранить</button>
</form>
@endsection
