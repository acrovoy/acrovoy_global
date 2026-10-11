
@extends('dashboard.layout')

@section('dashboard-content')

<div class="mx-auto max-w-6xl space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                Delivery Addresses
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                Manage your saved delivery addresses and choose the default address for checkout.
            </p>
        </div>

        <a
            href="{{ route('buyer.addresses.create') }}"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 active:scale-[0.98]"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M12 5v14M5 12h14"/>
            </svg>

            Add Address
        </a>

    </div>

    {{-- ALERTS --}}
    <x-alerts />

    @php
        $addressCount = $addresses->count();
        $defaultCount = $addresses->where('is_default', true)->count();
        $otherCount = $addressCount - $defaultCount;
    @endphp

    {{-- ADDRESS SUMMARY --}}
    @if($addressCount > 0)
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.7"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Saved addresses</p>
                        <p class="mt-0.5 text-xl font-semibold tabular-nums text-gray-900">
                            {{ $addressCount }}
                        </p>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.7"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="m5 12 4 4L19 6"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Default address</p>
                        <p class="mt-0.5 text-xl font-semibold tabular-nums text-gray-900">
                            {{ $defaultCount }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    @endif

    {{-- SAVED ADDRESSES --}}
    <section>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Saved Addresses
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Your delivery information for future orders.
                </p>
            </div>

            <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600">
                {{ $addressCount }} {{ \Illuminate\Support\Str::plural('address', $addressCount) }}
            </span>

        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            @if($addressCount > 0)

                <div class="divide-y divide-gray-100">

                    @foreach($addresses as $address)

                        @php
                            $fullName = trim(
                                ($address->first_name ?? '') . ' ' . ($address->last_name ?? '')
                            );

                            $cityLine = collect([
                                $address->city,
                                $address->postal_code,
                            ])->filter(fn ($value) => filled($value))->implode(', ');

                            $regionName = $address->regionLocation?->name;
                            $countryName = $address->countryLocation?->name;
                        @endphp

                        <article class="px-4 py-5 transition-colors hover:bg-gray-50/70 sm:px-6">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                                {{-- ADDRESS INFORMATION --}}
                                <div class="flex min-w-0 flex-1 gap-3 sm:gap-4">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5 text-gray-500"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             aria-hidden="true">
                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                            <circle cx="12" cy="10" r="2.5"/>
                                        </svg>

                                    </div>

                                    <div class="min-w-0 flex-1">

                                        {{-- NAME AND STATUS --}}
                                        <div class="flex flex-wrap items-center gap-2">

                                            <h3 class="break-words text-sm font-semibold text-gray-900 sm:text-base">
                                                {{ $fullName ?: 'Delivery Address' }}
                                            </h3>

                                            @if($address->is_default)
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                    Default
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                    Saved
                                                </span>
                                            @endif

                                        </div>

                                        {{-- ADDRESS DETAILS --}}
                                        <div class="mt-3 space-y-2.5 text-sm text-gray-600">

                                            @if(filled($address->street))
                                                <div class="flex items-start gap-2.5">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.7"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         aria-hidden="true">
                                                        <path d="M3 21h18M5 21V7l8-4v18m0-11h6v11M8 9h1m-1 4h1m-1 4h1m7-2h1"/>
                                                    </svg>

                                                    <span class="break-words">{{ $address->street }}</span>

                                                </div>
                                            @endif

                                            @if(filled($cityLine))
                                                <div class="flex items-start gap-2.5">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.7"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         aria-hidden="true">
                                                        <path d="M3 10l9-7 9 7M5 9v11h14V9M9 20v-6h6v6"/>
                                                    </svg>

                                                    <span class="break-words">{{ $cityLine }}</span>

                                                </div>
                                            @endif

                                            @if(filled($regionName))
                                                <div class="flex items-start gap-2.5">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.7"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         aria-hidden="true">
                                                        <path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/>
                                                        <circle cx="12" cy="10" r="2"/>
                                                    </svg>

                                                    <span class="break-words">{{ $regionName }}</span>

                                                </div>
                                            @endif

                                            @if(filled($countryName))
                                                <div class="flex items-start gap-2.5">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.7"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         aria-hidden="true">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path d="M3 12h18M12 3a15 15 0 0 1 0 18m0-18a15 15 0 0 0 0 18"/>
                                                    </svg>

                                                    <span class="break-words">{{ $countryName }}</span>

                                                </div>
                                            @endif

                                            @if(filled($address->phone))
                                                <div class="flex items-start gap-2.5">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.7"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         aria-hidden="true">
                                                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 8.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z"/>
                                                    </svg>

                                                    <span class="break-words">{{ $address->phone }}</span>

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                                {{-- ACTIONS --}}
                                <div class="flex flex-wrap items-center gap-2 border-t border-gray-100 pt-4 lg:shrink-0 lg:justify-end lg:border-0 lg:pt-0">

                                    @unless($address->is_default)
                                        <form
                                            method="POST"
                                            action="{{ route('buyer.addresses.set-default', $address) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-600 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="h-3.5 w-3.5"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="1.8"
                                                     stroke-linecap="round"
                                                     stroke-linejoin="round"
                                                     aria-hidden="true">
                                                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>
                                                </svg>

                                                Set Default
                                            </button>
                                        </form>
                                    @endunless

                                    <a
                                        href="{{ route('buyer.addresses.edit', $address) }}"
                                        class="inline-flex min-h-9 items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-3.5 w-3.5"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.8"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             aria-hidden="true">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                        </svg>

                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('buyer.addresses.destroy', $address) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this delivery address?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex min-h-9 items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-3.5 w-3.5"
                                                 viewBox="0 0 24 24"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 stroke-width="1.8"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 aria-hidden="true">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="m19 6-1 14H6L5 6"/>
                                                <path d="M10 11v5m4-5v5"/>
                                            </svg>

                                            Delete
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="px-5 py-14 text-center sm:px-6">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-gray-200 bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-7 w-7 text-gray-400"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.6"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        No delivery addresses yet
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                        Add a delivery address to keep your shipping details organized and make checkout easier.
                    </p>

                    <a
                        href="{{ route('buyer.addresses.create') }}"
                        class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 active:scale-[0.98]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        Add Your First Address
                    </a>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection
