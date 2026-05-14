@extends('dashboard.layout')

@section('dashboard-content')

<div class="grid grid-cols-12 gap-6">

    {{-- LEFT BIG BLOCK --}}
    <div class="col-span-8">

       

        @php
            $supplier = $offer->participant;
            $isReadonly = true;
        @endphp

        <div class="max-w-5xl mx-auto"
            data-rfq-id="{{ $rfq->id }}"
            data-offer-version-id="{{ $offerVersion->id }}">

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

    <button class="px-4 py-1 border rounded bg-white hover:bg-gray-50">
        Chat
    </button>

    <button class="px-4 py-1 border rounded bg-black text-white">
        Create Counter Offer
    </button>

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

                        <div class="font-medium mb-3">
                            General conditions
                        </div>

                        @foreach($rfq->attributeValues as $value)

                            @include('rfq.workspace.components.supplier-offer', [
                                'value' => $value,
                                'itemsByAttribute' => $itemsByAttribute,
                                'isReadonly' => true
                            ])

                        @endforeach

                    </div>

                    {{-- ATTACHMENTS --}}
                    <div class="border rounded-lg p-4 mb-6">

                        <div class="font-medium mb-2">
                            Attachments
                        </div>

                        <div class="text-xs text-gray-500 mb-3">
                            Supplier uploaded files and technical documents
                        </div>

                        <div class="flex items-center gap-3">

                            <div class="w-12 h-12 border rounded flex items-center justify-center text-gray-400">
                                📎
                            </div>

                        </div>

                    </div>

                    {{-- DELIVERY --}}
                    <div>

                        <div class="font-medium mb-3">
                            Delivery Services
                        </div>

                        <div class="flex gap-2 mb-4">

                            <input
                                type="text"
                                value="Buenos Aires, Argentina"
                                class="border p-2 rounded w-full bg-gray-50"
                                readonly>

                            <input
                                type="text"
                                value="Buenos Aires, Argentina"
                                class="border p-2 rounded w-full bg-gray-50"
                                readonly>

                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            @for($k = 0; $k < 2; $k++)

                                <div class="border rounded-lg p-4">

                                    <div class="font-medium mb-1">
                                        Delivery by Acrovoy
                                    </div>

                                    <div class="text-sm text-gray-500 mb-3">
                                        Delivery handled by platform
                                    </div>

                                    <div class="bg-blue-100 text-blue-700 px-3 py-2 rounded w-fit">
                                        Price: $0.00
                                    </div>

                                </div>

                            @endfor

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