@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">


<x-alerts />

{{-- ============================================================
    HEADER
============================================================= --}}

<div class="flex items-center justify-between">

    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            Units
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Manage measurement units used across the platform.
        </p>
    </div>

    <a
        href="{{ route('admin.settings.units.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition"
    >
        <span class="text-lg leading-none">+</span>
        Add Unit
    </a>

</div>


{{-- ============================================================
    FILTERS
============================================================= --}}

<form
    method="GET"
    action="{{ route('admin.settings.units.index') }}"
    class="bg-white border border-gray-200 rounded-xl shadow-sm p-5"
>

    <div class="flex items-center justify-between mb-4">

        <div>
            <h3 class="font-semibold text-gray-800 text-sm">
                Filters
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Find and sort units
            </p>
        </div>

        @if(request()->hasAny([
            'search',
            'unit_group',
            'status',
            'sort',
            'direction'
        ]))

            <a
                href="{{ route('admin.settings.units.index') }}"
                class="text-sm text-gray-500 hover:text-gray-900 hover:underline"
            >
                Reset
            </a>

        @endif

    </div>


    {{-- FILTERS --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- SEARCH --}}

        <div class="lg:col-span-2">

            <label
                for="unit-search"
                class="block text-xs font-medium text-gray-600 mb-1"
            >
                Search
            </label>

            <input
                id="unit-search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by name, code or symbol..."
                class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
            >

        </div>


        {{-- UNIT GROUP --}}

        <div>

            <label
                for="unit-group"
                class="block text-xs font-medium text-gray-600 mb-1"
            >
                Unit Group
            </label>

            <select
                id="unit-group"
                name="unit_group"
                class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
            >

                <option value="">
                    All groups
                </option>

                @foreach($groups as $group)

                    <option
                        value="{{ $group }}"
                        @selected(request('unit_group') === $group)
                    >
                        {{ ucfirst($group) }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- STATUS --}}

        <div>

            <label
                for="unit-status"
                class="block text-xs font-medium text-gray-600 mb-1"
            >
                Status
            </label>

            <select
                id="unit-status"
                name="status"
                class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
            >

                <option value="">
                    All statuses
                </option>

                <option value="active" @selected(request('status') === 'active')>
                    Active
                </option>

                <option value="inactive" @selected(request('status') === 'inactive')>
                    Inactive
                </option>

            </select>

        </div>

    </div>


    {{-- SORTING --}}

    <div class="mt-4 pt-4 border-t border-gray-100">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- SORT BY --}}

            <div>

                <label
                    for="unit-sort"
                    class="block text-xs font-medium text-gray-600 mb-1"
                >
                    Sort by
                </label>

                <select
                    id="unit-sort"
                    name="sort"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                >

                    <option value="sort_order" @selected(request('sort', 'sort_order') === 'sort_order')>
                        Sort Order
                    </option>

                    <option value="name" @selected(request('sort') === 'name')>
                        Name
                    </option>

                    <option value="code" @selected(request('sort') === 'code')>
                        Code
                    </option>

                    <option value="unit_group" @selected(request('sort') === 'unit_group')>
                        Group
                    </option>

                    <option value="created_at" @selected(request('sort') === 'created_at')>
                        Created
                    </option>

                </select>

            </div>


            {{-- DIRECTION --}}

            <div>

                <label
                    for="unit-direction"
                    class="block text-xs font-medium text-gray-600 mb-1"
                >
                    Direction
                </label>

                <select
                    id="unit-direction"
                    name="direction"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                >

                    <option value="asc" @selected(request('direction', 'asc') === 'asc')>
                        Ascending
                    </option>

                    <option value="desc" @selected(request('direction') === 'desc')>
                        Descending
                    </option>

                </select>

            </div>


            {{-- APPLY --}}

            <div class="flex items-end">

                <button
                    type="submit"
                    class="w-full h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition"
                >
                    Apply filters
                </button>

            </div>

        </div>

    </div>

</form>


{{-- ============================================================
    UNITS TABLE
============================================================= --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">

        <div>
            <h3 class="font-semibold text-gray-800 text-sm">
                Units
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                {{ $units->total() }} {{ $units->total() === 1 ? 'unit' : 'units' }} found
            </p>
        </div>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b border-gray-200">

                <tr>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Name
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Code
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Symbol
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Group
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Base
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Status
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                        Sort
                    </th>

                    <th class="px-5 py-3 text-right font-medium text-gray-500">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-100">

                @forelse($units as $unit)

                    <tr class="hover:bg-gray-50 transition">

                        {{-- NAME --}}

                        <td class="px-5 py-3">

                            <div class="font-semibold text-gray-900">
                                {{ $unit->name }}
                            </div>

                            <div class="text-xs text-gray-400 mt-0.5">

                                @foreach($unit->translations as $translation)

                                    <span class="mr-2">
                                        {{ strtoupper($translation->locale) }}:
                                        {{ $translation->name }}
                                    </span>

                                @endforeach

                            </div>

                        </td>


                        {{-- CODE --}}

                        <td class="px-5 py-3">

                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-mono">
                                {{ $unit->code }}
                            </span>

                        </td>


                        {{-- SYMBOL --}}

                        <td class="px-5 py-3 text-gray-700 font-medium">
                            {{ $unit->symbol }}
                        </td>


                        {{-- GROUP --}}

                        <td class="px-5 py-3">

                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                {{ ucfirst($unit->unit_group) }}
                            </span>

                        </td>


                        {{-- BASE --}}

                        <td class="px-5 py-3">

                            @if($unit->is_base)

                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs">
                                    Base
                                </span>

                            @else

                                <span class="text-gray-400">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- STATUS --}}

                        <td class="px-5 py-3">

                            @if($unit->is_active)

                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs">
                                    Active
                                </span>

                            @else

                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-500 text-xs">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- SORT --}}

                        <td class="px-5 py-3 text-gray-600">
                            {{ $unit->sort_order }}
                        </td>


                        {{-- ACTIONS --}}

                        <td class="px-5 py-3 text-right whitespace-nowrap">

                            <a
                                href="{{ route('admin.settings.units.edit', $unit) }}"
                                class="text-sm text-gray-600 hover:text-gray-900 hover:underline mr-3"
                            >
                                Edit
                            </a>

                            <form
                                action="{{ route('admin.settings.units.destroy', $unit) }}"
                                method="POST"
                                class="inline unit-delete-form"
                                data-unit-name="{{ $unit->name }}"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm text-red-600 hover:underline"
                                >
                                    Delete
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="px-5 py-10 text-center text-sm text-gray-400"
                        >
                            No units found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}

    @if($units->hasPages())

        <div class="px-5 py-4 border-t border-gray-200">
            {{ $units->withQueryString()->links() }}
        </div>

    @endif

</div>


</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.unit-delete-form').forEach(form => {

        form.addEventListener('submit', function (event) {

            event.preventDefault();

            const unitName =
                form.dataset.unitName || 'this unit';

            window.confirmModal.open({

                type: 'danger',

                title: 'Delete unit',

                description: 'This action cannot be undone.',

                message:
                    `Are you sure you want to delete "${unitName}"?`,

                cancelText: 'Cancel',

                confirmText: 'Delete',

                onConfirm: () => {

                    form.submit();

                }

            });

        });

    });

});
</script>


@endsection
