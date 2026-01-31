@extends('dashboard.admin.settings.layout')

@section('settings-content')
<h1 class="text-xl font-semibold mb-6">Edit country</h1>

<form method="POST" action="{{ route('admin.settings.countries.update', $country) }}" class="space-y-4 max-w-md">
    @csrf
    @method('PUT')

    <!-- Код страны -->
    <div>
        <label class="block text-sm font-medium mb-1">Code</label>
        <input type="text" name="code" value="{{ old('code', $country->code) }}"
               class="w-full border rounded px-3 py-2 text-sm">
        @error('code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Название страны -->
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $country->name) }}"
               class="w-full border rounded px-3 py-2 text-sm">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Активность -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" {{ $country->is_active ? 'checked' : '' }}>
        <span class="text-sm">Active</span>
    </div>

    <!-- Приоритетная страна -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_priority" value="1" {{ $country->is_priority ? 'checked' : '' }}>
        <span class="text-sm">Priority country (top of selector)</span>
    </div>

    <!-- Страна по умолчанию -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1" {{ $country->is_default ? 'checked' : '' }}>
        <span class="text-sm">Default country for new users</span>
    </div>

    <!-- Порядок сортировки -->
    <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $country->sort_order) }}"
               class="w-full border rounded px-3 py-2 text-sm">
        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first in lists</p>
    </div>

    <!-- Кнопки -->
    <div class="flex gap-3">
        <button class="px-4 py-2 bg-gray-900 text-white rounded text-sm">
            Update
        </button>
        <a href="{{ route('admin.settings.countries.index') }}"
           class="px-4 py-2 bg-gray-100 rounded text-sm">
            Cancel
        </a>
    </div>
</form>
@endsection
