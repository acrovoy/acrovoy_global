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


                        @if($activeVersion->id === $lastsubmittedVersion->id && !$existingDraftCounter)


                        <a
                            href=""
                            class="px-4 py-1 border rounded bg-white hover:bg-gray-50">
                            Close negotiation
                        </a>



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



                            <div class="flex items-center gap-3">



                            </div>

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

@endsection