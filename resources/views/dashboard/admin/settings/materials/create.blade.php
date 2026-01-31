@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Добавить материал</h1>
</div>

<form action="{{ route('admin.settings.materials.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
    @csrf

    {{-- Slug --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">Slug</label>
        <input type="text" name="slug" value="{{ old('slug') }}" 
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
        @error('slug')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Названия на разных языках --}}
    @php
        $locales = ['en' => 'English', 'uk' => 'Українська', 'es' => 'Español'];
    @endphp

    @foreach($locales as $locale => $label)
    <div>
        <label class="block mb-1 font-medium text-gray-700">Название ({{ $label }})</label>
        <input type="text" name="name[{{ $locale }}]" value="{{ old("name.$locale") }}"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
        @error("name.$locale")
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    @endforeach

    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">
        Сохранить
    </button>
</form>
@endsection
