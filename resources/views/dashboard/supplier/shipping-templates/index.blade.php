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
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full table-fixed divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600">
    <tr>
        <th class="w-[42%] px-4 py-3 text-left font-medium">Shipping Details</th>
        <th class="w-[25%] px-4 py-3 text-left font-medium">Place of Loading</th>
        <th class="w-[33%] px-4 py-3 text-left font-medium">Delivery Destinations</th>
    </tr>
</thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50 transition">
                    <td class="w-[400px] min-w-[400px] px-5 py-4 align-top">
    <div class="min-w-0">

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold leading-5 text-stone-900">
                {{ $template->title }}
            </span>

            @if($template->is_default)
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-100">
                    Default
                </span>
            @endif

            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                {{ $template->is_active
                    ? 'bg-green-50 text-green-700'
                    : 'bg-stone-100 text-stone-500' }}">
                {{ $template->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @if($template->description)
            <p class="mt-1 text-xs leading-5 text-stone-500">
                {{ $template->description }}
            </p>
        @endif

        @if($template->deliveryOptions->isNotEmpty())
            <div class="mt-3 space-y-2 border-t border-stone-100 pt-3">
                @foreach($template->deliveryOptions as $option)
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                            <span class="inline-flex rounded-md bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-800">
                                {{ $option->deliveryType?->name ?? $option->deliveryType?->code ?? 'Delivery' }}
                            </span>

                            @if($option->incoterm)
                                <span class="inline-flex rounded-md bg-stone-100 px-2 py-1 text-[10px] font-medium text-stone-600">
                                    {{ $option->incoterm }}
                                </span>
                            @endif
                        </div>

                        <div class="shrink-0 text-right">
                            @if($option->price !== null)
                                <div class="text-sm font-semibold tabular-nums text-stone-900">
                                    ${{ number_format((float) $option->price, 2) }}
                                </div>
                                <div class="text-[10px] text-stone-400">
                                    {{ $option->price_unit_label }}
                                </div>
                            @else
                                <span class="text-xs text-stone-400">
                                    Price on request
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-3 text-xs text-stone-400">
                No delivery options configured
            </p>
        @endif

        @if($template->delivery_time)
            <div class="mt-3 flex items-center gap-1.5 text-xs text-stone-500">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-3.5 w-3.5 shrink-0 text-stone-400"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.7"
                     aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 7v5l3 2"/>
                </svg>
                <span>Delivery time: {{ $template->delivery_time }}</span>
            </div>
        @endif

    </div>
</td>
                    

    

    <td class="w-[240px] min-w-[240px] px-5 py-4 align-top">
    @if(empty($template->warehouse))
        <button
            type="button"
            class="open-warehouse-drawer inline-flex items-center gap-2 rounded-lg border border-dashed border-stone-300 bg-white px-3 py-2 text-xs font-medium text-stone-600 transition hover:border-stone-400 hover:bg-stone-50 hover:text-stone-900"
            data-template-id="{{ $template->id }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.7"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 5v14m-7-7h14"/>
            </svg>
            Link warehouse
        </button>
    @else
        <button
            type="button"
            class="open-warehouse-drawer group block max-w-full text-left"
            data-template-id="{{ $template->id }}"
        >
            <span class="block text-sm font-medium leading-5 text-stone-800 transition group-hover:text-blue-700">
                {{ $template->warehouse->name ?? 'Warehouse #' . $template->warehouse->id }}
            </span>

            <span class="mt-1 block text-xs leading-5 text-stone-500">
                @if($template->warehouse->location)
                    {{ $template->warehouse->location->parent?->name ?? $template->warehouse->location->name }}
                    @if($template->warehouse->location->parent)
                        <span class="text-stone-300">/</span>
                        {{ $template->warehouse->location->name }}
                    @endif
                @endif

                @if($template->warehouse->country)
                    <span class="text-stone-300">·</span>
                    {{ $template->warehouse->country->name }}
                @endif
            </span>

            @if($template->warehouse->address)
                <span class="mt-1 block break-words text-[11px] leading-4 text-stone-400">
                    {{ $template->warehouse->address }}
                </span>
            @endif
        </button>
    @endif
</td>


                    <td class="w-[240px] min-w-[240px] max-w-[240px] px-5 py-3 align-top">

                    <div class="flex items-start justify-end gap-3 mb-4">

        {{-- Creation date --}}
        <span
            class="text-[10px] lowercase tracking-wide text-stone-400 whitespace-nowrap"
            title="{{ $template->created_at?->format('Y-m-d H:i') }}"
        >
            created {{ $template->created_at?->format('d M Y') }}
        </span>

        {{-- SHIPPING TEMPLATE ACTIONS --}}
        <details class="product-actions-menu relative shrink-0">

            <summary
                aria-label="Shipping template actions"
                title="More actions"
                class="flex h-6 w-6 cursor-pointer list-none items-center justify-center rounded-md border border-stone-200 bg-white text-stone-500 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300 [&::-webkit-details-marker]:hidden"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3.5 w-3.5"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <circle cx="5" cy="12" r="1.5"/>
                    <circle cx="12" cy="12" r="1.5"/>
                    <circle cx="19" cy="12" r="1.5"/>
                </svg>
            </summary>

            {{-- Fixed dropdown positioned by shared JavaScript --}}
            <div
                data-actions-dropdown
                class="fixed z-[9999] hidden w-44 overflow-hidden rounded-lg border border-stone-200 bg-white p-1 shadow-xl"
            >
                {{-- Activate / Deactivate --}}
                <form
                    action="{{ route('supplier.shipping-templates.toggle-active', $template) }}"
                    method="POST"
                >
                    @csrf

                    <button
                        type="submit"
                        data-close-product-actions
                        class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm transition
                            {{ $template->is_active
                                ? 'text-red-700 hover:bg-red-50'
                                : 'text-green-700 hover:bg-green-50' }}"
                    >
                        @if($template->is_active)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="mr-2 h-4 w-4 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" d="M8 8l8 8"/>
                            </svg>
                            Deactivate
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
            </svg>
                        <span class="ml-2">Activate</span>
                        @endif
                    </button>
                </form>



                {{-- Toggle Default --}}
<form
    action="{{ route('supplier.shipping-templates.toggle-default', ['template' => $template]) }}"
    method="POST"
>
    @csrf

    <button
        type="submit"
        data-close-product-actions
        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm transition
            {{ $template->is_default
                ? 'text-emerald-700 hover:bg-emerald-50'
                : 'text-stone-700 hover:bg-stone-50 hover:text-stone-950' }}"
    >
        @if($template->is_default)
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
            </svg>
            Remove Default
        @else
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>
            </svg>
            Set as Default
        @endif
    </button>
</form>



                <div class="my-1 border-t border-stone-100"></div>

                {{-- Edit --}}
                <a
                    href="{{ route('supplier.shipping-templates.edit', $template) }}"
                    data-close-product-actions
                    class="flex items-center rounded-md px-3 py-2 text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="mr-2 h-4 w-4 shrink-0 text-stone-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"/>
                    </svg>
                    Edit
                </a>

                <div class="my-1 border-t border-stone-100"></div>

                {{-- Delete --}}
                <form
                    action="{{ route('supplier.shipping-templates.destroy', $template) }}"
                    method="POST"
                    class="delete-template-form"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        data-close-product-actions
                        class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-red-700 transition hover:bg-red-50 hover:text-red-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 h-4 w-4 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </details>
    </div>

<button
    type="button"
    class="open-destinations-drawer mb-2 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50"
    data-template-id="{{ $template->id }}"
>
    

    Manage destinations
</button>


    
                        {{-- Delivery destinations --}}
<div
    x-data="{ expanded: false, canExpand: false }"
    x-init="$nextTick(() => {
        const content = $refs.locations;
        canExpand = content.scrollHeight > 60;
    })"
    class="mt-2 min-w-0"
>
    <div
        x-ref="locations"
        class="flex flex-wrap gap-1 overflow-hidden transition-[max-height] duration-200"
        :style="expanded ? 'max-height: none' : 'max-height: 160px'"
    >
        @foreach($template->locations as $location)
            @php
                $isCity = $location->parent_id !== null;
                $bgColor = $isCity ? 'bg-green-200' : 'bg-blue-100';
                $textColor = $isCity ? 'text-green-800' : 'text-blue-700';
            @endphp

            <span class="px-2 py-1 text-xs rounded-full {{ $bgColor }} {{ $textColor }}">
                {{ $location->name }}
            </span>
        @endforeach
    </div>

    <button
        type="button"
        x-cloak
        x-show="canExpand"
        @click="expanded = !expanded"
        :aria-expanded="expanded.toString()"
        class="mt-1 flex h-6 w-6 items-center justify-center rounded-md text-stone-500 transition hover:bg-stone-100 hover:text-stone-800"
        :title="expanded ? 'Show less' : 'Show all destinations'"
        :aria-label="expanded ? 'Collapse destinations' : 'Expand destinations'"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 transition-transform duration-200"
            :class="expanded ? 'rotate-180' : ''"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m6 9 6 6 6-6"
            />
        </svg>
    </button>
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


@include('dashboard.supplier.shipping-templates.partials.destinations-drawer', [
    'countries' => $countries,
])




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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const menus = document.querySelectorAll('.product-actions-menu');

    if (!menus.length) return;

    function closeMenu(details) {
        const dropdown = details.querySelector('[data-actions-dropdown]');

        details.open = false;

        if (dropdown) {
            dropdown.classList.add('hidden');
            dropdown.style.top = '';
            dropdown.style.left = '';
        }
    }

    function positionMenu(details) {
        const trigger = details.querySelector('summary');
        const dropdown = details.querySelector('[data-actions-dropdown]');

        if (!trigger || !dropdown || !details.open) return;

        dropdown.classList.remove('hidden');
        dropdown.style.visibility = 'hidden';

        const triggerRect = trigger.getBoundingClientRect();
        const menuRect = dropdown.getBoundingClientRect();
        const gap = 6;
        const padding = 8;

        let left = triggerRect.right - menuRect.width;
        let top = triggerRect.bottom + gap;

        // If there is insufficient space below, open above.
        if (top + menuRect.height > window.innerHeight - padding) {
            top = triggerRect.top - menuRect.height - gap;
        }

        // Keep the menu within the viewport horizontally.
        left = Math.max(
            padding,
            Math.min(left, window.innerWidth - menuRect.width - padding)
        );

        top = Math.max(
            padding,
            Math.min(top, window.innerHeight - menuRect.height - padding)
        );

        dropdown.style.left = `${left}px`;
        dropdown.style.top = `${top}px`;
        dropdown.style.visibility = 'visible';
    }

    menus.forEach(details => {
        details.addEventListener('toggle', () => {
            if (details.open) {
                // Close other open action menus.
                menus.forEach(other => {
                    if (other !== details) closeMenu(other);
                });

                positionMenu(details);
            } else {
                const dropdown = details.querySelector('[data-actions-dropdown]');

                if (dropdown) {
                    dropdown.classList.add('hidden');
                    dropdown.style.visibility = '';
                }
            }
        });

        details.querySelectorAll('[data-close-product-actions]').forEach(action => {
            action.addEventListener('click', () => {
                closeMenu(details);
            });
        });
    });

    // Close on outside click.
    document.addEventListener('click', event => {
        menus.forEach(details => {
            if (!details.contains(event.target)) {
                closeMenu(details);
            }
        });
    });

    // Close on Escape.
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            menus.forEach(closeMenu);
        }
    });

    // Close when scrolling or resizing.
    window.addEventListener('scroll', () => {
        menus.forEach(closeMenu);
    }, true);

    window.addEventListener('resize', () => {
        menus.forEach(closeMenu);
    });
});
</script>





@endsection