@extends('dashboard.layout')

@section('dashboard-content')

<div class="space-y-6">

    {{-- Back navigation --}}
    <a
        href="{{ route('supplier.shipping-templates.index') }}"
        class="inline-flex items-center gap-2 text-sm text-stone-500 transition hover:text-stone-900"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/>
        </svg>
        Back to Shipping Center
    </a>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                Warehouses
            </h1>
            <p class="mt-1 text-sm leading-5 text-stone-500">
                Manage your warehouse locations, contact details and availability.
            </p>
        </div>

        @can('create', App\Models\Warehouse::class)
            <button
                type="button"
                onclick="openCreateWarehouseDrawer()"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 shadow-sm transition hover:border-stone-300 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-300 focus:ring-offset-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Add New Warehouse
            </button>
        @endcan
    </div>

    <x-alerts />

    {{-- Warehouses table --}}
    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] divide-y divide-stone-200 text-sm">

                <thead class="bg-stone-50/80">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Warehouse
                        </th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Contact
                        </th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Phone
                        </th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Location
                        </th>
                        <th scope="col" class="w-[190px] px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Status & Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100 bg-white">

                    @forelse($warehouses as $warehouse)
                        <tr class="transition-colors hover:bg-stone-50/70">

                            {{-- Warehouse name and address --}}
                            <td class="max-w-[280px] px-5 py-4 align-top">
                                <div class="flex min-w-0 flex-col gap-1">
                                    <span class="break-words text-sm font-semibold leading-5 text-stone-900">
                                        {{ $warehouse->name }}
                                    </span>

                                    @if($warehouse->is_default)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                                            </svg>
                                            Default warehouse
                                        </span>
                                    @endif

                                    @if($warehouse->address)
                                        <span
                                            class="mt-0.5 break-words text-[11px] font-normal leading-4 text-stone-400"
                                            title="{{ $warehouse->address }}"
                                        >
                                            {{ $warehouse->address }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-5 py-4 align-top text-sm text-stone-600">
                                @if($warehouse->contact_person)
                                    {{ $warehouse->contact_person }}
                                @else
                                    <span class="text-stone-300">—</span>
                                @endif
                            </td>

                            {{-- Phone --}}
                            <td class="px-5 py-4 align-top text-sm text-stone-600">
                                @if($warehouse->phone)
                                    <span class="whitespace-nowrap">{{ $warehouse->phone }}</span>
                                @else
                                    <span class="text-stone-300">—</span>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="px-5 py-4 align-top">
                                @if(!$warehouse->location_id)
                                    <button
                                        type="button"
                                        onclick="openAttachLocationDrawer({{ $warehouse->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-stone-300 px-2.5 py-1.5 text-xs font-medium text-stone-600 transition hover:border-stone-400 hover:bg-white hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-300"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/>
                                            <circle cx="12" cy="9" r="2.5"/>
                                        </svg>
                                        Attach location
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        onclick="openAttachLocationDrawer({{ $warehouse->id }})"
                                        class="group flex max-w-[220px] items-start gap-2 text-left"
                                        title="Change warehouse location"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-stone-400 transition group-hover:text-stone-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/>
                                            <circle cx="12" cy="9" r="2.5"/>
                                        </svg>

                                        <span class="min-w-0">
                                            @if($warehouse->location)
                                                <span class="block break-words text-xs font-medium leading-4 text-stone-700 transition group-hover:text-stone-950">
                                                    {{ $warehouse->location->name }}
                                                </span>
                                            @endif

                                            <span class="block break-words text-[11px] leading-4 text-stone-400">
                                                {{ $warehouse->country?->name ?? 'Country not set' }}
                                            </span>
                                        </span>
                                    </button>
                                @endif
                            </td>

                            {{-- Status and actions --}}
                            <td class="w-[190px] px-5 py-4 align-top">
                                <div class="flex items-start justify-between gap-3">

                                    {{-- Status and creation date --}}
                                    <div class="flex min-w-0 flex-col items-start gap-2">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-medium
                                            {{ $warehouse->status === 'pending'
                                                ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200'
                                                : 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' }}">
                                            {{ ucfirst(str_replace('_', ' ', $warehouse->status ?? 'unknown')) }}
                                        </span>

                                        <span
                                            class="whitespace-nowrap text-[10px] tracking-wide text-stone-400"
                                            title="{{ $warehouse->created_at?->format('Y-m-d H:i') }}"
                                        >
                                            Created {{ $warehouse->created_at?->format('d M Y') }}
                                        </span>
                                    </div>

                                    {{-- Actions --}}
                                    @canany(['update', 'delete'], $warehouse)
                                        <details class="product-actions-menu relative ml-auto shrink-0">
                                            <summary
                                                aria-label="Warehouse actions"
                                                title="More actions"
                                                class="flex h-7 w-7 cursor-pointer list-none items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-500 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300 [&::-webkit-details-marker]:hidden"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <circle cx="5" cy="12" r="1.5"/>
                                                    <circle cx="12" cy="12" r="1.5"/>
                                                    <circle cx="19" cy="12" r="1.5"/>
                                                </svg>
                                            </summary>

                                            <div
                                                data-actions-dropdown
                                                class="fixed z-[9999] hidden w-44 overflow-hidden rounded-lg border border-stone-200 bg-white p-1 shadow-xl"
                                            >
                                                {{-- Edit --}}
                                                @can('update', $warehouse)
                                                    <button
                                                        type="button"
                                                        onclick='openEditWarehouseDrawer(@json($warehouse))'
                                                        data-close-product-actions
                                                        class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"/>
                                                        </svg>
                                                        Edit
                                                    </button>
                                                @endcan

                                                @can('update', $warehouse)
                                                    @can('delete', $warehouse)
                                                        <div class="my-1 border-t border-stone-100"></div>
                                                    @endcan
                                                @endcan

                                                {{-- Delete --}}
                                                @can('delete', $warehouse)
                                                    <form
                                                        action="{{ route('supplier.warehouses.destroy', $warehouse) }}"
                                                        method="POST"
                                                        class="delete-warehouse-form"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            data-close-product-actions
                                                            class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-red-700 transition hover:bg-red-50 hover:text-red-800"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/>
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </details>
                                    @endcanany

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-stone-200 bg-stone-50 text-stone-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 12v.01M15 15v.01M15 18v.01"/>
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-stone-900">
                                        No warehouses yet
                                    </h3>

                                    <p class="mt-1 text-sm leading-5 text-stone-500">
                                        Add a warehouse to manage its address, contact details and shipping location.
                                    </p>

                                    @can('create', App\Models\Warehouse::class)
                                        <button
                                            type="button"
                                            onclick="openCreateWarehouseDrawer()"
                                            class="mt-4 inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-white px-3.5 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                            </svg>
                                            Add New Warehouse
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        @if(method_exists($warehouses, 'links') && $warehouses->hasPages())
            <div class="border-t border-stone-200 px-5 py-4">
                {{ $warehouses->links() }}
            </div>
        @endif

    </div>

</div>

@include('dashboard.supplier.warehouses.partials.drawer-create')

{{-- Attach Location Drawer --}}
<div id="attachLocationDrawer" class="fixed inset-0 z-50 hidden">

    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-stone-950/40 backdrop-blur-[2px]"
        onclick="closeAttachLocationDrawer()"
    ></div>

    {{-- Drawer --}}
    <div
        id="attachLocationPanel"
        class="absolute right-0 top-0 flex h-full w-full max-w-[460px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    >
        {{-- Header --}}
        <div class="border-b border-stone-200 bg-stone-50/80 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold tracking-tight text-stone-900">
                        Attach Location
                    </h3>
                    <p class="mt-1 text-sm leading-5 text-stone-500">
                        Set the warehouse location used for shipping and inventory.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeAttachLocationDrawer()"
                    aria-label="Close drawer"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-stone-400 transition hover:bg-stone-200/70 hover:text-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18 6-12 12M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('supplier.warehouses.attach-location') }}"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <input type="hidden" name="warehouse_id" id="attach_warehouse_id">

            <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
                <x-location-picker :countries="$countries" />
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between gap-3 border-t border-stone-200 bg-white px-6 py-4">
                <button
                    type="button"
                    onclick="closeAttachLocationDrawer()"
                    class="rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50 hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-300"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-stone-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2"
                >
                    Save Location
                </button>
            </div>
        </form>
    </div>
</div>







<script>
function closeAttachLocationDrawer() {
    document.getElementById('attachLocationPanel')
        .classList.add('translate-x-full');

    setTimeout(() => {
        document.getElementById('attachLocationDrawer')
            .classList.add('hidden');
    }, 300);
}
</script>


<script>
function openCreateWarehouseDrawer() {

    resetWarehouseForm();

    document.getElementById('drawerTitle').innerText = 'Create Warehouse';

    const form = document.getElementById('warehouseForm');
    form.action = "{{ route('supplier.warehouses.store') }}";

    document.getElementById('formMethod').value = 'POST';

    openWarehouseDrawer();
}

function openEditWarehouseDrawer(warehouse) {

 

    document.getElementById('drawerTitle').innerText = 'Edit Warehouse';

    const form = document.getElementById('warehouseForm');
    form.action = "{{ route('supplier.warehouses.update', ':id') }}".replace(':id', warehouse.id);

    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('name').value = warehouse.name ?? '';
    document.getElementById('contact_person').value = warehouse.contact_person ?? '';
    document.getElementById('phone').value = warehouse.phone ?? '';
    document.getElementById('address').value = warehouse.address ?? '';
    document.getElementById('is_default').checked = warehouse.is_default ?? false;


    

    openWarehouseDrawer();

    
}

function openWarehouseDrawer() {
    document.getElementById('warehouseDrawer').classList.remove('hidden');

    setTimeout(() => {
        document.getElementById('warehouseDrawerPanel')
            .classList.remove('translate-x-full');
    }, 10);
}

function closeWarehouseDrawer() {
    document.getElementById('warehouseDrawerPanel')
        .classList.add('translate-x-full');

    setTimeout(() => {
        document.getElementById('warehouseDrawer')
            .classList.add('hidden');
    }, 300);
}

function resetWarehouseForm() {
    document.getElementById('name').value = '';
    document.getElementById('contact_person').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('address').value = '';
    document.getElementById('is_default').checked = false;
}
</script>

<script>
function openAttachLocationDrawer(warehouseId) {
    console.log('attach location for:', warehouseId);

    document.getElementById('attach_warehouse_id').value = warehouseId;
    
    document.getElementById('attachLocationDrawer')
        .classList.remove('hidden');

    setTimeout(() => {
        document.getElementById('attachLocationPanel')
            .classList.remove('translate-x-full');
    }, 10);
}
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {


    document.querySelectorAll('.delete-warehouse-form')
        .forEach(form => {


            form.addEventListener('submit', function (event) {

                event.preventDefault();


                const currentForm = this;


                confirmModal.open({

                    type: 'danger',

                    title: 'Delete Warehouse',

                    message: 'Are you sure you want to delete this warehouse?',

                    description: 'All related warehouse data may be affected.',

                    confirmText: 'Delete Warehouse',

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
document.addEventListener('DOMContentLoaded', function () {
    const menus = document.querySelectorAll('.product-actions-menu');

    function closeMenu(menu) {
        menu.open = false;

        const dropdown = menu.querySelector('[data-actions-dropdown]');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }

    function closeAllMenus(except = null) {
        menus.forEach(menu => {
            if (menu !== except) {
                closeMenu(menu);
            }
        });
    }

    function positionDropdown(menu) {
        const summary = menu.querySelector('summary');
        const dropdown = menu.querySelector('[data-actions-dropdown]');

        if (!summary || !dropdown) return;

        dropdown.classList.remove('hidden');

        const rect = summary.getBoundingClientRect();
        const width = dropdown.offsetWidth;
        const height = dropdown.offsetHeight;
        const margin = 8;
        const gap = 6;

        let left = rect.right - width;
        let top = rect.bottom + gap;

        if (left < margin) {
            left = margin;
        }

        if (left + width > window.innerWidth - margin) {
            left = window.innerWidth - width - margin;
        }

        if (
            top + height > window.innerHeight - margin &&
            rect.top - height - gap >= margin
        ) {
            top = rect.top - height - gap;
        }

        dropdown.style.left = `${left}px`;
        dropdown.style.top = `${top}px`;
    }

    menus.forEach(menu => {
        const summary = menu.querySelector('summary');
        const dropdown = menu.querySelector('[data-actions-dropdown]');

        if (!summary || !dropdown) return;

        summary.addEventListener('click', function (event) {
            event.preventDefault();

            const shouldOpen = !menu.open;

            closeAllMenus();

            if (shouldOpen) {
                menu.open = true;
                positionDropdown(menu);
            }
        });

        dropdown.addEventListener('click', function (event) {
            if (event.target.closest('[data-close-product-actions]')) {
                closeMenu(menu);
            }
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.product-actions-menu')) {
            closeAllMenus();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllMenus();
        }
    });

    window.addEventListener('resize', function () {
        menus.forEach(menu => {
            if (menu.open) positionDropdown(menu);
        });
    });

    window.addEventListener('scroll', function () {
        menus.forEach(menu => {
            if (menu.open) positionDropdown(menu);
        });
    }, true);
});
</script>


@endsection