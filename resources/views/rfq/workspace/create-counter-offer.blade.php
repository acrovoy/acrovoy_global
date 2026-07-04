@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('rfq.partials.aside-panel', [
'rfq' => $rfq,
'activeTab' => 'offers'
])

@endsection

@section('dashboard-content')

<div class="grid grid-cols-12 gap-6">

    {{-- ========================================= --}}
    {{-- LEFT CONTENT --}}
    {{-- ========================================= --}}
    <div class="col-span-8">

        @php
        $supplier = $offer->participant;

        @endphp

        <div
            class="max-w-5xl mx-auto"
            data-rfq-id="{{ $rfq->id }}"
            data-offer-id="{{ $offer->id }}"
            data-offer-version-id="{{ $counterVersion?->id }}">



            @csrf
            @method('PUT')

            <div class="border rounded-lg overflow-hidden bg-white shadow-sm">

                {{-- HEADER --}}
                <div class="flex items-center justify-between p-4 bg-gray-50 border-b">

                    <div class="flex items-center gap-4">

                        <img
                            src="{{ $rfq->image ?? asset('images/no-photo.png') }}"
                            class="w-12 h-12 rounded-lg object-cover border">

                        <div>

                            <div class="text-lg font-semibold text-gray-900">
                                {{ $rfq->title }}
                            </div>

                            <div class="flex items-center gap-2">

                                <div class="text-sm text-gray-500">Supplier:</div>

                                <div class="text-sm font-medium text-gray-700">
                                    {{ $supplier?->name ?? 'Unknown supplier' }}
                                </div>
                                

                            </div>
                            <div class="text-red-500 text-xs">Awaiting your reply for supplier's offer</div>

                        </div>

                    </div>

                    

                </div>

                {{-- BODY --}}
                <div class="pt-4 pb-6 px-6">

                    {{-- TOP ACTIONS --}}
                    <div class="flex justify-end gap-3 mb-4 text-sm">

                        <div id="autosaveStatus" class="text-xs text-gray-400">

                        </div>
                        {{-- =========================================
                                    DELETE DRAFT (only editable)
                                ========================================= --}}

                        <form method="POST"
                            action="{{ route('buyer.rfqs.counter.delete', [
                                        'rfq' => $rfq->id,
                                        'offer' => $offer->id,
                                        'version' => $counterVersion->id
                                    ]) }}"
                            onsubmit="return confirm('Delete this draft version?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="px-4 py-1 border rounded-lg border-red-200 text-red-300 hover:bg-red-50 hover:text-red-500">
                                Delete draft
                            </button>
                        </form>



                        {{-- =========================================
                                CHAT (only when editable or submitted)
                            ========================================= --}}
                        <button type="button"
                            class="px-4 py-1 border border-gray-500 text-gray-500 rounded-lg opacity-50 hover:bg-gray-50 hover:text-gray-800">
                            Chat with Supplier
                        </button>

                        <form method="POST"
                            action="{{ route('buyer.rfqs.counter.submit', [
                                    'rfq' => $rfq->id,
                                    'version' => $counterVersion->id
                                ]) }}">

                            @csrf

                            <button
                                type="submit"
                                class="px-5 py-1 rounded-lg bg-gray-700 text-white hover:bg-gray-800 text-sm font-medium">
                                Submit Counter Offer
                            </button>

                        </form>

                    </div>

                    {{-- REQUIREMENTS --}}
                    <div class="border rounded-xl p-5">

                        <div class=" mb-4">

                            <div>
                                <div class="flex justify-between text-xs uppercase text-gray-500 font-medium">
                                    <div>Buyer Counter Proposal</div>

                                    <div class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-[10px] font-medium">
                                        Editable
                                    </div>
                                </div>


                            </div>
                            <div class="font-medium mb-3">
                                Negotiation Terms
                            </div>


                        </div>

                        {{-- REQUIREMENT ITEMS --}}
                        @foreach($rfq->attributeValues as $value)

                        <div data-attribute-id="{{ $value->attribute->id }}">
                            @include('rfq.workspace.components.buyer-counter-offer', [
                            'value' => $value,
                            'itemsByAttribute' => $itemsByAttribute,
                            'isReadonly' => false,
                            'offerVersion' => $counterVersion,
                            'counterItemsByAttribute' => $counterItemsByAttribute,
                            ])
                        </div>

                        @endforeach

                    </div>

                    {{-- ========================================= --}}
{{-- GRAND TOTAL --}}
{{-- ========================================= --}}

<div class="p-4 border border-gray-200 rounded-xl bg-white mb-6 mt-6">

    <div class="text-sm font-semibold text-gray-900 mb-4">
        Grand Total
    </div>

    {{-- ========================================= --}}
    {{-- SUPPLIER OFFER (READ ONLY) --}}
    {{-- ========================================= --}}

    <div class="mb-4 p-3 bg-gray-200 border border-gray-100 rounded-lg">

        <div class="text-xs text-gray-500 mb-2">
            Supplier offer
        </div>

        <div class="flex items-center justify-between">

            <div class="text-sm text-gray-700">
                Total amount
            </div>

            <div class="text-base font-semibold text-gray-900">
                {{ $offerVersion?->total_price
                    ? number_format($offerVersion->total_price, 2)
                    : '—'
                }}
                <span class="text-xs font-normal text-gray-500">USD</span>
            </div>

        </div>

    </div>

    {{-- ========================================= --}}
    {{-- BUYER COUNTER --}}
    {{-- ========================================= --}}

    <div>

        <div class="text-xs text-blue-600 mb-2">
            Your counter offer
        </div>

        <div class="flex items-end justify-between">

            <div class="text-xs text-gray-500">
                Your proposed total amount
            </div>

            <div class="flex flex-col items-end">

                <div class="text-[11px] text-gray-400 mb-1">
                    USD
                </div>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="total_price"
                    value="{{ old('total_price', $counterVersion?->total_price) }}"
                    data-autosave
                    data-field="total_price"
                    placeholder="0.00"
                    class="w-40 border border-blue-200 rounded-lg px-3 py-2 text-sm text-right
                           focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">

            </div>

        </div>

    </div>

</div>



                    {{-- ATTACHMENTS --}}
                    <div class="border rounded-xl p-5 mt-6">

                        <div class="text-lg font-semibold text-gray-900">
                            Attachments
                        </div>

                        <div class="text-sm text-gray-500 mt-1">
                            Upload negotiation files, revised specifications or commercial documents.
                        </div>

                        <div class="border-2 border-dashed rounded-xl p-8 text-center mt-4">

                            <div class="text-sm font-medium text-gray-700">
                                Upload files
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                PDF, XLSX, DOCX, ZIP up to 25MB
                            </div>

                            <input
                                type="file"
                                name="attachments[]"
                                multiple
                                class="mt-4">

                        </div>

                    </div>

                </div>

            </div>



        </div>

    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-span-4">

        @include('rfq.partials.buyer-offer-history-panel', [
        'versions' => $versions ?? collect(),
        'counterVersion' => $counterVersion ?? null,
        ])

    </div>

</div>




<script>
document.addEventListener('DOMContentLoaded', () => {

    let autosaveTimer = null;

    const autosaveStatus = document.getElementById('autosaveStatus');

    const rfqId = document.querySelector('[data-rfq-id]')?.dataset?.rfqId;
    const offerId = document.querySelector('[data-offer-id]')?.dataset?.offerId;
    const versionId = document.querySelector('[data-offer-version-id]')?.dataset?.offerVersionId;

    const fields = document.querySelectorAll(
        'input[name="total_price"], input[name^="notes"], input[name^="unit_price"], input[name^="option_id"], input[name^="option_ids"]'
    );

    fields.forEach(field => {

        const eventType = (field.type === 'checkbox' || field.type === 'radio')
            ? 'change'
            : 'input';

        field.addEventListener(eventType, () => {

            clearTimeout(autosaveTimer);

            autosaveStatus.innerText = 'Saving...';

            autosaveTimer = setTimeout(() => {


                if (field.name === 'total_price') {

                    fetch(`/dashboard/buyer/rfqs/${rfqId}/offers/${offerId}/versions/${versionId}/autosave`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            total_price: field.value
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        autosaveStatus.innerText = res.ok ? 'Saved' : 'Error';
                        setTimeout(() => autosaveStatus.innerText = 'Ready', 1200);
                    });

                    return;
                }


                const wrapper = field.closest('[data-attribute-id]');
                if (!wrapper) return;

                const attributeId = wrapper.dataset.attributeId;

                const payload = {
                    attribute_id: attributeId
                };

                // NOTES
                const notes = wrapper.querySelector(`input[name="notes[${attributeId}]"]`);
                if (notes) payload.notes = notes.value;

                // PRICE
                const price = wrapper.querySelector(`input[name="unit_price[${attributeId}]"]`);
                if (price) payload.unit_price = price.value;

                // OPTION SINGLE
                const option = wrapper.querySelector(`input[name="option_id[${attributeId}]"]:checked`);
                if (option) payload.option_id = option.value;

                // OPTION MULTI
                const options = wrapper.querySelectorAll(`input[name="option_ids[${attributeId}][]"]:checked`);
                payload.option_ids = Array.from(options).map(i => i.value);

                fetch(`/dashboard/buyer/rfqs/${rfqId}/offers/${offerId}/versions/${versionId}/autosave`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    autosaveStatus.innerText = res.ok ? 'Saved' : 'Error';
                    setTimeout(() => autosaveStatus.innerText = 'Ready', 1200);
                })
                .catch(() => {
                    autosaveStatus.innerText = 'Connection error';
                });

            }, 400);

        });

    });

});
</script>

@endsection