<div class="flex flex-col lg:flex-row gap-6">

           <div class="w-full lg:w-1/4 space-y-6">

            {{-- Категории продавца --}}
            @include('supplier.sections.catalog-sidebar')




    {{-- LEFT FILTER --}}
    <div class="bg-white rounded-xl shadow p-6">
                            <form method="GET" action="{{ url()->current() }}">
                                <input type="hidden" name="sort" value="{{ request('sort', 'featured') }}">
                                <input type="hidden" name="category" value="{{ request('category') }}">
                                <input type="hidden" name="tab" value="{{ request('tab', 'home') }}">
                                
                                <h2 class="text-lg font-semibold text-gray-900 mb-5">
                                    Filter Products
                                </h2>

                                {{-- ================= Price ================= --}}
                                <div class="border-b border-gray-100 pb-4 mb-4">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    Price
                                </h4>

                                <div class="grid grid-cols-2 gap-2">

                                <input type="number"
                                    name="min_price"
                                    value="{{ request('min_price') }}"
                                    placeholder="Min"
                                    class="text-sm rounded-lg border border-gray-200 p-2 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"

                                    
                                    >

                                <input type="number"
                                    name="max_price"
                                    value="{{ request('max_price') }}"
                                    placeholder="Max"
                                    class="text-sm rounded-lg border border-gray-200 p-2 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                    >

                                </div>
                                </div>


                                {{-- ================= Materials ================= --}}
                                <div class="border-b border-gray-100 pb-4 mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                        Materials
                                    </h4>

                                    <div class="max-h-48 overflow-y-auto space-y-1 text-sm">
                                        @foreach(App\Models\Material::all() as $material)
                                            <label class="flex items-center gap-2 px-1 py-1 rounded hover:bg-gray-50 cursor-pointer">

                                                <input type="checkbox"
                                    name="material[]"
                                    value="{{ $material->slug }}"
                                    class="rounded text-emerald-600 border-gray-300"
                                    @checked(in_array($material->slug, request()->input('material', [])))
                                >

                                                <span class="text-gray-600">
                                                    {{ $material->name }}
                                                </span>

                                            </label>
                                        @endforeach
                                    </div>
                                </div>



                                {{-- ================= MOQ ================= --}}
                                <div class="border-b border-gray-100 pb-4 mb-4">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    MOQ
                                </h4>

                                <input type="number"
                                    name="min_moq"
                                    value="{{ request('min_moq') }}"
                                    placeholder="MOQ"
                                    class="w-full text-sm rounded-lg border border-gray-200 p-2 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">

                                </div>



                                {{-- ================= Lead Time ================= --}}
                                <div class="border-b border-gray-100 pb-4 mb-4">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    Lead Time (days)
                                </h4>

                                <div class="grid grid-cols-2 gap-2">

                                <input type="number"
                                    name="min_lead_time"
                                    value="{{ request('min_lead_time') }}"
                                    placeholder="Min"
                                    class="text-sm rounded-lg border border-gray-200 p-2 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">

                                <input type="number"
                                    name="max_lead_time"
                                    value="{{ request('max_lead_time') }}"
                                    placeholder="Max"
                                    class="text-sm rounded-lg border border-gray-200 p-2 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">

                                </div>
                                </div>

                                {{-- ================= Country ================= --}}
                                <div class="mb-4">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    Country of Origin
                                </h4>

                                <div class="max-h-48 overflow-y-auto space-y-1 text-sm">

                                @foreach(App\Models\Country::all() as $country)

                                <label class="flex items-center gap-2 px-1 py-1 rounded hover:bg-gray-50 cursor-pointer">

                                <input type="checkbox"
                                    name="country[]"
                                    value="{{ $country->id }}"
                                    class="rounded text-emerald-600 border-gray-300"
                                    @checked(in_array($country->id, request()->input('country', [])))
                                    >

                                <span class="text-gray-600">{{ $country->name }}</span>

                                </label>

                                @endforeach

                                </div>
                                </div>

                                <button type="submit"
                                        class="w-full mt-2 bg-gray-950 hover:bg-gray-900 transition text-white text-sm font-medium py-2.5 rounded-lg shadow-sm">

                                Apply Filters

                                </button>

                            </form>
    </div>

</div>

{{-- Product Grid --}}
@include('supplier.sections.product-grid')

               
</div>