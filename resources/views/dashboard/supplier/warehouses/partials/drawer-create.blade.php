<div id="warehouseDrawer"
     class="fixed inset-0 z-50 hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
         onclick="closeWarehouseDrawer()"></div>

    {{-- DRAWER --}}
    <div id="warehouseDrawerPanel"
         class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-2xl
                transform translate-x-full transition-transform duration-300
                flex flex-col">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900" id="drawerTitle">
                Create Warehouse
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Create or update your warehouse information for inventory and shipping.
            </p>
        </div>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('supplier.warehouses.store') }}"
              id="warehouseForm"
              class="flex flex-col flex-1">

            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- BODY --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Warehouse name
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           placeholder="Warehouse name"
                           class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Contact person
                    </label>

                    <input type="text"
                           name="contact_person"
                           id="contact_person"
                           placeholder="Contact person"
                           class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Phone
                    </label>

                    <input type="text"
                           name="phone"
                           id="phone"
                           placeholder="Phone"
                           class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Address
                    </label>

                    <textarea name="address"
                              id="address"
                              rows="4"
                              placeholder="Warehouse address"
                              class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-gray-900/10"></textarea>
                </div>

                <label class="flex items-center gap-3 text-sm text-gray-700">
                    <input type="checkbox"
                           name="is_default"
                           id="is_default"
                           value="1"
                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/10">
                    Default warehouse
                </label>

            </div>

            {{-- FOOTER --}}
            <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

                <button type="button"
                        onclick="closeWarehouseDrawer()"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-200
                               text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save
                </button>

            </div>

        </form>
    </div>
</div>