@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">

    {{-- ============================================================
        HEADER
    ============================================================= --}}

    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Attributes
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage product attributes used in filters and specifications
            </p>
        </div>


        <a
            href="{{ route('admin.settings.attributes.create') }}"
            class="inline-flex items-center gap-2
                   px-4 py-2
                   rounded-lg
                   bg-gray-900
                   text-white
                   text-sm font-medium
                   shadow-sm
                   hover:bg-gray-800
                   transition"
        >
            <span class="text-base leading-none">+</span>
            Add attribute
        </a>

    </div>


    
    

    {{-- ============================================================
        FILTERS
    ============================================================= --}}
    <form method="GET" action="{{ route('admin.settings.attributes.index') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-sm">
                    Filters
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Find and sort attributes
                </p>
            </div>
            @if(request()->hasAny(['search','entity_type','type','new','sort','direction']))
                <a href="{{ route('admin.settings.attributes.index') }}" class="text-sm text-gray-500 hover:text-gray-900 hover:underline">
                    Reset
                </a>
            @endif
        </div>

        {{-- FILTERS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- SEARCH --}}
            <div class="lg:col-span-2">
                <label for="attribute-search" class="block text-xs font-medium text-gray-600 mb-1">
                    Search
                </label>
                <input id="attribute-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code..." class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
            </div>

            {{-- ENTITY TYPE --}}
            <div>
                <label for="attribute-entity-type" class="block text-xs font-medium text-gray-600 mb-1">
                    Entity Type
                </label>
                <select id="attribute-entity-type" name="entity_type" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="">All entity types</option>
                    <option value="product" @selected(request('entity_type') === 'product')>Product</option>
                    <option value="rfq" @selected(request('entity_type') === 'rfq')>RFQ</option>
                    <option value="offer" @selected(request('entity_type') === 'offer')>Offer</option>
                    <option value="contract" @selected(request('entity_type') === 'contract')>Contract</option>
                    <option value="company" @selected(request('entity_type') === 'company')>Company</option>
                    <option value="user" @selected(request('entity_type') === 'user')>User</option>
                </select>
            </div>

            {{-- ATTRIBUTE TYPE --}}
            <div>
                <label for="attribute-type" class="block text-xs font-medium text-gray-600 mb-1">
                    Attribute Type
                </label>
                <select id="attribute-type" name="type" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="">All types</option>
                    <option value="text" @selected(request('type') === 'text')>Text</option>
                    <option value="number" @selected(request('type') === 'number')>Number</option>
                    <option value="select" @selected(request('type') === 'select')>Select</option>
                    <option value="multiselect" @selected(request('type') === 'multiselect')>Multiselect</option>
                    <option value="boolean" @selected(request('type') === 'boolean')>Boolean</option>
                </select>
            </div>
        </div>

        {{-- NEW --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="new" value="1" @checked(request()->boolean('new')) class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                <span class="text-sm font-medium text-gray-700">
                    New custom attributes
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-xs font-semibold">
                    Last 7 days
                </span>
            </label>
        </div>

        {{-- SORTING --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- SORT BY --}}
                <div>
                    <label for="attribute-sort" class="block text-xs font-medium text-gray-600 mb-1">
                        Sort by
                    </label>
                    <select id="attribute-sort" name="sort" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        <option value="sort_order" @selected(request('sort', 'sort_order') === 'sort_order')>Sort Order</option>
                        <option value="name" @selected(request('sort') === 'name')>Name</option>
                        <option value="code" @selected(request('sort') === 'code')>Code</option>
                        <option value="created_at" @selected(request('sort') === 'created_at')>Created</option>
                    </select>
                </div>

                {{-- DIRECTION --}}
                <div>
                    <label for="attribute-direction" class="block text-xs font-medium text-gray-600 mb-1">
                        Direction
                    </label>
                    <select id="attribute-direction" name="direction" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        <option value="asc" @selected(request('direction', 'asc') === 'asc')>Ascending</option>
                        <option value="desc" @selected(request('direction') === 'desc')>Descending</option>
                    </select>
                </div>

                {{-- APPLY --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition">
                        Apply filters
                    </button>
                </div>
            </div>
        </div>
    </form>


{{-- ============================================================
    ATTRIBUTES
============================================================= --}}

<div class="space-y-4">


        {{-- ========================================================
            SYSTEM ATTRIBUTES
        ========================================================= --}}

        <div
            class="bg-white
                   border border-gray-200
                   rounded-xl
                   shadow-sm
                   overflow-hidden"
        >

            {{-- HEADER --}}

            <button
                type="button"
                class="attribute-section-toggle
                       w-full
                       flex items-center justify-between
                       px-5 py-4
                       text-left
                       bg-gray-50
                       hover:bg-gray-100
                       transition"
                data-target="system-attributes"
                aria-expanded="false"
            >

                <div>

                    <div class="flex items-center gap-3">

                        <h3 class="font-semibold text-gray-800 text-sm">
                            System Attributes
                        </h3>

                        <span
                            class="inline-flex items-center
                                   px-2 py-0.5
                                   rounded-md
                                   bg-gray-200
                                   text-gray-600
                                   text-xs"
                        >
                            {{ $systemAttributes->count() }}
                        </span>

                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        Built-in attributes managed by the system.
                    </p>

                </div>


                {{-- ARROW --}}

                <svg
                    class="attribute-section-arrow
                           w-5 h-5
                           text-gray-500
                           transition-transform duration-200"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>

            </button>


            {{-- CONTENT --}}

            <div
                id="system-attributes"
                class="hidden"
            >

                @if($systemAttributes->count())

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead class="bg-white border-t border-b border-gray-100">

                                <tr>

                                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                                        Name
                                    </th>

                                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                                        Entity Type
                                    </th>

                                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                                        Type
                                    </th>

                                    <th class="px-5 py-3 text-left font-medium text-gray-500">
                                        Required
                                    </th>

                                    <th class="px-5 py-3 text-right font-medium text-gray-500">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100">

                                @foreach($systemAttributes as $attribute)

                                    <tr class="hover:bg-gray-50 transition">

                                        {{-- NAME --}}

                                        <td class="px-5 py-3">

                                            <div class="font-semibold text-gray-900">
                                                {{ $attribute->name }}
                                            </div>

                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $attribute->code }}
                                            </div>

                                        </td>


                                        {{-- ENTITY TYPE --}}

                                        <td class="px-5 py-3 text-gray-600">
                                            {{ $attribute->entity_type }}
                                        </td>


                                        {{-- TYPE --}}

                                        <td class="px-5 py-3">

                                            <span
                                                class="inline-flex
                                                       px-2 py-1
                                                       rounded-md
                                                       bg-gray-100
                                                       text-gray-700
                                                       text-xs"
                                            >
                                                {{ ucfirst($attribute->type) }}
                                            </span>

                                        </td>


                                        {{-- REQUIRED --}}

                                        <td class="px-5 py-3 text-gray-600">

                                            {{ $attribute->is_required ? 'Yes' : 'No' }}

                                        </td>


                                        {{-- ACTIONS --}}

                                        <td class="px-5 py-3 text-right whitespace-nowrap">

                                            @if(in_array($attribute->type, ['select', 'multiselect']))

                                                <a
                                                    href="{{ route(
                                                        'admin.settings.attributes.options.index',
                                                        $attribute->id
                                                    ) }}"
                                                    class="text-sm
                                                           text-gray-600
                                                           hover:text-gray-900
                                                           hover:underline
                                                           mr-3"
                                                >
                                                    Options
                                                </a>

                                            @endif


                                            <a
                                                href="{{ route(
                                                    'admin.settings.attributes.edit',
                                                    $attribute->id
                                                ) }}"
                                                class="text-sm
                                                       text-gray-600
                                                       hover:text-gray-900
                                                       hover:underline
                                                       mr-3"
                                            >
                                                Edit
                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="px-5 py-8 text-center text-sm text-gray-400">
                        No system attributes.
                    </div>

                @endif

            </div>

        </div>


        {{-- ========================================================
    CUSTOM ATTRIBUTES
========================================================= --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    {{-- HEADER --}}
    <button type="button" class="attribute-section-toggle w-full flex items-center justify-between px-5 py-4 text-left bg-gray-50 hover:bg-gray-100 transition" data-target="custom-attributes" aria-expanded="false">
        <div>
            
        <div class="flex items-center gap-3">
            <h3 class="font-semibold text-gray-800 text-sm">
                Custom Attributes
            </h3>

            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs">
                {{ $customAttributes->count() }}
            </span>

            @if($newCustomAttributes->count())
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-xs font-semibold">
                    {{ $newCustomAttributes->count() }} New
                </span>
            @endif
        </div>

            <p class="text-xs text-gray-500 mt-1">
                Custom attributes created by buyers or suppliers.
            </p>
        </div>
        {{-- ARROW --}}
        <svg class="attribute-section-arrow w-5 h-5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    {{-- CONTENT --}}
    <div id="custom-attributes" class="hidden">
        @if($customAttributes->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white border-t border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Name</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Entity Type</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Type</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Required</th>
                            <th class="px-5 py-3 text-right font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($customAttributes as $attribute)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- NAME --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="font-semibold text-gray-900">
                                            {{ $attribute->name }}
                                        </div>
                                        @if($attribute->created_at && $attribute->created_at->gte(now()->subDays(7)))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-semibold uppercase tracking-wide">
                                                New
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $attribute->code }}
                                    </div>
                                </td>
                                {{-- ENTITY TYPE --}}
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $attribute->entity_type }}
                                </td>
                                {{-- TYPE --}}
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                        {{ ucfirst($attribute->type) }}
                                    </span>
                                </td>
                                {{-- REQUIRED --}}
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $attribute->is_required ? 'Yes' : 'No' }}
                                </td>
                                {{-- ACTIONS --}}
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    @if(in_array($attribute->type, ['select', 'multiselect']))
                                        <a href="{{ route('admin.settings.attributes.options.index', $attribute->id) }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline mr-3">
                                            Options
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.settings.attributes.edit', $attribute->id) }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline mr-3">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.settings.attributes.destroy', $attribute->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete attribute?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-8 text-center text-sm text-gray-400">
                No custom attributes.
            </div>
        @endif
    </div>
</div>

    </div>

</div>


{{-- ================================================================
    COLLAPSE SCRIPT
================================================================ --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('.attribute-section-toggle')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const targetId = this.dataset.target;
                const target = document.getElementById(targetId);

                const arrow = this.querySelector(
                    '.attribute-section-arrow'
                );

                const isOpen = !target.classList.contains('hidden');


                if (isOpen) {

                    target.classList.add('hidden');

                    this.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                    arrow.classList.remove(
                        'rotate-180'
                    );

                } else {

                    target.classList.remove('hidden');

                    this.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                    arrow.classList.add(
                        'rotate-180'
                    );

                }

            });

        });

});

</script>

@endsection