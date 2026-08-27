{{-- ============================================================
        FILTERS
    ============================================================= --}}
    <form method="GET" action="{{ route('admin.settings.attributes.index') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-sm">
                    Filters
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Find and sort attributes
                </p>
            </div>
            @if(request()->hasAny(['search','entity_type','type','new','sort','direction']))
            <a href="{{ route('admin.settings.attributes.index') }}" class="text-sm text-gray-500 hover:text-gray-900 hover:underline">
                Reset
            </a>
            @endif
        </div>

        {{-- FILTERS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- SEARCH --}}
            <div class="lg:col-span-2">
                <label for="attribute-search" class="block text-xs font-medium text-gray-600 mb-1">
                    Search
                </label>
                <input id="attribute-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code..." class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
            </div>

            {{-- ENTITY TYPE --}}
            <div>
                <label for="attribute-entity-type" class="block text-xs font-medium text-gray-600 mb-1">
                    Entity Type
                </label>
                <select id="attribute-entity-type" name="entity_type" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="">All entity types</option>
                    <option value="product" @selected(request('entity_type')==='product' )>Product</option>
                    <option value="rfq" @selected(request('entity_type')==='rfq' )>RFQ</option>
                    <option value="offer" @selected(request('entity_type')==='offer' )>Offer</option>
                    <option value="contract" @selected(request('entity_type')==='contract' )>Contract</option>
                    <option value="company" @selected(request('entity_type')==='company' )>Company</option>
                    <option value="user" @selected(request('entity_type')==='user' )>User</option>
                </select>
            </div>

            {{-- ATTRIBUTE TYPE --}}
            <div>
                <label for="attribute-type" class="block text-xs font-medium text-gray-600 mb-1">
                    Attribute Type
                </label>

                <select id="attribute-type" name="type" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="">All types</option>
                    <option value="text" @selected(request('type')==='text' )>Text</option>
                    <option value="number" @selected(request('type')==='number' )>Number</option>
                    <option value="measurement" @selected(request('type')==='measurement' )>Measurement</option>
                    <option value="select" @selected(request('type')==='select' )>Select</option>
                    <option value="multiselect" @selected(request('type')==='multiselect' )>Multiselect</option>
                    <option value="boolean" @selected(request('type')==='boolean' )>Boolean</option>
                </select>
            </div>
        </div>

        {{-- NEW --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="new" value="1" @checked(request()->boolean('new')) class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                <span class="text-sm font-medium text-gray-700">
                    New custom attributes
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-xs font-semibold">
                    Last 7 days
                </span>
            </label>
        </div>

        {{-- SORTING --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- SORT BY --}}
                <div>
                    <label for="attribute-sort" class="block text-xs font-medium text-gray-600 mb-1">
                        Sort by
                    </label>
                    <select id="attribute-sort" name="sort" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        <option value="sort_order" @selected(request('sort', 'sort_order' )==='sort_order' )>Sort Order</option>
                        <option value="name" @selected(request('sort')==='name' )>Name</option>
                        <option value="code" @selected(request('sort')==='code' )>Code</option>
                        <option value="created_at" @selected(request('sort')==='created_at' )>Created</option>
                    </select>
                </div>

                {{-- DIRECTION --}}
                <div>
                    <label for="attribute-direction" class="block text-xs font-medium text-gray-600 mb-1">
                        Direction
                    </label>
                    <select id="attribute-direction" name="direction" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        <option value="asc" @selected(request('direction', 'asc' )==='asc' )>Ascending</option>
                        <option value="desc" @selected(request('direction')==='desc' )>Descending</option>
                    </select>
                </div>

                {{-- APPLY --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition">
                        Apply filters
                    </button>
                </div>
            </div>
        </div>
    </form>