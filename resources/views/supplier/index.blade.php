@extends('layouts.app')

@section('content')

{{-- HERO BANNER --}}
<section class="py-2 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl font-extrabold text-gray-900">
            Suppliers
        </h1>
        <p class="text-lg text-gray-700 mt-2">
            Browse our trusted suppliers for premium furniture and materials.
        </p>
    </div>
</section>



{{-- MAIN LAYOUT --}}
<section class="py-8 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">

        <div class="flex flex-col md:flex-row gap-6">




            {{-- FILTER SIDEBAR --}}
            <aside class="w-full md:w-1/4 mb-4">

            

   {{-- Category links — кликаем, переход сразу --}}
<div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4 mb-4">
    <h4 class="font-medium mb-2">Filter By Category</h4>
    <ul class="space-y-1">
        @foreach($categories as $cat)
            <li>
                <a href=""
                   class="text-gray-700 hover:text-black
                          @if(request('category') == $cat->slug) font-bold text-orange-500 @endif">
                    {{ $cat->name }}
                </a>

                {{-- Подкатегории --}}
                @if($cat->children->count())
                    <ul class="ml-4 mt-1 space-y-1">
                        @foreach($cat->children as $child)
                            <li>
                                <a href="{{ route('catalog.index', ['category' => $child->slug]) }}"
                                   class="text-gray-600 hover:text-black
                                          @if(request('category') == $child->slug) font-bold text-orange-500 @endif">
                                    {{ $child->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>




 {{-- Reputation bar --}}
<div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
    <h4 class="font-medium mb-2">Supplier Type</h4>
    <div class="space-y-2 max-h-48 overflow-y-auto p-2 rounded">
        @php
            $types = ['premium' => 'Premium', 'verified' => 'Verified', 'featured' => 'Featured'];
        @endphp
        @foreach($types as $key => $label)
            <label class="flex items-center gap-2">
                <input type="checkbox" name="supplier_type[]" value="{{ $key }}"
                    @if(in_array($key, (array) request('supplier_type'))) checked @endif>
                {{ $label }}
            </label>
        @endforeach
    </div>
</div>





    {{-- Material + Price filters — через форму GET --}}
    <form method="GET" action="" class="mt-4 bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
        {{-- Сохраняем текущую категорию при отправке --}}
        <input type="hidden" name="category" value="{{ request('category') }}">


     {{-- Reset Filters Button styled like section header --}}
<div class="flex items-center justify-between mb-2">
    <h2 class="text-xl font-bold">Filter By Products</h2>
    <a href=""
       class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 shadow-sm rounded-full transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Reset
    </a>
</div>


        {{-- Material Filter --}}
            <div>
                <h4 class="font-medium mb-2">Materials</h4>
                <div class="max-h-48 overflow-y-auto border p-2 rounded space-y-2">
                    @foreach(App\Models\Material::all() as $material)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="material[]" value="{{ $material->slug }}"
                                @if(in_array($material->slug, (array) request('material'))) checked @endif>
                            {{ $material->name }}
                        </label>
                    @endforeach
                </div>
            </div>

        {{-- MOQ Filter --}}
<div>
    <h4 class="font-medium mb-2">MOQ</h4>
    <input type="number" name="min_moq" value="{{ request('min_moq') }}" placeholder="Min MOQ" class="w-full p-2 border rounded">
</div>


{{-- Sold Filter --}}
<div>
    <h4 class="font-medium mb-2">Sold (Min)</h4>
    <input type="number" name="sold_from" value="{{ request('sold_from') }}" placeholder="Min sold" class="w-full p-2 border rounded">
</div>

{{-- Lead Time Filter --}}
<div>
    <h4 class="font-medium mb-2">Lead Time (days)</h4>
    <div class="flex space-x-2">
        <input type="number" name="min_lead_time" value="{{ request('min_lead_time') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
        <input type="number" name="max_lead_time" value="{{ request('max_lead_time') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
    </div>
</div>

        {{-- Price Filter --}}
        <div>
            <h4 class="font-medium mb-2">Price</h4>
            <div class="flex space-x-2">
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
            </div>
        </div>



        {{-- Country Filter --}}
        <div>
            <h4 class="font-medium mb-2">Country of Origin</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto border p-2 rounded">
                @foreach(App\Models\Country::all() as $country)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="country[]" value="{{ $country->id }}"
                            @if(in_array($country->id, (array) request('country'))) checked @endif>
                        {{ $country->name }}
                    </label>
                @endforeach
            </div>
        </div>


        <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-700 transition">
            Apply Filters
        </button>
    </form>

</aside>






            {{-- SUPPLIER CONTENT --}}
            <div class="w-full md:flex-1">

                {{-- Sort Bar --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <div class="text-gray-700 text-sm mb-2 sm:mb-0">
                        Showing {{ count($suppliers) }} suppliers
                    </div>
                    <select class="border rounded p-2 bg-white shadow-sm">
                        
                        <option>Sort by: Name A-Z</option>
                        <option>Sort by: Name Z-A</option>
                        <option>Sort by: Newest</option>
                               
                        
                    </select>
                </div>

                {{-- Supplier Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($suppliers as $supplier)
                        <a href="{{ url('/supplier/' . $supplier->slug) }}"
                        class="block bg-white rounded-xl shadow hover:shadow-2xl transition overflow-hidden supplier-card"
                        data-country="{{ $supplier->country->name ?? '' }}">
                            <img src="{{ $supplier->catalog_image ? asset('storage/' . $supplier->catalog_image) : asset('images/no-logo.png') }}" 
                            class="w-full h-48 object-cover rounded-t-lg" 
                            alt="{{ $supplier->name }}">
                            <div class="p-4 text-center">
                                

                                <h3 class="text-lg font-semibold">{{ $supplier->name }}</h3>
                    <p class="text-gray-600">{{ $supplier->country->name ?? '' }} | {{ $supplier->short_description }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>


@endsection
