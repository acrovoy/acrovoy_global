{{-- CUSTOM ATTRIBUTES WORKSPACE COMPONENT --}}

@php
$existing = $rfq->attributeValues ?? collect();
@endphp

<div class="mt-8 pt-6 border-t border-gray-200">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-3">

        <div>
            <div class="text-sm font-semibold text-gray-800">
                Custom attributes
            </div>

            <div class="text-xs text-gray-500">
                Advanced RFQ attributes (type, options, rules)
            </div>
        </div>

        <button type="button"
            onclick="openAttributeDrawer()"
            class="text-sm text-gray-600 hover:text-gray-900">
            + Add attribute
        </button>

    </div>

    {{-- LIST --}}
    <div class="space-y-2">

 @foreach($existing as $value)

            @php
                $attribute = $value->attribute;
            @endphp

            <div class="p-3 border border-gray-100 rounded-lg bg-gray-50 flex items-center justify-between">

                {{-- NAME --}}
                <div class="text-sm font-medium text-gray-800">
                    {{ $attribute?->name }}
                </div>

                {{-- VALUE --}}
                <div class="text-xs text-gray-500">

                    @if($attribute->type === 'text' || $attribute->type === 'number')
                        {{ $value->value_text ?? $value->value_number ?? '—' }}

                    @elseif($attribute->type === 'select')

    {{ $attribute->options
        ->firstWhere('id', $value->attribute_option_id)
        ?->translations
        ->firstWhere('locale', app()->getLocale())
        ?->value
        ?? '—'
    }}

                    @elseif($attribute->type === 'multiselect')

    {{ $value->options
        ->map(function($opt) {
            return $opt?->translations
                ->firstWhere('locale', app()->getLocale())
                ?->value;
        })
        ->filter()
        ->implode(', ')
    }}
                    @else
                        —
                    @endif

                </div>

                {{-- EDIT --}}
                <button type="button"
                        onclick="openAttributeDrawer({{ $attribute->id }}, this)"
                        class="text-xs text-gray-500 hover:text-gray-800">
                    Edit
                </button>

            </div>

        @endforeach

</div>
</div>


{{-- OVERLAY --}}
<div id="attribute-overlay"
    class="fixed inset-0 bg-black/40 hidden z-40"></div>




<script>
    let optionIndex = 0;

    /*
    |--------------------------------------------------------------------------
    | OPEN DRAWER (CREATE / EDIT)
    |--------------------------------------------------------------------------
    */
    function openAttributeDrawer(id = null, btn = null) {

        document.getElementById('attribute-overlay').classList.remove('hidden');
        document.getElementById('attribute-drawer').classList.remove('translate-x-full');

        const title = document.getElementById('attribute-title');

        // reset form
        document.getElementById('attr-id').value = '';
        document.getElementById('attr-key').value = '';
        document.getElementById('attr-value').value = '';
        document.getElementById('attr-type').value = 'text';

        document.getElementById('options-container').innerHTML = '';

        optionIndex = 0;

        /*
        |--------------------------------------------------------------------------
        | EDIT MODE (READ FROM ROW)
        |--------------------------------------------------------------------------
        */
        if (btn) {

            const row = btn.closest('.custom-row');

            const key = row.querySelector('input[name*="[key]"]')?.value ?? '';
            const type = row.querySelector('select[name*="[type]"]')?.value ?? 'text';
            const value = row.querySelector('input[name*="[value]"]')?.value ?? '';

            document.getElementById('attr-key').value = key;
            document.getElementById('attr-type').value = type;
            document.getElementById('attr-value').value = value;

            // options
            const optionInputs = row.querySelectorAll('input[name*="[options]"]');

            optionInputs.forEach(opt => {
                if (opt.value.trim() !== '') {
                    addDrawerOption(opt.value);
                }
            });

            title.innerText = 'Edit attribute';

        } else {

            title.innerText = 'Create attribute';
        }

        toggleDrawerOptions();
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE DRAWER
    |--------------------------------------------------------------------------
    */
    function closeAttributeDrawer() {
        document.getElementById('attribute-overlay').classList.add('hidden');
        document.getElementById('attribute-drawer').classList.add('translate-x-full');
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE UI
    |--------------------------------------------------------------------------
    */
    function toggleDrawerOptions() {

        const type = document.getElementById('attr-type').value;

        const options = document.getElementById('drawer-options');
        const value = document.getElementById('drawer-value');

        const isSelect = (type === 'select' || type === 'multiselect');

        if (options) options.classList.toggle('hidden', !isSelect);
        if (value) value.classList.toggle('hidden', isSelect);
    }

    /*
    |--------------------------------------------------------------------------
    | ADD OPTION
    |--------------------------------------------------------------------------
    */
    function addDrawerOption(val = '') {

        const container = document.getElementById('options-container');

        const row = document.createElement('div');
        row.className = "flex items-center gap-2";

        row.innerHTML = `
        <input type="text"
               name="options[]"
               value="${val}"
               class="w-full border rounded px-2 py-1 text-xs">

        <button type="button"
                onclick="this.parentElement.remove()"
                class="text-red-500 text-xs">
            ✕
        </button>
    `;

        container.appendChild(row);
    }

    /*
    |--------------------------------------------------------------------------
    | OVERLAY CLOSE
    |--------------------------------------------------------------------------
    */
    document.getElementById('attribute-overlay')
        .addEventListener('click', closeAttributeDrawer);
</script>