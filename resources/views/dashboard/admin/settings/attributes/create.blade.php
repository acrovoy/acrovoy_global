@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col max-w-3xl">

<x-alerts />

{{-- Header --}}

<div class="flex items-start justify-between mb-6">

    <div>

        <h1 class="text-xl font-semibold text-gray-900">
            Add Attribute
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Create a new product attribute
        </p>

    </div>

</div>




<div class="bg-white border border-gray-200 rounded-xl shadow-sm">

<form action="{{ route('admin.settings.attributes.store') }}"
method="POST"
class="p-6 flex flex-col gap-6">

@csrf


@php

use App\Models\Language;

$languages = Language::where('is_active', true)
->orderBy('sort_order')
->get();

@endphp

{{-- Entity Type --}}

<div>

    <label
        for="entity_type"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Entity Type
    </label>

    <select
        name="entity_type"
        id="entity_type"
        class="
            mt-1.5
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            text-gray-900
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
    >

        <option value="">
            Select entity type
        </option>

        <option value="product">
            Product
        </option>

        <option value="rfq">
            RFQ
        </option>

        <option value="offer">
            Offer
        </option>

        <option value="contract">
            Contract
        </option>

        <option value="company">
            Company
        </option>

        <option value="user">
            User
        </option>

    </select>

    @error('entity_type')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>


{{-- Context --}}

<div>

    <label class="block font-medium mb-1">
        Context
    </label>

    <input
        type="text"
        name="context"
        value="{{ old('context', $attribute->context ?? '') }}"
        class="w-full border border-gray-300 rounded px-3 py-2"
        required
    >

</div>



{{-- Translations --}}

{{-- ============================================================
     ATTRIBUTE TRANSLATIONS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Translations
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Enter the attribute name for each available language.
        </p>

    </div>


    {{-- TRANSLATIONS --}}
    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            @foreach($languages as $lang)

                <div>

                    <label
                        for="translation-{{ $lang->code }}"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Name
                        <span class="text-gray-400 font-medium">
                            ({{ strtoupper($lang->code) }})
                        </span>
                    </label>


                    <input
                        type="text"
                        name="translations[{{ $lang->code }}]"
                        id="translation-{{ $lang->code }}"
                        value="{{ old('translations.' . $lang->code) }}"
                        class="
                            mt-1.5
                            w-full
                            h-10
                            px-3
                            rounded-lg
                            border border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            placeholder:text-gray-400
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                        required
                    >

                    @error('translations.' . $lang->code)
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            @endforeach

        </div>

    </div>

</div>



{{-- ============================================================
     ATTRIBUTE CODE
============================================================ --}}

<div>

    <label
        for="code"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Code
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Unique technical identifier used by the system, API and integrations.
    </p>

    <input
        type="text"
        name="code"
        id="code"
        value="{{ old('code') }}"
        placeholder="e.g. frame_material"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            text-gray-900
            placeholder:text-gray-400
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
        required
    >

    @error('code')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>



{{-- ============================================================
     ATTRIBUTE TYPE
============================================================ --}}

<div>

    <label
        for="type"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Type
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Defines how the attribute value is entered and stored.
    </p>

    <select
        name="type"
        id="type"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            text-gray-900
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
        required
    >

        <option value="">Select type</option>

        <option value="text" @selected(old('type') === 'text')}>
            Text
        </option>

        <option value="number" @selected(old('type') === 'number')}>
            Number
        </option>

        <option value="select" @selected(old('type') === 'select')}>
            Select
        </option>

        <option value="multiselect" @selected(old('type') === 'multiselect')}>
            Multiselect
        </option>

        <option value="boolean" @selected(old('type') === 'boolean')}>
            Boolean
        </option>

    </select>

    @error('type')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>



{{-- ============================================================
     ATTRIBUTE UNIT
============================================================ --}}

<div>

    <label
        for="unit_id"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Unit
        <span class="font-medium text-gray-400">(optional)</span>
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Measurement unit displayed next to numeric attribute values.
    </p>

    <select
        name="unit_id"
        id="unit_id"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            text-gray-900
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
    >

        <option value="">
            No unit
        </option>

        @foreach($units as $group => $groupUnits)

            <optgroup label="{{ ucfirst(str_replace('_', ' ', $group)) }}">

                @foreach($groupUnits as $unit)

                    <option
                        value="{{ $unit->id }}"
                        @selected(old('unit_id') == $unit->id)
                    >
                        {{ $unit->symbol }}
                        —
                        {{ $unit->translation()?->name ?? $unit->code }}
                    </option>

                @endforeach

            </optgroup>

        @endforeach

    </select>

    @error('unit_id')

        <p class="mt-1 text-xs text-red-500">
            {{ $message }}
        </p>

    @enderror

</div>


{{-- ============================================================
     ATTRIBUTE FLAGS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Attribute Settings
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Configure how this attribute is used across the platform.
        </p>

    </div>


    {{-- FLAGS --}}
    <div class="p-5">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">


            {{-- REQUIRED --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_required"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_required', $attribute->is_required ?? false))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Required
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Value must be provided
                    </span>
                </span>

            </label>


            {{-- FILTERABLE --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_filterable"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_filterable', $attribute->is_filterable ?? false))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Filterable
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Available in product filters
                    </span>
                </span>

            </label>


            {{-- CUSTOM --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_custom"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_custom', $attribute->is_custom ?? false))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Custom
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Supplier-specific attribute
                    </span>
                </span>

            </label>


            {{-- OFFERABLE --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_offerable"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_offerable', $attribute->is_offerable ?? false))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Offerable
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Can be used in supplier offers
                    </span>
                </span>

            </label>

        </div>

    </div>

</div>


{{-- ============================================================
     SORT ORDER
============================================================ --}}

<div>

    <label
        for="sort_order"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Sort Order
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Defines the default display order of this attribute.
    </p>

    <input
        type="number"
        name="sort_order"
        id="sort_order"
        min="0"
        value="{{ old('sort_order', $attribute->sort_order ?? 0) }}"
        class="
            mt-2
            w-32
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            text-gray-900
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
    >

    @error('sort_order')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>



{{-- Actions --}}

<div class="flex justify-end gap-3 pt-4 border-t">

<a
href="{{ route('admin.settings.attributes.index') }}"
class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition"
>

Отмена

</a>


<button
type="submit"
class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition"
>

Сохранить

</button>

</div>


</form>

</div>

</div>

@endsection