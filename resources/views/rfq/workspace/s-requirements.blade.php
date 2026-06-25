@extends('dashboard.layout')

@section('dashboard-content')

{{-- BACK --}}
    <a href="{{ route('supplier.rfqs.index') }}"
        class="text-sm text-gray-400 hover:text-gray-700 transition">
        ← Back to RFQs
    </a>

<div class="grid grid-cols-12 gap-6 mt-3">

    {{-- LEFT BIG BLOCK --}}
    <div class="col-span-8">

        <div class="max-w-5xl mx-auto"
            data-rfq-id="{{ $rfq->id }}"
            data-offer-version-id="{{ $offerVersion->id }}">

            @php $i = 1; @endphp

            <div class="border rounded-lg mb-3 overflow-hidden">

                {{-- HEADER --}}
                <div class="flex justify-between items-center p-3 bg-gray-50">

                    <div class="flex items-center gap-3">
                        <div class="w-5 font-semibold">{{ $i }}</div>

                        <img src="{{ $rfq->image ?? asset('images/no-photo.png') }}"
                            class="w-12 h-12 rounded-lg object-cover border">

                        <div class="text-lg font-semibold text-gray-900">
                            {{ $rfq->title }}
                            <div class="text-red-500 text-xs">Awaiting your reply for supplier's offer</div>
                        </div>


                    </div>

                </div>

                <div class="p-5 bg-white">

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-3 mb-4 text-sm">


                        {{-- =========================================
                            DELETE DRAFT (only editable)
                        ========================================= --}}
                        @if(!$isReadonly)
                        <form
                            method="POST"
                            action="{{ route('supplier.rfq.offers.versions.delete', [
                            'rfq' => $rfq->id,
                            'version' => $offerVersion->id,
                        ]) }}"
                            onsubmit="return confirm('Delete this draft version?')">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="px-4 py-1 border rounded-lg border-red-200 text-red-300 hover:bg-red-50 hover:text-red-500">
                                Delete draft
                            </button>
                        </form>



                        {{-- =========================================
                            CHAT (only when editable or submitted)
                        ========================================= --}}
                        <button type="button"
                            class="px-4 py-1 border border-gray-500 text-gray-500 rounded-lg opacity-50 hover:bg-gray-50 hover:text-gray-800
                            {{ $isReadonly ? 'opacity-50 cursor-not-allowed' : '' }}"
                            @disabled($isReadonly)>
                            Chat with Orderer
                        </button>




                        {{-- =========================================
                        SUBMIT OFFER
                    ========================================= --}}
                        <form
                            method="POST"
                            action="{{ route('supplier.rfq.offers.versions.submit', [
                            'rfq' => $rfq->id,
                            'version' => $offerVersion->id,
                        ]) }}">
                            @csrf

                            <button
                                type="submit"
                                @disabled($isReadonly)
                                class="px-5 py-1 rounded-lg bg-gray-700 text-white hover:bg-gray-800 text-sm font-medium
                                {{ $isReadonly
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-black text-white hover:bg-gray-800'
                                }}">
                                Submit Offer
                            </button>
                        </form>
                        @endif

                        {{-- =========================================
    CREATE NEW VERSION (REVISION)
========================================= --}}

@php
    $isReadonly = $rfq->status->isClosed();
@endphp

@if($canCreateRevision && !$isReadonly)
    <form method="POST"
        action="{{ route('supplier.rfq.offer.create-revision', $rfq) }}">
        @csrf

        <button type="submit"
            class="px-4 py-1 border rounded bg-white hover:bg-gray-50">
            Create New Version
        </button>
    </form>
@endif

                    </div>


                    @php
                    $isReadonly = $isReadonly ?? false;
                    @endphp


                    {{-- REQUIREMENT --}}
                    <div class="border rounded-lg p-4 mb-6">




                        @if($isReadonly)


                        <div class="flex justify-between text-xs text-gray-500 font-medium">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Submitted Version
                            </div>

                            <div class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                Read only
                            </div>
                        </div>

                        @else
                        <div class="flex justify-between text-xs text-gray-500 font-medium">
                            <div class="uppercase"> Supplier Proposal
                            </div>

                            {{-- FINAL CHECKBOX --}}
                            <label class="flex items-center gap-2 cursor-pointer select-none">

                                <input
                                    type="checkbox"
                                    name="is_final"
                                    value="1"
                                    @checked($offerVersion?->is_final)
                                class="w-4 h-4 rounded border-gray-300
                                text-gray-900 focus:ring-0 focus:outline-none"
                                >

                                <span class="text-[11px] text-gray-600">
                                    Mark as Final Offer
                                </span>

                            </label>


                        </div>
                        @endif




                        <div class="font-medium mb-3">
                            General conditions

                        </div>

                        @foreach($rfq->attributeValues as $value)

                        @include('rfq.workspace.components.supplier-requirement', [
                        'value' => $value,
                        'itemsByAttribute' => $itemsByAttribute
                        ])

                        @endforeach




                        {{-- ATTACHMENTS --}}

                        <div class="font-medium">
                            Attachments
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            Supplier uploaded files and technical documents
                        </div>
                        <div class="border rounded-lg p-4 mb-6 bg-gray-50 hover:bg-white transition">

                            <div class="flex items-center gap-3">

                                <div class="w-12 h-12 border rounded flex items-center justify-center text-gray-400">
                                    +
                                </div>

                            </div>

                        </div>


                        





                    </div>

                   

                    

                </div>

            </div>

        </div>

    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-span-4">

        @include('rfq.partials.offer-history-panel')

    </div>

</div>

{{-- ========================= --}}
{{-- AUTOSAVE ENGINE --}}
{{-- ========================= --}}

<script>
    let timers = {};

    function getContext() {
        const root = document.querySelector('[data-rfq-id]');
        return {
            rfq_id: root.dataset.rfqId,
            version_id: root.dataset.offerVersionId
        };
    }

    function autosave(key, payload) {

        clearTimeout(timers[key]);

        timers[key] = setTimeout(() => {

            const ctx = getContext();

            fetch(`/dashboard/supplier/rfqs/${ctx.rfq_id}/offer/autosave`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ...ctx,
                    ...payload
                })
            });

        }, 800);

    }

    document.addEventListener('input', (e) => {

        const el = e.target;

        if (!el.hasAttribute('data-autosave')) return;

        if (el.dataset.field === 'price') {

            autosave(el.name, {
                requirement_id: el.dataset.requirementId,
                unit_price: el.value
            });

            return;
        }

        autosave(el.name, {
            requirement_id: el.dataset.requirementId,
            field: el.name,
            value: el.value
        });

    });

    document.addEventListener('change', (e) => {

        const el = e.target;

        if (!el.hasAttribute('data-autosave')) return;

        if (el.type === 'checkbox') {

            const group = document.querySelectorAll(
                `[data-requirement-id="${el.dataset.requirementId}"][type="checkbox"]`
            );

            let values = [];

            group.forEach(cb => {
                if (cb.checked) values.push(cb.value);
            });

            autosave(el.name, {
                requirement_id: el.dataset.requirementId,
                option_ids: values
            });

            return;
        }

        if (el.type === 'radio') {

            autosave(el.name, {
                requirement_id: el.dataset.requirementId,
                option_id: el.value
            });

            return;
        }

        autosave(el.name, {
            requirement_id: el.dataset.requirementId,
            field: el.name,
            value: el.value
        });

    });

    document.addEventListener('change', (e) => {

        const el = e.target;

        if (!el.hasAttribute('data-autosave-file')) return;

        const ctx = getContext();

        const formData = new FormData();

        formData.append('rfq_id', ctx.rfq_id);
        formData.append('version_id', ctx.version_id);

        for (let file of el.files) {
            formData.append('files[]', file);
        }

        fetch(`/dashboard/supplier/rfqs/${ctx.rfq_id}/offer/autosave`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

    });

    document.getElementById('submit-offer')?.addEventListener('click', () => {

        const ctx = getContext();

        fetch(`/dashboard/supplier/rfqs/${ctx.rfq_id}/offer/autosave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(ctx)
        });

    });
</script>

@endsection