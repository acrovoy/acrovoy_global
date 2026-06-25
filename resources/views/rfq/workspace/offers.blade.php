@extends('dashboard.layout')

@section('dashboard-content')

{{-- BACK --}}
<a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
    class="text-sm text-gray-500 hover:text-gray-900 transition">

    ← Back to RFQ Overview
</a>


<x-alerts />


<div class="grid grid-cols-12 gap-6">

    {{-- LEFT BIG BLOCK --}}
    <div class="col-span-8">

        @php
        $supplier = $offer->participant;
        $isReadonly = true;
        $isClosed = $rfq->status->isClosed();

        /*
        |---------------------------------------------
        | ACTIVE VERSION FIX
        |---------------------------------------------
        */

        $activeVersion = null;

        if (isset($counterVersion) && $counterVersion) {
        $activeVersion = $counterVersion;
        } else {
        $activeVersion = $offerVersion ?? null;
        }
        @endphp

        <div class="max-w-5xl mx-auto"
            data-rfq-id="{{ $rfq->id }}"
            data-offer-version-id="{{ $activeVersion?->id }}">

            @php $i = 1; @endphp

            <div class="border rounded-lg mb-3 overflow-hidden">

                {{-- HEADER --}}
                <div class="flex justify-between items-center p-3 bg-gray-50">

                    <div class="flex items-center gap-3">

                        <div class="w-5 font-semibold">
                            {{ $i }}
                        </div>

                        <img src="{{ $rfq->image ?? asset('images/no-photo.png') }}"
                            class="w-10 h-10 rounded object-cover">

                        <div>

                            <div class="text-sm font-medium text-gray-900">
                                {{ $rfq->title }}
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                Supplier:
                                {{ $supplier?->name ?? 'Unknown supplier' }}
                            </div>

                        </div>

                    </div>

                    <div class="flex items-center gap-4 text-sm">

                        <span class="text-green-600 text-xs">
                            Supplier offer submitted
                        </span>

                    </div>

                </div>

                <div class="p-5 bg-white">

                    {{-- BUYER ACTIONS --}}
                    <div class="flex justify-end gap-3 mb-4 text-sm">


                        @if(
    !$rfq->status->isClosed() &&
    $activeVersion->id === $lastsubmittedVersion->id &&
    !$existingDraftCounter
)
@if(!$rfq->status->isClosed())
    <button
    type="button"
    onclick="openCloseNegotiationDrawer()"
    class="px-4 py-1 border rounded bg-white hover:bg-gray-50 transition">
    Close negotiation
</button>
@endif
    <a
        href="{{ route('buyer.rfqs.counter-offer.create', [
            'rfq' => $rfq->id,
            'offer' => $offer->id,
            'create' => true,
        ]) }}"
        class="px-4 py-1 border rounded bg-white hover:bg-gray-50">
        Create Counter Offer
    </a>

@endif


                    </div>

                    {{-- REQUIREMENTS --}}
                    <div class="border rounded-lg p-4 mb-6">

                        <div class="mb-3 flex items-center justify-between">

                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Supplier Offer
                            </div>

                            <div class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                Read only
                            </div>

                        </div>

                        <div class="font-medium">
                            General conditions
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            Supplier uploaded files and technical documents
                        </div>

                        @foreach($rfq->attributeValues as $value)

                        @include('rfq.workspace.components.supplier-offer', [
                        'value' => $value,
                        'itemsByAttribute' => $itemsByAttribute,
                        'isReadonly' => true,
                        'supplierOfferVersionToCounter' => $supplierOfferVersionToCounter,
                        ])

                        @endforeach



                        {{-- ATTACHMENTS --}}

                        <div class="font-medium">
                            Attachments
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            Supplier uploaded files and technical documents
                        </div>
                        <div class="border rounded-lg p-4 mb-6 bg-gray-50">

                            <div class="flex items-center gap-3">

                                <div class="w-12 h-12 border rounded flex items-center justify-center text-gray-400">
                                    +
                                </div>

                            </div>

                        </div>


                        {{-- Delivery --}}
                        <div class="font-medium">
                            Delivery Service from Supplier
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            Supplier uploaded files and technical documents
                        </div>

                        <div class="border rounded-lg p-4 mb-6 bg-gray-50">


                        @php
$addressa = App\Models\UserAddress::find($rfq->delivery_address_id);

            if (!$addressa) {
                $cityId = null;
            } else {

                $eexistingLocation = \App\Models\Location::where('name', $addressa->city)
                    ->where('parent_id', $addressa->region)
                    ->first();

                $cityId = $eexistingLocation?->id;
            }

$shippingTemplates = $offer->participant->shippingTemplates
    ->filter(fn ($template) =>
        $template->locations->contains('id', $cityId)
    );
@endphp
                            @forelse($shippingTemplates as $template)





                            <div class="flex items-center justify-between px-2 border-b last:border-0">

                                <div class="mb-2 mt-2">
                                    <div class="font-medium text-gray-900">
                                        {{ $template->title }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $template->description }}


                                    </div>
                                </div>

                                <div class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($template->price, 2) }}
                                </div>

                            </div>

                            @empty

                            <div class="text-xs text-gray-500">
                                No delivery options available for this location
                            </div>

                            @endforelse

                        </div>

                    </div>










                </div>

            </div>

        </div>

    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-span-4">

        @include('rfq.partials.buyer-offer-history-panel')

    </div>

</div>


{{-- CLOSE NEGOTIATION DRAWER --}}
<div id="close-negotiation-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="close-negotiation-drawer"
     class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300 z-50
            flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Close negotiation
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Finalize this RFQ negotiation with supplier
        </p>
    </div>

    {{-- Body --}}
    <form method="POST"
          action=""
          class="flex flex-col flex-1">

        @csrf
        @method('PATCH')

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

            <div class="text-sm text-gray-700">
                Are you sure you want to close this negotiation?
                This action will:
            </div>

            <ul class="text-xs text-gray-500 space-y-2 list-disc pl-4">
                <li>Lock all offers and versions</li>
                <li>Prevent new counter-offers</li>
                <li>Mark RFQ as closed</li>
            </ul>

            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

    <div class="text-sm text-gray-700">
        Choose how you want to finalize this negotiation:
    </div>

    {{-- WARNING --}}
    <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-100 text-xs text-yellow-800">
        ⚠️ Accepting this offer will automatically mark all other supplier offers as <b>Rejected</b>.
    </div>

    <div class="p-3 rounded-lg bg-red-50 border border-red-100 text-xs text-red-800">
        ❌ Rejecting will exclude this supplier from further consideration.
    </div>

    {{-- ACTION TYPE --}}
    <input type="hidden" name="decision" id="close-decision" value="accept">

    {{-- NOTE --}}
    <div>
        <label class="text-xs text-gray-500 uppercase tracking-wide">
            Closing note (optional)
        </label>

        <textarea name="close_note"
                  rows="4"
                  class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                         focus:outline-none focus:ring-2 focus:ring-gray-900/10"></textarea>
    </div>

</div>


            
        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

    <button type="button"
            onclick="closeCloseNegotiationDrawer()"
            class="px-4 py-2 text-sm rounded-lg border border-gray-200
                   text-gray-600 hover:bg-gray-50 transition">
        Cancel
    </button>

    <div class="flex gap-2">

        {{-- REJECT --}}
        <button type="submit"
                onclick="document.getElementById('close-decision').value='reject'"
                class="px-4 py-2 text-sm rounded-lg border border-red-200
                       text-red-600 hover:bg-red-50 transition">
            Reject
        </button>

        {{-- ACCEPT --}}
        <button type="submit"
                onclick="document.getElementById('close-decision').value='accept'"
                class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                       hover:bg-gray-800 transition shadow-sm">
            Accept & Close
        </button>

    </div>

</div>

    </form>
</div>


<script>
function openCloseNegotiationDrawer() {
    document.getElementById('close-negotiation-overlay')
        .classList.remove('hidden');

    document.getElementById('close-negotiation-drawer')
        .classList.remove('translate-x-full');
}

function closeCloseNegotiationDrawer() {
    document.getElementById('close-negotiation-overlay')
        .classList.add('hidden');

    document.getElementById('close-negotiation-drawer')
        .classList.add('translate-x-full');
}

// click outside
document.getElementById('close-negotiation-overlay')
    .addEventListener('click', closeCloseNegotiationDrawer);
</script>



@endsection