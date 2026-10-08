@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">

    <x-alerts />

    {{-- HEADER --}}

    <div class="flex items-start justify-between gap-4">

        <div>

            <h1 class="text-2xl font-semibold text-gray-900">
                Payment Terms
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage payment terms and conditions available for products and orders.
            </p>

        </div>

        <a
            href="{{ route('admin.settings.payment-terms.create') }}"
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
            + Add Payment Term
        </a>

    </div>


    {{-- TABLE --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        @if($paymentTerms->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                ID
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Sort Order
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Name
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Code
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($paymentTerms as $paymentTerm)

                            @php
                                $translation = $paymentTerm->translations
                                    ->firstWhere('locale', app()->getLocale());

                                $translation ??= $paymentTerm->translations->first();
                            @endphp

                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-5 py-4">
                                    <span class="text-sm text-gray-500">
                                        #{{ $paymentTerm->id }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            justify-center
                                            min-w-8
                                            h-7
                                            px-2
                                            rounded-md
                                            bg-gray-100
                                            border
                                            border-gray-200
                                            text-xs
                                            font-semibold
                                            text-gray-600
                                        "
                                    >
                                        {{ $paymentTerm->sort_order ?? 0 }}
                                    </span>

                                </td>

                                <td class="px-5 py-4">

                                    <div class="font-medium text-gray-900">
                                        {{ $translation?->name ?? $paymentTerm->code }}
                                    </div>

                                    @if($translation?->description)

                                        <div class="text-xs text-gray-400 mt-1 max-w-md truncate">
                                            {{ $translation->description }}
                                        </div>

                                    @endif

                                </td>

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
                                        {{ $paymentTerm->code }}
                                    </code>

                                </td>

                                <td class="px-5 py-4">

                                    @if($paymentTerm->is_active)

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

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        <form
                                            action="{{ route(
                                                'admin.settings.payment-terms.toggle',
                                                $paymentTerm->id
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
                                                {{ $paymentTerm->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                        </form>

                                        <a
                                            href="{{ route(
                                                'admin.settings.payment-terms.edit',
                                                $paymentTerm->id
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

                                        <form
                                            action="{{ route(
                                                'admin.settings.payment-terms.destroy',
                                                $paymentTerm->id
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this payment term?');"
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
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0118 7.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <h3 class="text-sm font-semibold text-gray-900">
                    No payment terms
                </h3>

                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                    Create a payment term to make it available for products.
                </p>

                <a
                    href="{{ route('admin.settings.payment-terms.create') }}"
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
                    Add Payment Term
                </a>

            </div>

        @endif

    </div>

</div>

@endsection