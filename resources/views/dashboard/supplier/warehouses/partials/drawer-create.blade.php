<div id="warehouseDrawer"
     class="fixed inset-0 z-50 hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 bg-gray-900 bg-opacity-30"
         onclick="closeWarehouseDrawer()"></div>

    {{-- DRAWER --}}
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl
                transform translate-x-full transition-transform duration-300"
         id="warehouseDrawerPanel">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900" id="drawerTitle">
                Create Warehouse
            </h3>

            <button onclick="closeWarehouseDrawer()"
                    class="text-gray-500 hover:text-gray-800 text-xl">
                ✕
            </button>
        </div>

        {{-- Form --}}
        <form method="POST"
              action="{{ route('supplier.warehouses.store') }}"
              id="warehouseForm"
              class="flex flex-col gap-4 p-5 overflow-y-auto">

            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <input type="text" name="name" id="name"
                   placeholder="Name"
                   class="w-full border rounded-lg px-3 py-2">

            <input type="text" name="contact_person" id="contact_person"
                   placeholder="Contact person"
                   class="w-full border rounded-lg px-3 py-2">

            <input type="text" name="phone" id="phone"
                   placeholder="Phone"
                   class="w-full border rounded-lg px-3 py-2">

            <textarea name="address" id="address"
                      placeholder="Address"
                      class="w-full border rounded-lg px-3 py-2"></textarea>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_default" id="is_default" value="1">
                Default warehouse
            </label>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button"
                        onclick="closeWarehouseDrawer()"
                        class="px-4 py-2 text-sm border rounded-lg">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg">
                    Save
                </button>
            </div>

        </form>
    </div>
</div>