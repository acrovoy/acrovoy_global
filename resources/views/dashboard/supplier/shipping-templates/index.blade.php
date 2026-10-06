@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Shipping Center</h2>
            <p class="text-sm text-gray-500">
                Manage all your shipping templates and assign countries, prices and delivery times
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('supplier.warehouses.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2
          text-sm font-medium text-gray-700
          bg-white border border-gray-200
          rounded-lg
          hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
          active:scale-[0.98]
          transition-all duration-150 shadow-sm">

    <span>Manage warehouses</span>

</a>

            <a href="{{ route('supplier.shipping-templates.create') }}"
   class="inline-flex items-center gap-2 px-4 py-2
          text-sm font-medium text-gray-700
          bg-white border border-gray-200
          rounded-lg
          hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
          active:scale-[0.98]
          transition-all duration-150 shadow-sm">

    <span class="text-lg leading-none">+</span>
    <span>Add New Template</span>

</a>
        </div>





    </div>

    <x-alerts />

    {{-- Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600">
    <tr>
        <th class="w-[400px] min-w-[400px] max-w-[380px] px-5 py-3 text-left font-medium">Shipping Details</th>
        <th class="px-5 py-3 text-left font-medium">Place of Loading</th>
        <th class="px-5 py-3 text-left font-medium">Delivery Destination</th>
        <th class="px-5 py-3 text-left font-medium">Actions</th>
    </tr>
</thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50 transition">
                    <td class="w-[400px] min-w-[400px] max-w-[380px] px-5 py-3 align-top">
    <div class="min-w-0">

        {{-- Title + Price --}}
        <div class="flex items-start justify-between gap-4">

        <div>
            <span class="text-sm font-semibold text-gray-900 leading-snug">
                {{ $template->title }}
            </span>
            <div class="text-sm text-gray-500 leading-snug">
                {{ $template->description }}
            </div>
        </div>
            <span class="shrink-0 text-sm font-semibold text-gray-900">
                ${{ number_format($template->price, 2) }}
                {{-- Price Unit --}}
        @if($template->price_unit_label)
            <div class="mt-1 text-xs text-gray-400">
                {{ $template->price_unit_label }}
            </div>
        @endif
            </span>
            

        </div>


        


        {{-- Delivery Time --}}
        @if($template->delivery_time)
            <div class="mt-1 text-xs text-gray-500 leading-snug">
                Delivery time: {{ $template->delivery_time }}
            </div>
        @endif

        {{-- Delivery Type + Incoterm --}}
        <div class="mt-2 flex flex-wrap items-center gap-2">

            @php
                $deliveryTypes = [
                    'self_pickup' => 'Self Pickup',
                    'curbside_delivery' => 'Curbside Delivery',
                    'door_to_door' => 'Door-to-Door Delivery',
                    'white_glove' => 'White Glove Delivery',
                    'delivery_assembly' => 'Delivery & Assembly',
                    'delivery_installation' => 'Delivery & Installation',
                    'custom' => 'Custom Delivery',
                ];

                $incoterms = [
                    'EXW' => 'EXW',
                    'FCA' => 'FCA',
                    'CPT' => 'CPT',
                    'CIP' => 'CIP',
                    'DAP' => 'DAP',
                    'DPU' => 'DPU',
                    'DDP' => 'DDP',
                ];
            @endphp

            @if($template->delivery_type)
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-900">
                    {{ $deliveryTypes[$template->delivery_type] ?? $template->delivery_type }}
                </span>
            @endif

            @if($template->incoterm)
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-700">
                    {{ $incoterms[$template->incoterm] ?? $template->incoterm }}
                </span>
            @endif

        </div>

        

    </div>
</td>
                    

    

    <td class="px-5 py-3 text-gray-700 align-top">

    @if(empty($template->warehouse))

        <button
            class="open-warehouse-drawer text-xs px-3 py-1.5 rounded-lg border border-gray-300
                   text-gray-600 hover:bg-gray-100 transition"
            data-template-id="{{ $template->id }}">
            Link warehouse
        </button>

    @else

        <div class="space-y-1">

            

            <button
    class="open-warehouse-drawer hover:text-gray-100 transition"
    data-template-id="{{ $template->id }}">

    <div class="text-sm font-medium text-blue-800">
        {{ $template->warehouse->name ?? 'Warehouse #' . $template->warehouse->id }}
    </div>

    <div class="text-xs text-gray-500">

        {{-- Регион --}}
        @if($template->warehouse?->location)
            {{ $template->warehouse->location->parent?->name ?? $template->warehouse->location->name }}
        @endif

        {{ $template->warehouse->location->name ?? '' }}

        {{-- Адрес --}}
        @if($template->warehouse?->address)
            · {{ $template->warehouse->address }}
        @endif

    </div>

</button>

        </div>

    @endif

</td>


                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-1">
                            @foreach($template->locations as $location)
                            @php
                            // Если есть parent_id — это город, иначе регион
                            $isCity = $location->parent_id !== null;
                            $bgColor = $isCity ? 'bg-green-200' : 'bg-blue-100';
                            $textColor = $isCity ? 'text-green-800' : 'text-blue-700';
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $bgColor }} {{ $textColor }}">
                                {{ $location->name }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-5 py-3 text-right whitespace-nowrap space-x-2 align-top">

    {{-- ACTIVATE / DEACTIVATE --}}
    <form action="{{ route('supplier.shipping-templates.toggle-active', $template) }}"
          method="POST"
          class="inline">
        @csrf

        <button
            class="px-3 py-1.5 text-xs rounded-lg border transition
            {{ $template->is_active
                ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'
                : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' }}">

            {{ $template->is_active ? 'Deactivate' : 'Activate' }}

        </button>
    </form>
<div class="mt-2">
    {{-- EDIT --}}
    <a href="{{ route('supplier.shipping-templates.edit', $template) }}"
       class="text-sm text-gray-700 hover:underline ml-2">
        Edit
    </a>

   {{-- DELETE --}}
<form action="{{ route('supplier.shipping-templates.destroy', $template) }}"
      method="POST"
      class="inline delete-template-form">

    @csrf
    @method('DELETE')

    <button
        type="submit"
        class="text-sm text-red-600 hover:underline ml-2"
    >
        Delete
    </button>

</form>
</div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                        No shipping templates found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- Warehouse Drawer Overlay --}}
<div id="warehouse-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="warehouse-drawer"
     class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300
            z-50 flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Link Warehouse
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Assign warehouses to shipping template
        </p>
    </div>

    {{-- Form --}}
    <form method="POST"
          id="warehouse-form"
          action=""
          class="flex flex-col flex-1">
        @csrf

        <input type="hidden" name="template_id" id="warehouse-template-id">

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-3">

            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                Warehouses
            </div>

            <div id="warehouse-list" class="space-y-3">
                {{-- JS inject --}}
            </div>

        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-4">

            {{-- Left --}}
            <button type="button"
                    id="warehouse-cancel"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-200
                           text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </button>

            {{-- Right --}}
            <div class="flex items-center gap-3">

                <div class="text-xs text-gray-400 whitespace-nowrap">
                    Saved immediately on submit
                </div>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save
                </button>

            </div>

        </div>

    </form>
</div>

<script>

const drawer = document.getElementById('warehouse-drawer');
const overlay = document.getElementById('warehouse-drawer-overlay');
const warehouseList = document.getElementById('warehouse-list');

const templateWarehouses = @json(
    $templates->mapWithKeys(fn ($t) => [
        $t->id => $t->warehouse_id
    ])
);

const attachWarehouseBaseUrl = @json(
    route('supplier.shipping-templates.attach-warehouse', ['template' => '__ID__'])
);

let currentTemplateId = null;


// ===============================
// GLOBAL CLICK HANDLER (SAFE)
// ===============================
document.addEventListener('click', function (e) {

    const btn = e.target.closest('.open-warehouse-drawer');
    if (!btn) return;

    e.preventDefault();

    const templateId = btn.getAttribute('data-template-id');

    // DEBUG (оставил как ты просил)
    console.log('CLICKED ELEMENT:', btn);
    console.log('DATASET:', btn.dataset);
    console.log('ATTR:', templateId);

    if (!templateId || templateId === 'undefined') {
        console.error('❌ Missing template ID on button');
        return;
    }

    currentTemplateId = templateId;

    const hiddenInput = document.getElementById('warehouse-template-id');
    if (hiddenInput) {
        hiddenInput.value = currentTemplateId;
    }

    openShippingTemplateDrawer();
    loadWarehouses(currentTemplateId);
});


// ===============================
// OPEN DRAWER
// ===============================
function openShippingTemplateDrawer() {
    overlay.classList.remove('hidden');
    drawer.classList.remove('translate-x-full');

    const form = document.getElementById('warehouse-form');

    if (form) {
        const url = attachWarehouseBaseUrl.replace('__ID__', currentTemplateId);
        form.action = url;
    }
}


// ===============================
// CLOSE DRAWER
// ===============================
function closeDrawer() {
    drawer.classList.add('translate-x-full');

    setTimeout(() => {
        overlay.classList.add('hidden');
        warehouseList.innerHTML = '';
        currentTemplateId = null;
    }, 200);
}


// ===============================
// CLOSE EVENTS
// ===============================
document.getElementById('warehouse-cancel').addEventListener('click', closeDrawer);
overlay.addEventListener('click', closeDrawer);


// ===============================
// DATA
// ===============================
const warehouses = @json($warehouses);


// ===============================
// LOAD WAREHOUSES
// ===============================
function loadWarehouses(templateId) {

    warehouseList.innerHTML = '';

    const selectedWarehouseId = templateWarehouses[templateId] ?? null;

    warehouses.forEach(wh => {

        const isChecked = selectedWarehouseId == wh.id;

        warehouseList.insertAdjacentHTML('beforeend', `
    <label class="flex items-start justify-between px-4 py-3 border rounded-lg cursor-pointer
        ${isChecked ? 'bg-gray-100 border-gray-400' : 'bg-white hover:bg-gray-50'}">

        <div class="flex flex-col">

            <span class="text-sm font-medium text-gray-900">
                ${wh.name}
            </span>

            <span class="text-xs text-gray-500">

                ${(() => {
                    let region = '';
                    let city = '';

                    if (wh.location) {
                        if (wh.location.parent) {
                            city = wh.location.name;
                            region = wh.location.parent.name;
                        } else {
                            region = wh.location.name;
                        }
                    }

                    let parts = [];

                    if (region) parts.push(region);
                    if (city) parts.push(city);

                    return parts.join(' · ');
                })()}

                ${wh.address ? `<div>${wh.address}</div>` : ''}

            </span>

        </div>

        <input type="radio"
    name="warehouse_id"
    value="${wh.id}"
    class="w-4 h-4 accent-black appearance-none rounded-full
           border border-gray-400 checked:bg-black checked:border-black
           focus:ring-0 focus:outline-none"
    ${isChecked ? 'checked' : ''}>

    </label>
`);
    });
}

</script>

<script>

document.addEventListener('DOMContentLoaded', function () {


    document.querySelectorAll('.delete-template-form')
        .forEach(form => {


            form.addEventListener('submit', function (event) {

                event.preventDefault();


                const currentForm = this;


                confirmModal.open({

                    type: 'danger',

                    title: 'Delete Shipping Template',

                    message: 'Are you sure you want to delete this template?',

                    description: 'This action cannot be undone.',

                    confirmText: 'Delete Template',

                    cancelText: 'Cancel',

                    onConfirm: () => {

                        currentForm.submit();

                    }

                });


            });


        });


});

</script>

@endsection