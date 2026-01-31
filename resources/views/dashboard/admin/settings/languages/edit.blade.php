@extends('dashboard.admin.settings.layout')

@section('settings-content')
<h1 class="text-xl font-semibold mb-6">
    Edit language: {{ $language->code }}
</h1>

<form method="POST"
      action="{{ route('admin.settings.languages.update', $language) }}"
      class="max-w-md space-y-5">
    @csrf
    @method('PUT')

    {{-- Code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Language code
        </label>
        <input type="text"
               name="code"
               value="{{ old('code', $language->code) }}"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
        @error('code')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Language name
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $language->name) }}"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
        @error('name')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Native Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Native name
        </label>
        <input type="text"
               name="native_name"
               value="{{ old('native_name', $language->native_name) }}"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
        @error('native_name')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Locale --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Locale
        </label>
        <input type="text"
               name="locale"
               value="{{ old('locale', $language->locale) }}"
               placeholder="en-US, ru-RU"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
        @error('locale')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Direction --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Text direction
        </label>
        <select name="direction"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
            <option value="ltr" {{ old('direction', $language->direction) === 'ltr' ? 'selected' : '' }}>Left-to-right</option>
            <option value="rtl" {{ old('direction', $language->direction) === 'rtl' ? 'selected' : '' }}>Right-to-left</option>
        </select>
        @error('direction')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Priority --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Priority
        </label>
        <select name="priority"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
            @foreach(['core','high','medium','low'] as $priority)
                <option value="{{ $priority }}" {{ old('priority', $language->priority) === $priority ? 'selected' : '' }}>
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sort Order --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Sort order
        </label>
        <input type="number"
               name="sort_order"
               value="{{ old('sort_order', $language->sort_order) }}"
               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">
        @error('sort_order')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Default --}}
    <div class="flex items-center gap-2">
        <input type="checkbox"
               name="is_default"
               value="1"
               {{ old('is_default', $language->is_default) ? 'checked' : '' }}
               class="rounded border-gray-300">
        <span class="text-sm text-gray-700">Default language</span>
    </div>

    {{-- Active --}}
    <div class="flex items-center gap-2">
        <input type="checkbox"
               name="is_active"
               value="1"
               {{ old('is_active', $language->is_active) ? 'checked' : '' }}
               class="rounded border-gray-300">
        <span class="text-sm text-gray-700">Active</span>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Notes
        </label>
        <textarea name="notes"
                  rows="3"
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-gray-200">{{ old('notes', $language->notes) }}</textarea>
        @error('notes')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-4">
        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800">
            Update
        </button>

        <a href="{{ route('admin.settings.languages.index') }}"
           class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
            Cancel
        </a>
    </div>
</form>
@endsection
