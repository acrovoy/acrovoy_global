{{-- CUSTOM ATTRIBUTES WORKSPACE COMPONENT --}}

@php
$existing = $rfq->customAttributeValues ?? collect();
$rfqStatus = $rfq->status;
$isReadonly = $rfqStatus->isPublished() || $rfqStatus->isClosed();

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

        @if(!$isReadonly)
            <button type="button"
                onclick="openPickerDrawer()"
                class="text-sm text-gray-600 hover:text-gray-900">
                + Attach attribute
            </button>
        @else
            <span class="text-xs text-gray-400">
                Read-only mode
            </span>
        @endif

      

    </div>

    {{-- LIST --}}
    <div class="space-y-2">

    

<div class="space-y-5">

@foreach($attachedAttributes as $group => $attributes)

    <div class="mb-4">

       

        <div class="space-y-3">

            @foreach($attributes as $attribute)

            

                @include('rfq.workspace.components.attribute-field', [
                    'attribute' => $attribute,
                    'type' => $attribute->type,
                    'name' => "attributes[{$attribute->id}]",
                    'savedValue' => $attribute->saved_value,
                    'savedOptions' => $attribute->saved_options ?? [],
                    'isRequired' => $attribute->is_required,
                ])

            @endforeach

        </div>

    </div>

@endforeach

</div>



      

    </div>
</div>


{{-- OVERLAY --}}
<div id="global-overlay"
     class="fixed inset-0 bg-black/40 hidden z-40"></div>



<script>

  function openPickerDrawer() {
    closeAttributeDrawer();

    document.getElementById('attribute-picker-drawer')
        .classList.remove('translate-x-full');

    document.getElementById('global-overlay')
        .classList.remove('hidden');
  }

  function closePickerDrawer() {
    document.getElementById('attribute-picker-drawer')
        .classList.add('translate-x-full');

    document.getElementById('global-overlay')
        .classList.add('hidden');
  }

  /*
  |----------------------------------------------------------------------
  | OVERLAY CLICK (FIXED - ONLY ONCE, NOT INSIDE FUNCTION)
  |----------------------------------------------------------------------
  */
  document.getElementById('global-overlay')
      .addEventListener('click', closeAllDrawers);

</script>


<script>
    let optionIndex = 0;

    /*
    |--------------------------------------------------------------------------
    | OPEN DRAWER (CREATE / EDIT)
    |--------------------------------------------------------------------------
    */
    function openAttributeDrawer(id = null, btn = null) {

       

        closePickerDrawer(); // 👈 ВАЖНО

        document.getElementById('global-overlay').classList.remove('hidden');
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
        |----------------------------------------------------------------------
        | EDIT MODE (READ FROM ROW)
        |----------------------------------------------------------------------
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
        document.getElementById('attribute-drawer')
            .classList.add('translate-x-full');
    }

    function closePickerDrawer() {
        document.getElementById('attribute-picker-drawer')
            .classList.add('translate-x-full');

        document.getElementById('global-overlay')
            .classList.add('hidden');
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE ALL DRAWERS (FIXED - NO EVENT LISTENER INSIDE)
    |--------------------------------------------------------------------------
    */
    function closeAllDrawers() {
    document.getElementById('attribute-drawer')
        ?.classList.add('translate-x-full');

    document.getElementById('attribute-picker-drawer')
        ?.classList.add('translate-x-full');

    document.getElementById('global-overlay')
        .classList.add('hidden');
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
    | OVERLAY CLOSE (attribute drawer support if needed)
    |--------------------------------------------------------------------------
    */
    document.getElementById('attribute-overlay')
        ?.addEventListener('click', closeAttributeDrawer);

</script>