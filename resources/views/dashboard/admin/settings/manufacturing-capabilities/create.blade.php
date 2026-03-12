@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">
        Add Manufacturing Capability
    </h1>
</div>

<x-alerts />

<form action="{{ route('admin.settings.manufacturing-capabilities.store') }}"
      method="POST"
      class="space-y-4 bg-white p-6 rounded shadow">

    @csrf

    <div>
        <label class="block mb-1 font-medium text-gray-700">Slug</label>

        <input type="text"
               name="slug"
               value="{{ old('slug') }}"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300"
               required>

        @error('slug')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">Icon</label>

        <input type="text"
               name="icon"
               placeholder="fa-solid fa-gear"
               value="{{ old('icon') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">Sort Order</label>

        <input type="number"
               name="sort_order"
               value="{{ old('sort_order', 0) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox"
               name="visibility_flag"
               value="1"
               checked>

        <label class="text-sm text-gray-700">
            Visible
        </label>
    </div>

   

    @foreach($languages as $language)

        <div>
            <label class="block mb-1 font-medium text-gray-700">
                Name ({{ $language->code }})
            </label>

            <input type="text"
                   name="name[{{ $language->code }}]"
                   value="{{ old("name.$language->code") }}"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>

    @endforeach

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">

        Save
    </button>

</form>

@endsection