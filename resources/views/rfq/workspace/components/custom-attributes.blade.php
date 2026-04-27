{{-- CUSTOM ATTRIBUTES WORKSPACE COMPONENT --}}

@php
    $existing = $rfq->customAttributes ?? collect();
@endphp

<div class="mt-8 pt-6 border-t border-gray-200">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-3">

        <div>
            <div class="text-sm font-semibold text-gray-800">
                Custom attributes
            </div>

            <div class="text-xs text-gray-500">
                Key / Value pairs for additional RFQ specifications
            </div>
        </div>

        <button type="button"
                onclick="addCustomAttributeRow()"
                class="text-sm text-gray-600 hover:text-gray-900">
            + Add attribute
        </button>

    </div>

    {{-- CONTAINER --}}
    <div id="custom-attributes-container" class="space-y-3">

        {{-- EXISTING FROM DB --}}
        @foreach($existing as $i => $attr)

            <div class="p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition flex items-start gap-2 custom-row">

            <input type="hidden"
                name="custom_attributes[{{ $i }}][id]"
                value="{{ $attr->id }}">

                <div class="flex-1 grid grid-cols-2 gap-2">

                    <input type="text"
                           name="custom_attributes[{{ $i }}][key]"
                           value="{{ $attr->key }}"
                           placeholder="Key"
                           class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

                    <input type="text"
                           name="custom_attributes[{{ $i }}][value]"
                           value="{{ $attr->value }}"
                           placeholder="Value"
                           class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

                </div>

                {{-- DELETE --}}
                <button type="button"
                        onclick="markCustomAttributeDeleted(this)"
                        class="text-gray-400 hover:text-red-600 transition text-sm px-2 mt-2">
                    ✕
                </button>

                <input type="hidden"
                       name="custom_attributes[{{ $i }}][_delete]"
                       value="0">

            </div>

        @endforeach


        {{-- OLD INPUTS (validation errors) --}}
        @if(old('custom_attributes'))

            @foreach(old('custom_attributes') as $i => $item)

                <div class="p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition flex items-start gap-2 custom-row">

                    <div class="flex-1 grid grid-cols-2 gap-2">

                        <input type="text"
                               name="custom_attributes[{{ $i }}][key]"
                               value="{{ $item['key'] ?? '' }}"
                               placeholder="Key"
                               class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

                        <input type="text"
                               name="custom_attributes[{{ $i }}][value]"
                               value="{{ $item['value'] ?? '' }}"
                               placeholder="Value"
                               class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

                    </div>

                    <button type="button"
                            onclick="markCustomAttributeDeleted(this)"
                            class="text-gray-400 hover:text-red-600 transition text-sm px-2 mt-2">
                        ✕
                    </button>

                    <input type="hidden"
                           name="custom_attributes[{{ $i }}][_delete]"
                           value="0">

                </div>

            @endforeach

        @endif

    </div>
</div>

{{-- JS --}}
@once
<script>

let customIndex = 1000;

/*
|--------------------------------------------------------------------------
| ADD NEW ROW
|--------------------------------------------------------------------------
*/
function addCustomAttributeRow() {

    const container = document.getElementById('custom-attributes-container');

    const row = document.createElement('div');
    row.className = "p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition flex items-start gap-2 custom-row";

    row.innerHTML = `
        <div class="flex-1 grid grid-cols-2 gap-2">

            <input type="text"
                   name="custom_attributes[${customIndex}][key]"
                   placeholder="Key"
                   class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

            <input type="text"
                   name="custom_attributes[${customIndex}][value]"
                   placeholder="Value"
                   class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">

        </div>

        <button type="button"
                onclick="removeCustomRow(this)"
                class="text-gray-400 hover:text-red-600 transition text-sm px-2 mt-2">
            ✕
        </button>

        <input type="hidden"
               name="custom_attributes[${customIndex}][_delete]"
               value="0">
    `;

    container.appendChild(row);

    customIndex++;
}

/*
|--------------------------------------------------------------------------
| SOFT DELETE (DB rows)
|--------------------------------------------------------------------------
*/
function markCustomAttributeDeleted(btn) {



    const row = btn.closest('.custom-row');

    const deleteInput = row.querySelector('input[name*="_delete"]');

    if (deleteInput) {
        deleteInput.value = "1";
    }

    // НЕ блокируем pointerEvents
    // НЕ делаем disabled UI

    row.classList.add('opacity-40');
}

/*
|--------------------------------------------------------------------------
| REMOVE NEW ROW (not saved yet)
|--------------------------------------------------------------------------
*/
function removeCustomRow(btn) {
    btn.closest('.custom-row').remove();
}

</script>
@endonce