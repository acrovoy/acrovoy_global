@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('supplier.shipping-templates.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to shipping center 
        </a>


<div class="flex flex-col">


    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Warehouses</h2>
            <p class="text-sm text-gray-500">
                Manage all your warehouses, contact details and locations
            </p>
        </div>

        <div class="flex items-center gap-3">

            

            @can('create', App\Models\Warehouse::class)

<button onclick="openCreateWarehouseDrawer()"
    class="inline-flex items-center gap-2 mt-3 px-4 py-2
       text-sm font-medium text-gray-700
       bg-white border border-gray-200
       rounded-lg
       hover:bg-gray-50">

    <span class="text-lg leading-none">+</span>
    <span>Add New Warehouse</span>

</button>

@endcan

        </div>
    </div>

    <x-alerts />

    {{-- Table Card --}}
    <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">

            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Name</th>
                    <th class="px-5 py-3 text-left font-medium">Contact</th>
                    <th class="px-5 py-3 text-left font-medium">Phone</th>
                    <th class="px-5 py-3 text-left font-medium">Location</th>
                    <th class="px-5 py-3 text-left font-medium">Address</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

                @forelse($warehouses as $warehouse)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Name --}}
<td class="px-5 py-3 font-medium text-gray-900">
    <div class="flex flex-col">
        <span>{{ $warehouse->name }}</span>

        @if($warehouse->is_default)
            <span class="text-xs text-gray-400 font-normal">
                default
            </span>
        @endif
    </div>
</td>

                        {{-- Contact --}}
                        <td class="px-5 py-3 text-gray-700">
                            {{ $warehouse->contact_person ?? '-' }}
                        </td>

                        {{-- Phone --}}
                        <td class="px-5 py-3 text-gray-700">
                            {{ $warehouse->phone ?? '-' }}
                        </td>

                        {{-- Location --}}
<td class="px-5 py-3 text-gray-700">
    <div class="flex items-center justify-between gap-2">

        

        @if(!$warehouse->location_id)
            <button
                onclick="openAttachLocationDrawer({{ $warehouse->id }})"
                class="text-xs px-3 py-1.5 rounded-lg border border-gray-300
                       text-gray-600 hover:bg-gray-100 transition">
                Attach location
            </button>

            @else
            <div>
<button
                onclick="openAttachLocationDrawer({{ $warehouse->id }})"
                class="text-blue-500">
            @if($warehouse->location)
                {{ $warehouse->location->name }} / 
            @endif

            {{ $warehouse->country?->name ?? '-' }}

            </button>
        </div>

        @endif

    </div>
</td>

                        {{-- Address --}}
                        <td class="px-5 py-3 text-gray-700">
                            {{ $warehouse->address ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            
                                <span class="px-2 py-1 text-xs rounded-sm @if($warehouse->status == 'pending') bg-yellow-100 @else bg-green-100 @endif text-gray-600">
                                    {{ $warehouse->status }}
                                </span>
                            
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3 text-right whitespace-nowrap space-x-2">

                           @can('update', $warehouse)

        <button 
            onclick='openEditWarehouseDrawer(@json($warehouse))'
            class="text-sm text-gray-700 hover:underline">
            Edit
        </button>

    @endcan

                             @can('delete', $warehouse)

        <form action="{{ route('supplier.warehouses.destroy', $warehouse) }}"
              method="POST"
              class="inline delete-warehouse-form">

            @csrf
            @method('DELETE')

            <button class="text-sm text-red-600 hover:underline">
                Delete
            </button>

        </form>

    @endcan

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                            No warehouses found.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>


@include('dashboard.supplier.warehouses.partials.drawer-create')


{{-- DRAWER --}}
<div id="attachLocationDrawer"
     class="fixed inset-0 z-50 hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
         onclick="closeAttachLocationDrawer()"></div>

    {{-- DRAWER --}}
    <div id="attachLocationPanel"
         class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-2xl
                transform translate-x-full transition-transform duration-300
                flex flex-col">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b bg-gray-50">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Attach Location
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Attach a location to this warehouse for shipping and inventory.
                    </p>
                </div>

                <button type="button"
                        onclick="closeAttachLocationDrawer()"
                        class="shrink-0 text-gray-400 hover:text-gray-700
                               transition text-xl leading-none">
                    ✕
                </button>

            </div>
        </div>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('supplier.warehouses.attach-location') }}"
              class="flex flex-col flex-1">

            @csrf

            <input type="hidden"
                   name="warehouse_id"
                   id="attach_warehouse_id">

            {{-- BODY --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                <x-location-picker :countries="$countries" />

            </div>

            {{-- FOOTER --}}
            <div class="border-t bg-white px-6 py-4
                        flex items-center justify-between gap-2">

                <button type="button"
                        onclick="closeAttachLocationDrawer()"
                        class="px-4 py-2 text-sm rounded-lg
                               border border-gray-200 text-gray-600
                               hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg
                               bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Attach
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


@endsection