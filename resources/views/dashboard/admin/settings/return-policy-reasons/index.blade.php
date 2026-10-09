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
                Return Reasons
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage the reasons buyers can select when requesting a return.
            </p>

        </div>

        <a
            href="{{ route('admin.settings.return-policy-reasons.create') }}"
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
            + Add Return Reason
        </a>

    </div>


    {{-- ============================================================
        TABLE
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        @if($reasons->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                ID
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Reason
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Code
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Sort Order
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Policies
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @foreach($reasons as $reason)

                            @php
                                $translation = $reason->translation(app()->getLocale())
                                    ?? $reason->translation('en');
                            @endphp

                            <tr class="hover:bg-gray-50 transition">

                                {{-- ID --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-500">
                                        #{{ $reason->id }}
                                    </span>

                                </td>


                                {{-- REASON --}}

                                <td class="px-5 py-4">

                                    <div class="font-medium text-gray-900">
                                        {{ $translation?->name ?? $reason->code }}
                                    </div>

                                    @if($translation?->description)

                                        <div class="text-xs text-gray-400 mt-1 max-w-md truncate">
                                            {{ $translation->description }}
                                        </div>

                                    @endif

                                    @if($reason->translations->count())

                                        <div class="text-xs text-gray-400 mt-1">

                                            {{ $reason->translations->count() }}

                                            {{ $reason->translations->count() === 1
                                                ? 'translation'
                                                : 'translations'
                                            }}

                                        </div>

                                    @endif

                                </td>


                                {{-- CODE --}}

                                <td class="px-5 py-4">

                                    <code
                                        class="
                                            text-xs
                                            text-gray-600
                                            bg-gray-100
                                            border
                                            border-gray-200
                                            px-1.5
                                            py-0.5
                                            rounded
                                        "
                                    >
                                        {{ $reason->code }}
                                    </code>

                                </td>


                                {{-- SORT ORDER --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $reason->sort_order }}
                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td class="px-5 py-4">

                                    @if($reason->is_active)

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


                                {{-- POLICIES --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $reason->policies_count ?? 0 }}
                                    </span>

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- TOGGLE ACTIVE --}}

                                        <form
                                            action="{{ route(
                                                'admin.settings.return-policy-reasons.toggle-active',
                                                $reason->id
                                            ) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
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
                                                {{ $reason->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                        </form>


                                        {{-- EDIT --}}

                                        <a
                                            href="{{ route(
                                                'admin.settings.return-policy-reasons.edit',
                                                $reason->id
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
                                                'admin.settings.return-policy-reasons.destroy',
                                                $reason->id
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this return reason?');"
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
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <h3 class="text-sm font-semibold text-gray-900">
                    No return reasons
                </h3>

                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                    Create a return reason to define why buyers can request a return.
                </p>

                <a
                    href="{{ route('admin.settings.return-policy-reasons.create') }}"
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
                    Add Return Reason
                </a>

            </div>

        @endif

    </div>

</div>

@endsection