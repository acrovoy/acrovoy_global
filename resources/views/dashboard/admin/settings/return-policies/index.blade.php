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
                Return Policies
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage return and refund policies available across the platform.
            </p>

        </div>

        <a
            href="{{ route('admin.settings.return-policies.create') }}"
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
            + Add Return Policy
        </a>

    </div>


    {{-- ============================================================
        TABLE
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        @if($returnPolicies->count())

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
                                Policy
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
                                Code
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
                                Return Window
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
    Reasons
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
    Resolutions
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
                                Shipping
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
                                Restocking Fee
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
                                Custom Products
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
                                Products
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

                        @foreach($returnPolicies as $returnPolicy)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- ID --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-500">
                                        #{{ $returnPolicy->id }}
                                    </span>

                                </td>


                                {{-- POLICY --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-2">

                                        <div class="font-medium text-gray-900">
                                            {{ $returnPolicy->name }}
                                        </div>

                                        @if($returnPolicy->is_default)

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-2
                                                    py-0.5
                                                    rounded-md
                                                    text-[11px]
                                                    font-semibold
                                                    bg-blue-50
                                                    text-blue-700
                                                    border
                                                    border-blue-200
                                                "
                                            >
                                                Default
                                            </span>

                                        @endif

                                    </div>

                                    @if($returnPolicy->translations->count())

                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $returnPolicy->translations->count() }}
                                            {{ $returnPolicy->translations->count() === 1 ? 'translation' : 'translations' }}
                                        </div>

                                    @endif

                                </td>


                                {{-- CODE --}}

                                <td class="px-5 py-4">

                                    @if($returnPolicy->code)

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
                                            {{ $returnPolicy->code }}
                                        </code>

                                    @else

                                        <span class="text-xs text-gray-400">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- RETURN WINDOW --}}

                                <td class="px-5 py-4">

                                    @if($returnPolicy->return_window_days !== null)

                                        <span class="text-sm text-gray-700">
                                            {{ $returnPolicy->return_window_days }}
                                            {{ $returnPolicy->return_window_days == 1 ? 'day' : 'days' }}
                                        </span>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            No window
                                        </span>

                                    @endif

                                </td>


                                
{{-- REASONS --}}

<td class="px-5 py-4">

    @if($returnPolicy->reasons->isNotEmpty())

        <div class="flex flex-wrap gap-1.5 max-w-[280px]">

            @foreach($returnPolicy->reasons as $reason)

                @php
                    $translation = $reason->translation(app()->getLocale())
                        ?? $reason->translation('en');
                @endphp

                @if($translation?->name)

                    <span
                        class="
                            inline-flex
                            items-center
                            px-2
                            py-1
                            rounded-md
                            bg-gray-50
                            border
                            border-gray-200
                            text-xs
                            font-medium
                            text-gray-700
                        "
                    >
                        {{ $translation->name }}
                    </span>

                @endif

            @endforeach

        </div>

    @else

        <span class="text-sm text-gray-400">
            None
        </span>

    @endif

</td>



{{-- RESOLUTIONS --}}

<td class="px-5 py-4">

    @if($returnPolicy->resolutions->isNotEmpty())

        <div class="flex flex-wrap gap-1.5 max-w-[280px]">

            @foreach($returnPolicy->resolutions as $resolution)

                @php
                    $translation = $resolution->translation(app()->getLocale())
                        ?? $resolution->translation('en');
                @endphp

                @if($translation?->name)

                    <span
                        class="
                            inline-flex
                            items-center
                            px-2
                            py-1
                            rounded-md
                            bg-gray-50
                            border
                            border-gray-200
                            text-xs
                            font-medium
                            text-gray-700
                        "
                    >
                        {{ $translation->name }}
                    </span>

                @endif

            @endforeach

        </div>

    @else

        <span class="text-sm text-gray-400">
            None
        </span>

    @endif

</td>





                                {{-- SHIPPING --}}

                                <td class="px-5 py-4">

                                    @switch($returnPolicy->return_shipping_payer)

                                        @case('buyer')

                                            <span class="text-sm text-gray-700">
                                                Buyer
                                            </span>

                                            @break

                                        @case('supplier')

                                            <span class="text-sm text-gray-700">
                                                Supplier
                                            </span>

                                            @break

                                        @case('depends_on_reason')

                                            <span class="text-sm text-gray-700">
                                                By reason
                                            </span>

                                            @break

                                        @default

                                            <span class="text-sm text-gray-400">
                                                —
                                            </span>

                                    @endswitch

                                </td>


                                {{-- RESTOCKING FEE --}}

                                <td class="px-5 py-4">

                                    @if($returnPolicy->restocking_fee_enabled)

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                px-2.5
                                                py-1
                                                rounded-md
                                                text-xs
                                                font-medium
                                                bg-amber-50
                                                text-amber-700
                                                border
                                                border-amber-200
                                            "
                                        >
                                            {{ $returnPolicy->restocking_fee_percent !== null
                                                ? number_format((float) $returnPolicy->restocking_fee_percent, 2) . '%'
                                                : 'Enabled'
                                            }}
                                        </span>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            None
                                        </span>

                                    @endif

                                </td>


                                {{-- CUSTOM PRODUCTS --}}

                                <td class="px-5 py-4">

                                    @if($returnPolicy->custom_products_returnable)

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
                                            Allowed
                                        </span>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            Not allowed
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}

                                <td class="px-5 py-4">

                                    @if($returnPolicy->is_active)

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


                                {{-- PRODUCTS --}}

                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $returnPolicy->product_policies_count ?? 0 }}
                                    </span>

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- TOGGLE ACTIVE --}}

                                        <form
                                            action="{{ route(
                                                'admin.settings.return-policies.toggle-active',
                                                $returnPolicy->id
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
                                                {{ $returnPolicy->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                        </form>


                                        {{-- EDIT --}}

                                        <a
                                            href="{{ route(
                                                'admin.settings.return-policies.edit',
                                                $returnPolicy->id
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
                                                'admin.settings.return-policies.destroy',
                                                $returnPolicy->id
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this return policy?');"
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
                    No return policies
                </h3>

                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                    Create a return policy to define how returns and refunds are handled across the platform.
                </p>


                <a
                    href="{{ route('admin.settings.return-policies.create') }}"
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
                    Add Return Policy
                </a>

            </div>

        @endif

    </div>

</div>

@endsection