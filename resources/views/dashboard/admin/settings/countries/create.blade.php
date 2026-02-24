@extends('dashboard.admin.settings.layout')

@section('settings-content')

<h1 class="text-xl font-semibold mb-6">Add country</h1>

@php
use App\Models\Language;

$languages = Language::where('is_active', true)
    ->orderBy('sort_order')
    ->get();
@endphp

<form method="POST" action="{{ route('admin.settings.countries.store') }}"
      class="space-y-6 max-w-md">

    @csrf

    <!-- Code -->
    <div>
        <label class="block text-sm font-medium mb-1">Code</label>
        <input type="text"
               name="code"
               value="{{ old('code') }}"
               class="w-full border rounded px-3 py-2 text-sm">

        @error('code')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Translations -->
    <div class="border rounded-lg p-4 space-y-4">

        <h3 class="text-sm font-medium text-gray-700">
            Translations
        </h3>

        @foreach($languages as $lang)

            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Name ({{ strtoupper($lang->code) }})
                </label>

                <input type="text"
                       name="translations[{{ $lang->code }}]"
                       value="{{ old('translations.' . $lang->code) }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

        @endforeach

    </div>

    <!-- Active -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" checked>
        <span class="text-sm">Active</span>
    </div>

    <!-- Priority -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_priority" value="1">
        <span class="text-sm">Priority country (top of selector)</span>
    </div>

    <!-- Default -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1">
        <span class="text-sm">Default country for new users</span>
    </div>

    <!-- Sort order -->
    <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>

        <input type="number"
               name="sort_order"
               value="{{ old('sort_order') }}"
               class="w-full border rounded px-3 py-2 text-sm">

        <p class="text-xs text-gray-500 mt-1">
            Lower numbers appear first in lists
        </p>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-2">
        <button class="px-4 py-2 bg-gray-900 text-white rounded text-sm">
            Save
        </button>

        <a href="{{ route('admin.settings.countries.index') }}"
           class="px-4 py-2 bg-gray-100 rounded text-sm">
            Cancel
        </a>
    </div>

</form>

@endsection