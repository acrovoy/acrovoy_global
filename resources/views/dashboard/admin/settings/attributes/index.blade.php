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

<div>
        <a
            href="{{ route('admin.settings.attribute-groups.index') }}"
            class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
            Manage attribute group
        </a>


        <a
            href="{{ route('admin.settings.attributes.create') }}"
            class="inline-flex items-center gap-2
                   px-4 py-2 ml-2
                   rounded-lg
                   bg-gray-900
                   text-white
                   text-sm font-medium
                   shadow-sm
                   hover:bg-gray-800
                   transition">
            <span class="text-base leading-none">+</span>
            Add attribute
        </a>
        </div>

    </div>


@include('dashboard.admin.settings.attributes.partials.filters')


    

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
                   overflow-hidden">

            {{-- HEADER --}}
            <button
                type="button"
                class="attribute-section-toggle w-full flex items-center justify-between
                       px-5 py-4 text-left bg-gray-50 hover:bg-gray-100 transition"
                data-target="system-attributes"
                aria-expanded="false">

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
                                   text-xs">
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
                    stroke-width="2">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 9l-7 7-7-7" />
                </svg>

            </button>

            {{-- CONTENT --}}
            <div
                id="system-attributes"
                class="hidden">

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

    @php
    $groupedAttributes = $systemAttributes
        ->groupBy(function ($attribute) {
            return $attribute->attributeGroup?->id ?? 'ungrouped';
        })
        ->sortBy(function ($attributes, $groupId) {

            if ($groupId === 'ungrouped') {
                return PHP_INT_MAX;
            }

            return $attributes->first()?->attributeGroup?->sort_order
                ?? PHP_INT_MAX;
        })
        ->map(function ($attributes) {

            return $attributes
                ->sortBy([
                    ['sort_order', 'asc'],
                    ['id', 'asc'],
                ])
                ->values();

        });
@endphp

    @foreach($groupedAttributes as $groupId => $attributes)

    @php
        $group = $attributes->first()?->attributeGroup;
    @endphp

        {{-- ================================================================
             GROUP HEADER
        ================================================================= --}}

        <tr class="bg-gray-50">
            <td
                colspan="5"
                class="px-5 py-3"
            >
                <div class="flex items-center gap-3">

                    <div
                        class="w-7 h-7
                               flex items-center justify-center
                               rounded-lg
                               bg-white
                               border border-gray-200
                               text-xs font-semibold text-gray-500"
                    >
                        {{ $group?->sort_order ?? '—' }}
                    </div>

                    <div>

                        <div class="text-sm font-semibold text-gray-900">
                            @if($groupId === 'ungrouped')
                                Ungrouped
                            @else
                                {{ $group?->translation()?->name ?? $group?->name ?? '—' }}
                            @endif
                        </div>

                        <div class="text-[11px] text-gray-400">
                            {{ $attributes->count() }}
                            {{ $attributes->count() === 1 ? 'attribute' : 'attributes' }}
                        </div>

                    </div>

                </div>
            </td>
        </tr>


        {{-- ================================================================
             ATTRIBUTES
        ================================================================= --}}

        @foreach($attributes as $attribute)

            <tr class="hover:bg-gray-50 transition">

                {{-- NAME --}}
                <td class="px-5 py-3 pl-12">

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
                        class="inline-flex px-2 py-1 rounded-md
                               bg-gray-100 text-gray-700 text-xs"
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


                    <form
                        method="POST"
                        action="{{ route(
                            'admin.settings.attributes.destroy',
                            $attribute
                        ) }}"
                        class="attribute-delete-form"
                        data-attribute-name="{{ $attribute->name ?? $attribute->code }}"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="text-sm text-red-600 hover:text-red-800"
                        >
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

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
                                    <form
                                        action="{{ route('admin.settings.attributes.destroy', $attribute->id) }}"
                                        method="POST"
                                        class="inline attribute-delete-form"
                                        data-attribute-name="{{ $attribute->name ?? $attribute->code }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm text-red-600 hover:underline">
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

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.attribute-delete-form').forEach(form => {

        form.addEventListener('submit', function (event) {

            event.preventDefault();

            const attributeName =
                form.dataset.attributeName || 'this attribute';

            window.confirmModal.open({

                type: 'danger',

                title: 'Delete attribute',

                description: 'This action cannot be undone.',

                message:
                    `Are you sure you want to delete "${attributeName}"?`,

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