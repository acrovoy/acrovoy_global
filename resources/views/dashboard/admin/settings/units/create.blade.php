@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-5xl">


<x-alerts />

{{-- ============================================================
    HEADER
============================================================= --}}

<div class="flex items-center justify-between">

    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Add Unit
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Create a measurement unit and define its translations.
        </p>
    </div>

    <a
        href="{{ route('admin.settings.units.index') }}"
        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
    >
        ← Units
    </a>

</div>


@php
    use App\Models\Language;

    $languages = Language::where('is_active', true)
        ->orderBy('sort_order')
        ->get();
@endphp


{{-- ============================================================
    FORM
============================================================= --}}

<form
    method="POST"
    action="{{ route('admin.settings.units.store') }}"
    class="space-y-6"
>

    @csrf


    {{-- ========================================================
        TRANSLATIONS
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

            <h3 class="font-semibold text-gray-800 text-sm">
                Translations
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Define the unit name for each active language.
            </p>

        </div>


        <div class="p-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                @foreach($languages as $language)

                    <div>

                        <label
                            for="translation-{{ $language->code }}"
                            class="block text-sm text-gray-600 mb-1"
                        >
                            Name
                            <span class="text-gray-400">
                                ({{ strtoupper($language->code) }})
                            </span>
                        </label>

                        <input
                            id="translation-{{ $language->code }}"
                            type="text"
                            name="translations[{{ $language->code }}]"
                            value="{{ old('translations.' . $language->code) }}"
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                            required
                        >

                        @error('translations.' . $language->code)
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    {{-- ========================================================
        BASIC INFORMATION
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

            <h3 class="font-semibold text-gray-800 text-sm">
                Unit Information
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                System values used to identify and display the unit.
            </p>

        </div>


        <div class="p-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- CODE --}}

                <div>

                    <label
                        for="code"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Code
                    </label>

                    <input
                        id="code"
                        type="text"
                        name="code"
                        value="{{ old('code') }}"
                        placeholder="kg"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        required
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        Unique system identifier, for example: kg, cm, m.
                    </p>

                    @error('code')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- SYMBOL --}}

                <div>

                    <label
                        for="symbol"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Symbol
                    </label>

                    <input
                        id="symbol"
                        type="text"
                        name="symbol"
                        value="{{ old('symbol') }}"
                        placeholder="kg"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        required
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        Symbol displayed next to values.
                    </p>

                    @error('symbol')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- GROUP --}}

                <div>

                    <label
                        for="unit_group"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Unit Group
                    </label>

                    <input
                        id="unit_group"
                        type="text"
                        name="unit_group"
                        value="{{ old('unit_group') }}"
                        placeholder="weight"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        required
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        For example: weight, length, area, volume.
                    </p>

                    @error('unit_group')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- SORT ORDER --}}

                <div>

                    <label
                        for="sort_order"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Sort Order
                    </label>

                    <input
                        id="sort_order"
                        type="number"
                        name="sort_order"
                        min="0"
                        value="{{ old('sort_order', 0) }}"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        Lower values appear first.
                    </p>

                    @error('sort_order')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================
        CONVERSION
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

            <h3 class="font-semibold text-gray-800 text-sm">
                Conversion
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Define how this unit relates to the base unit of its group.
            </p>

        </div>


        <div class="p-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- CONVERSION FACTOR --}}

                <div>

                    <label
                        for="conversion_factor"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Conversion Factor
                    </label>

                    <input
                        id="conversion_factor"
                        type="number"
                        name="conversion_factor"
                        step="0.000000000001"
                        min="0"
                        value="{{ old('conversion_factor', 1) }}"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        required
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        Example: 1 meter = 100 centimeters.
                    </p>

                    @error('conversion_factor')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- CONVERSION OFFSET --}}

                <div>

                    <label
                        for="conversion_offset"
                        class="block text-sm text-gray-600 mb-1"
                    >
                        Conversion Offset
                    </label>

                    <input
                        id="conversion_offset"
                        type="number"
                        name="conversion_offset"
                        step="0.000000000001"
                        value="{{ old('conversion_offset', 0) }}"
                        class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                    >

                    <p class="text-xs text-gray-400 mt-1">
                        Usually 0. Used for temperature conversions.
                    </p>

                    @error('conversion_offset')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================
        SETTINGS
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

            <h3 class="font-semibold text-gray-800 text-sm">
                Settings
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Control how this unit is used across the platform.
            </p>

        </div>


        <div class="p-5 space-y-4">

            {{-- BASE UNIT --}}

            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="checkbox"
                    name="is_base"
                    value="1"
                    @checked(old('is_base'))
                    class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                >

                <div>

                    <div class="text-sm font-medium text-gray-700">
                        Base unit
                    </div>

                    <div class="text-xs text-gray-400">
                        Mark this as the base unit for its unit group.
                    </div>

                </div>

            </label>


            {{-- ACTIVE --}}

            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', true))
                    class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                >

                <div>

                    <div class="text-sm font-medium text-gray-700">
                        Active
                    </div>

                    <div class="text-xs text-gray-400">
                        Allow this unit to be used across the platform.
                    </div>

                </div>

            </label>

        </div>

    </div>


    {{-- ========================================================
        ACTIONS
    ========================================================= --}}

    <div class="flex justify-end gap-3 pt-2">

        <a
            href="{{ route('admin.settings.units.index') }}"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="px-5 py-2 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition shadow-sm"
        >
            Create Unit
        </button>

    </div>

</form>


</div>

@endsection
