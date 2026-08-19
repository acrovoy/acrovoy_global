@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">

    <x-alerts />

    {{-- ============================================================
        HEADER
    ============================================================ --}}

    <div class="flex items-start justify-between gap-4">

        <div>

            <h1 class="text-2xl font-semibold text-gray-900">
                Attribute Groups
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage groups used to organize product attributes.
            </p>

        </div>

        <a
            href="{{ route('admin.settings.attribute-groups.create') }}"
            class="
                inline-flex
                items-center
                gap-2
                px-4
                py-2
                rounded-lg
                bg-gray-900
                text-white
                text-sm
                font-medium
                hover:bg-gray-800
                transition
            "
        >
            + Add Attribute Group
        </a>

    </div>


    {{-- ============================================================
        TABLE
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        @if($groups->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-left
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                ID
                            </th>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-left
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                Name
                            </th>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-left
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                Status
                            </th>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-left
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                Owner
                            </th>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-left
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                Attributes
                            </th>

                            <th
                                class="
                                    px-5
                                    py-3
                                    text-right
                                    text-xs
                                    font-semibold
                                    text-gray-500
                                    uppercase
                                    tracking-wide
                                "
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($groups as $group)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- ID --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-500">
                                        #{{ $group->id }}
                                    </span>

                                </td>


                                {{-- NAME --}}

                                <td class="px-5 py-4">

                                    <div class="font-medium text-gray-900">
                                        {{ $group->name }}
                                    </div>

                                    @if($group->code)

                                        <div class="mt-1">

                                            <code
                                                class="
                                                    text-xs
                                                    text-gray-500
                                                    bg-gray-100
                                                    border
                                                    border-gray-200
                                                    px-1.5
                                                    py-0.5
                                                    rounded
                                                "
                                            >
                                                {{ $group->code }}
                                            </code>

                                        </div>

                                    @endif

                                </td>


                                {{-- STATUS --}}

                                <td class="px-5 py-4">

                                    @if($group->is_active)

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                px-2.5
                                                py-1
                                                rounded-md
                                                text-xs
                                                font-medium
                                                bg-green-50
                                                text-green-700
                                                border
                                                border-green-200
                                            "
                                        >
                                            Active
                                        </span>

                                    @else

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                px-2.5
                                                py-1
                                                rounded-md
                                                text-xs
                                                font-medium
                                                bg-gray-100
                                                text-gray-500
                                                border
                                                border-gray-200
                                            "
                                        >
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- OWNER --}}

                                <td class="px-5 py-4">

                                    @if($group->owner_type)

                                        <div class="text-sm text-gray-700">

                                            {{ class_basename($group->owner_type) }}

                                        </div>

                                        @if($group->owner_id)

                                            <div class="text-xs text-gray-400 mt-0.5">
                                                ID: {{ $group->owner_id }}
                                            </div>

                                        @endif

                                    @else

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                px-2.5
                                                py-1
                                                rounded-md
                                                text-xs
                                                font-medium
                                                bg-blue-50
                                                text-blue-700
                                                border
                                                border-blue-200
                                            "
                                        >
                                            System
                                        </span>

                                    @endif

                                </td>


                                {{-- ATTRIBUTES COUNT --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">

                                        {{ $group->attributes_count ?? $group->attributes?->count() ?? 0 }}

                                    </span>

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- EDIT --}}

                                        <a
                                            href="{{ route(
                                                'admin.settings.attribute-groups.edit',
                                                $group->id
                                            ) }}"
                                            class="
                                                px-3
                                                py-1.5
                                                text-xs
                                                font-medium
                                                rounded-lg
                                                border
                                                border-gray-200
                                                text-gray-700
                                                hover:bg-gray-100
                                                transition
                                            "
                                        >
                                            Edit
                                        </a>


                                        {{-- DELETE --}}

                                        <form
                                            action="{{ route(
                                                'admin.settings.attribute-groups.destroy',
                                                $group->id
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this attribute group?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="
                                                    px-3
                                                    py-1.5
                                                    text-xs
                                                    font-medium
                                                    rounded-lg
                                                    border
                                                    border-red-200
                                                    text-red-600
                                                    hover:bg-red-50
                                                    transition
                                                "
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- ====================================================
                PAGINATION
            ===================================================== --}}

            @if(method_exists($groups, 'links'))

                <div class="px-5 py-4 border-t border-gray-200">

                    {{ $groups->links() }}

                </div>

            @endif


        @else

            {{-- ====================================================
                EMPTY STATE
            ===================================================== --}}

            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">

                <div
                    class="
                        w-12
                        h-12
                        rounded-xl
                        bg-gray-100
                        flex
                        items-center
                        justify-center
                        text-gray-400
                        mb-4
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-4m-6 0V3h6v2m-6 0h6"
                        />
                    </svg>
                </div>


                <h3 class="text-sm font-semibold text-gray-900">
                    No attribute groups
                </h3>

                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                    Create an attribute group to organize your product attributes.
                </p>


                <a
                    href="{{ route('admin.settings.attribute-groups.create') }}"
                    class="
                        inline-flex
                        items-center
                        mt-5
                        px-4
                        py-2
                        rounded-lg
                        bg-gray-900
                        text-white
                        text-sm
                        font-medium
                        hover:bg-gray-800
                        transition
                    "
                >
                    Add Attribute Group
                </a>

            </div>

        @endif

    </div>

</div>

@endsection