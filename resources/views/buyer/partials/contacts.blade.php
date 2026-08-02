
<div class="max-w-7xl mx-auto space-y-4">


{{-- ================= ADDRESS INFORMATION ================= --}}
@php
    $address = $company->primaryAddress;

    $hasAddress =
        $address &&
        (
            filled($address->country_id) ||
            filled($address->state) ||
            filled($address->city) ||
            filled($address->postal_code) ||
            filled($address->address_line_1) ||
            filled($address->address_line_2)
        );
@endphp

@if($hasAddress)

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200">

        <h2 class="text-lg font-semibold text-gray-900">
            Business Location
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Registered business location and operating address.
        </p>

    </div>

    <div class="grid lg:grid-cols-[360px,1fr]">

        {{-- Left --}}
        <div class="p-6 border-r border-gray-200">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                    <svg class="h-6 w-6"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0"/>

                    </svg>

                </div>

                <div>

                    <div class="text-sm font-semibold text-gray-900">
                        Registered Address
                    </div>

                    <div class="mt-3 space-y-2 text-sm text-gray-600">

                        @if($address->address_line_1)
                            <div>{{ $address->address_line_1 }}</div>
                        @endif

                        @if($address->address_line_2)
                            <div>{{ $address->address_line_2 }}</div>
                        @endif

                        @if($address->city || $address->state)
                            <div>
                                {{ collect([$address->city, $address->state])->filter()->implode(', ') }}
                            </div>
                        @endif

                        @if($address->postal_code)
                            <div>
                                {{ $address->postal_code }}
                            </div>
                        @endif

                        @if($address->country)
                            <div class="font-medium text-gray-900">
                                {{ $address->country->name }}
                            </div>
                        @endif

                    </div>

                </div>

            </div>

            @if($address->latitude && $address->longitude)

                <a href="https://www.google.com/maps/search/?api=1&query={{ $address->latitude }},{{ $address->longitude }}"
                   target="_blank"
                   class="mt-8 inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M13.828 10.172a4 4 0 00-5.656 5.656M10 13l4-4m-2-6h7v7"/>

                    </svg>

                    View on Google Maps

                </a>

            @endif

        </div>

        {{-- Right --}}
        <div>

            @if($address->latitude && $address->longitude)

                <div
    id="company-address-map"
    class="h-[360px] w-full"
    data-lat="{{ $address->latitude }}"
    data-lng="{{ $address->longitude }}">
</div>

            @else

                <div class="flex h-[360px] items-center justify-center bg-gray-50 text-gray-400">

                    Location map is unavailable.

                </div>

            @endif

        </div>

    </div>

</div>

@endif

        {{-- ================= CONTACT INFORMATION ================= --}}


   <x-contact.list
    :contacts="$company->publicContacts"
    title="Business Contacts"
    description="Primary business contact details."
    ownerType="supplier"
    :ownerId="$company->id"
    :public="true"
    
/>


</div>











 

    
<script> 

   document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.copy-certificate-number');

    if (!btn) return;

    const number = btn.dataset.number;

    try {

        await navigator.clipboard.writeText(number);

        dispatchAlert(
            'success',
            'Certificate number copied to clipboard.'
        );

    } catch (err) {

        dispatchAlert(
            'error',
            'Failed to copy certificate number.'
        );

    }

});

</script>



<script>
function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
}
</script>






<x-edit.edit-drawer />

<x-alerts />

