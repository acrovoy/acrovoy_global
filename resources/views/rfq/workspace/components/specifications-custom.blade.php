@php
$existing = $rfq->customAttributes ?? collect();
@endphp

@if($existing->count())

<div class="p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition mb-6">

    <div class="font-medium text-sm text-gray-800 mb-3">
        Additional specifications
    </div>

    <div class="space-y-3">

        @foreach($existing as $attr)

            @php
                $requirementId = $attr->id;
                $item = $itemsByRequirement[$attr->id] ?? null;
            @endphp

            <div class="border rounded-md p-3 bg-white">

                <div class="flex items-start gap-2 text-sm mb-3">

                    <div class="min-w-[140px] font-medium text-gray-800">
                        {{ $attr->key }}:
                    </div>

                    <div class="text-gray-600">
                        {{ $attr->value }}
                    </div>

                </div>

                {{-- NOTES --}}
                <textarea
                    data-custom-autosave
                    data-requirement-id="{{ $requirementId }}"
                    data-field="notes"
                    class="w-full border rounded p-2 mb-3 text-sm"
                    placeholder="Notes"
                >{{ $item->notes ?? '' }}</textarea>

                {{-- PRICE --}}
                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 border-dashed border rounded flex items-center justify-center text-gray-400">
                        +
                    </div>

                    <input
                        type="text"
                        data-custom-autosave
                        data-requirement-id="{{ $requirementId }}"
                        data-field="price"
                        value="{{ $item->unit_price ?? '' }}"
                        class="ml-auto border rounded px-3 py-1 w-32 text-sm"
                        placeholder="Price"
                    >

                </div>

            </div>

        @endforeach

    </div>

</div>

@endif

<script>
let customTimers = {};

function getRfqId() {
    return document.querySelector('[data-rfq-id]').dataset.rfqId;
}

function customAutosave(requirementId, payload) {

    clearTimeout(customTimers[requirementId]);

    customTimers[requirementId] = setTimeout(() => {

        fetch(`/dashboard/supplier/rfqs/${getRfqId()}/custom-autosave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                requirement_id: requirementId,
                ...payload
            })
        });

    }, 600);
}

/**
 * INPUT HANDLER
 */
document.addEventListener('input', (e) => {

    const el = e.target;

    if (!el.hasAttribute('data-custom-autosave')) return;

    customAutosave(
        el.dataset.requirementId,
        {
            field: el.dataset.field,
            value: el.value
        }
    );
});
</script>