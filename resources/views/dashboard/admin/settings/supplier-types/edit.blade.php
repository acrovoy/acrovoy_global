@extends('dashboard.admin.settings.layout')

@section('settings-content')

<h1 class="text-xl font-semibold mb-6">Edit Supplier Type</h1>

<form method="POST"
      action="{{ route('admin.settings.supplier-types.update', $supplierType->id) }}"
      class="space-y-2 bg-gray-50 border rounded-xl shadow-sm p-6">

    @csrf
    @method('PUT')

    {{-- SLUG --}}
    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Slug</label>

        <input type="text"
               name="slug"
               class="w-full border rounded px-3 py-2 text-sm"
               value="{{ old('slug', $supplierType->slug) }}">

        @error('slug')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- TRANSLATIONS --}}
    <div x-data="{ open: false }"
         class="border rounded-lg p-4 mb-6 bg-white">

        <h4 class="font-semibold mb-4">Supplier Type Translations</h4>

        @foreach($languages as $index => $language)

            @php
                $translation = $supplierType->translations
                    ->firstWhere('locale', $language->code);

                $name = old(
                    "translations.$language->code.name",
                    $translation->name ?? ''
                );
            @endphp

            @if($index === 0)

                {{-- MAIN LANGUAGE --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>

                    <input type="text"
                           name="translations[{{ $language->code }}][name]"
                           class="w-full border rounded px-3 py-2 text-sm"
                           value="{{ $name }}"
                           placeholder="Supplier Type Name">
                </div>

            @else

                {{-- OTHER LANGUAGES --}}
                <div x-show="open" x-collapse class="mb-4">

                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>

                    <input type="text"
                           name="translations[{{ $language->code }}][name]"
                           class="w-full border rounded px-3 py-2 text-sm"
                           value="{{ $name }}"
                           placeholder="Supplier Type Name">
                </div>

            @endif

        @endforeach

        @if(count($languages) > 1)
            <button type="button"
                    @click="open = !open"
                    class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">

                Other Languages

                <svg :class="{ 'rotate-180': open }"
                     class="w-4 h-4 transition-transform"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        @endif

    </div>

    {{-- BUTTONS --}}
    <div class="flex gap-3 mt-6">

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded text-sm hover:bg-gray-800 transition">

            Update Supplier Type
        </button>

        <a href="{{ route('admin.settings.supplier-types.index') }}"
           class="px-4 py-2 bg-gray-100 rounded text-sm">

            Cancel
        </a>

    </div>

</form>

@endsection