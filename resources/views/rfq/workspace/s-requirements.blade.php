@extends('dashboard.layout')

@section('dashboard-content')

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
                    class="w-10 h-10 rounded object-cover">

                <div class="text-sm">
                    Новая модель стола для производства
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm">
                <span class="px-2 py-1 bg-gray-200 rounded">Published</span>

                <span class="text-red-500 text-xs">
                    Awaiting your reply for supplier's offer
                </span>
            </div>
        </div>

        <div class="p-5 bg-white">

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 mb-4 text-sm">

                <button type="button" class="px-4 py-1 border rounded">
                    Chat
                </button>

                <button type="button" class="px-4 py-1 border rounded">
                    Save Draft
                </button>

                <button type="button"
                    id="submit-offer"
                    class="px-4 py-1 bg-black text-white rounded">
                    Submit Offer for this Item
                </button>

            </div>

            {{-- REQUIREMENT --}}
<div class="border rounded-lg p-4 mb-6">

    <div class="font-medium mb-3">
        General conditions
    </div>

    @foreach($rfq->attributeValues as $value)

        @include('rfq.workspace.components.supplier-requirement', [
            'value' => $value,
            'itemsByAttribute' => $itemsByAttribute
        ])

    @endforeach

    @include('rfq.workspace.components.specifications-custom', [
        'rfq' => $rfq,
        'itemsByRequirement' => $itemsByRequirement
    ])

</div>

            {{-- ATTACHMENTS --}}
            <div class="border rounded-lg p-4 mb-6">

                <div class="font-medium mb-2">Attachments</div>

                <div class="text-xs text-gray-500 mb-3">
                    Upload relevant files including technical drawings...
                </div>

                <div class="flex items-center gap-3">

                    

                    <input type="file"
                        multiple
                        data-autosave-file
                        class="w-14 h-14 border-dashed border rounded flex items-center justify-center">

                </div>

            </div>

            {{-- DELIVERY --}}
            <div>

                <div class="font-medium mb-3">Delivery Services</div>

                <div class="flex gap-2 mb-4">

                    <input
                        type="text"
                        name="delivery[from]"
                        data-autosave
                        data-requirement-id="delivery_from"
                        value="Buenos Aires, Argentina"
                        class="border p-2 rounded w-full">

                    <input
                        type="text"
                        name="delivery[to]"
                        data-autosave
                        data-requirement-id="delivery_to"
                        value="Buenos Aires, Argentina"
                        class="border p-2 rounded w-full">

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

    /**
     * TEXT INPUTS
     */
    document.addEventListener('input', (e) => {

        const el = e.target;

        if (!el.hasAttribute('data-autosave')) return;

        /**
         * =========================
         * PRICE (ОТДЕЛЬНО)
         * =========================
         */
        if (el.dataset.field === 'price') {

            autosave(el.name, {
                requirement_id: el.dataset.requirementId,
                unit_price: el.value
            });

            return;
        }

        /**
         * =========================
         * DEFAULT (notes / text)
         * =========================
         */
        autosave(el.name, {
            requirement_id: el.dataset.requirementId,
            field: el.name,
            value: el.value
        });
    });


    /**
     * CHECKBOX / RADIO / SELECT
     */
    document.addEventListener('change', (e) => {

        const el = e.target;

        if (!el.hasAttribute('data-autosave')) return;

        /**
         * =========================
         * MULTISELECT (checkbox)
         * =========================
         */
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

        /**
         * =========================
         * SELECT (radio)
         * =========================
         */
        if (el.type === 'radio') {

            autosave(el.name, {
                requirement_id: el.dataset.requirementId,
                option_id: el.value
            });

            return;
        }

        /**
         * =========================
         * DEFAULT
         * =========================
         */
        autosave(el.name, {
            requirement_id: el.dataset.requirementId,
            field: el.name,
            value: el.value
        });
    });


    /**
     * FILE UPLOAD
     */
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


    /**
     * FINAL SUBMIT
     */
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