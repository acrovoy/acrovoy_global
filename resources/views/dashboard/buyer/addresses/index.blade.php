
@extends('dashboard.layout')

@section('dashboard-content')

<div class="mx-auto max-w-6xl">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

        <div>
            

            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                Delivery Addresses
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Manage your saved delivery addresses and choose the default address for checkout.
            </p>
        </div>

        <a
            href="{{ route('buyer.addresses.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-150 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 active:scale-[0.98]"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14"/>
            </svg>

            Add Address
        </a>

    </div>


    {{-- =========================================================
         ALERTS
    ========================================================== --}}
    <div class="mb-6 space-y-3">

        <x-alerts />

        

       

    </div>


    @php
        $addressCount = $addresses->count();
        $defaultCount = $addresses->where('is_default', true)->count();
        $otherCount = $addressCount - $defaultCount;
    @endphp


   


    {{-- =========================================================
         SAVED ADDRESSES
    ========================================================== --}}
    <section>

        <div class="mb-3 flex items-end justify-between gap-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Saved Addresses
                </h2>

                
            </div>

            <span class="hidden items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 sm:inline-flex">
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

                        <article class="group px-5 py-5 transition-colors hover:bg-gray-50/70 sm:px-6">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                                {{-- ADDRESS INFORMATION --}}
                                <div class="flex min-w-0 flex-1 gap-4">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">

                                        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1116 0z"/>
                                            <circle cx="12" cy="10" r="2.5"/>
                                        </svg>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        {{-- NAME AND STATUS --}}
                                        <div class="flex flex-wrap items-center gap-2">

                                            <h3 class="truncate text-sm font-semibold text-gray-900">
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
                                        <div class="mt-3 space-y-2 text-sm text-gray-600">

                                            @if(filled($address->street))
                                                <div class="flex items-start gap-2">

                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 21h18M5 21V7l8-4v18m0-11h6v11M8 9h1m-1 4h1m-1 4h1m7-2h1"/>
                                                    </svg>

                                                    <span class="break-words">
                                                        {{ $address->street }}
                                                    </span>

                                                </div>
                                            @endif


                                            @if(filled($cityLine))
                                                <div class="flex items-start gap-2">

                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 10l9-7 9 7M5 9v11h14V9M9 20v-6h6v6"/>
                                                    </svg>

                                                    <span>
                                                        {{ $cityLine }}
                                                    </span>

                                                </div>
                                            @endif


                                            @if(filled($regionName))
                                                <div class="flex items-start gap-2">

                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 21s7-4.4 7-11a7 7 0 10-14 0c0 6.6 7 11 7 11z"/>
                                                        <circle cx="12" cy="10" r="2"/>
                                                    </svg>

                                                    <span>{{ $regionName }}</span>

                                                </div>
                                            @endif


                                            @if(filled($countryName))
                                                <div class="flex items-start gap-2">

                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 12h18M12 3a15 15 0 010 18m0-18a15 15 0 000 18"/>
                                                    </svg>

                                                    <span>{{ $countryName }}</span>

                                                </div>
                                            @endif


                                            @if(filled($address->phone))
                                                <div class="flex items-start gap-2">

                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.4 19.4 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 2 .7 2.9a2 2 0 01-.5 2.1L8 8.9a16 16 0 006 6l1.2-1.3a2 2 0 012.1-.5c.9.3 1.9.6 2.9.7a2 2 0 011.8 2.1z"/>
                                                    </svg>

                                                    <span>{{ $address->phone }}</span>

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>


                                {{-- ACTIONS --}}
                                <div class="flex flex-wrap items-center gap-2 lg:shrink-0 lg:justify-end">

                                    @unless($address->is_default)
                                        <form
                                            method="POST"
                                            action="{{ route('buyer.addresses.set-default', $address) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3z"/>
                                                </svg>

                                                Set Default
                                            </button>
                                        </form>
                                    @endunless


                                    <a
                                        href="{{ route('buyer.addresses.edit', $address) }}"
                                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"/>
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
                                            class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10"/>
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
                <div class="px-6 py-14 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-gray-200 bg-gray-50">

                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1116 0z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>

                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-gray-900">
                        No delivery addresses yet
                    </h3>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-gray-500">
                        Add a delivery address to make checkout faster and keep your shipping details organized.
                    </p>

                    <a
                        href="{{ route('buyer.addresses.create') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14"/>
                        </svg>

                        Add Your First Address
                    </a>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection

