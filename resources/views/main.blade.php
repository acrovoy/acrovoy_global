@extends('layouts.app')

@section('content')





{{-- =======================
    1. HERO BLOCK
======================= --}}
<section class="relative h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/home/banners/hero.jpg') }}')">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="container relative mx-auto h-full flex flex-col justify-start items-start text-white px-14 pt-24">
        <h1 class="text-5xl font-extrabold mb-4 drop-shadow-2xl">ACROVOY</h1>
        <p class="text-2xl font-light mb-4 drop-shadow-2xl">{{ __('main.title1') }}</p>
        <p class="max-w-xl mb-8 text-lg drop-shadow-2xl">
            Find trusted suppliers of hotel & restaurant furniture, décor & outdoor collections.
        </p>

        <div class="flex space-x-4">
            <a href="/suppliers" class="px-6 py-3 bg-blue-900 rounded-lg hover:bg-blue-600 transition">
                Browse Suppliers
            </a>
            <a href="{{ route('buyer.rfqs.create') }}" class="px-6 py-3 bg-white text-blue-900 rounded-lg hover:bg-gray-200 transition">
    Request a Quote
</a>
        </div>
    </div>
</section>



{{-- =======================
    2. CATEGORIES
======================= --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            @foreach ($categories as $cat)
                <a href="{{ $cat['link'] }}" class="block bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">
                    <img src="{{ asset($cat['image']) }}" class="w-full h-48 object-cover" alt="{{ $cat['title'] }}">
                    <div class="p-4 text-center font-semibold text-lg">{{ $cat['title'] }}</div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =======================
    3. WHY ACROVOY
======================= --}}
<section class="py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold mb-10">Why ACROVOY</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach ($advantages as $adv)
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">✔ {{ $adv['title'] }}</h3>
                    <p class="text-gray-700">{{ $adv['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =======================
    4. FEATURED SUPPLIERS
======================= --}}
<section id="suppliers" class="py-20 bg-gray-50">
    <div class="container mx-auto">
        <h2 class="text-4xl font-bold mb-10 text-center">Featured Suppliers</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($featuredSuppliers as $supplier)
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-xl transition">
                    <img src="{{ asset($supplier['image']) }}" class="w-full h-48 object-cover rounded mb-4" alt="{{ $supplier['name'] }}">
                    <h3 class="text-lg font-semibold">{{ $supplier['name'] }}</h3>
                    <p class="text-gray-600">{{ $supplier['country'] }} | {{ $supplier['products'] }}</p>
                    <a href="{{ $supplier['link'] }}" class="text-blue-600 hover:underline mt-2 inline-block">
                        View Supplier
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =======================
    5. COLLECTIONS
======================= --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto">
        <h2 class="text-4xl font-bold mb-10 text-center">Collections</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($collections as $collection)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">
                    <!-- Картинка коллекции -->
                    <img src="{{ asset($collection['image']) }}" class="w-full h-48 object-cover" alt="{{ $collection['title'] }}">

                    <!-- Текст и кнопка -->
                    <div class="p-6 text-center">
                        <h3 class="font-semibold text-xl mb-4">{{ $collection['title'] }}</h3>
                        <a href="{{ $collection['link'] }}" class="inline-block px-6 py-2 bg-blue-900 text-white rounded hover:bg-blue-700 transition">View Collection</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =======================
    6. RFQ FORM
======================= --}}
<section id="rfq" class="py-20 bg-blue-50">
    <div class="container mx-auto max-w-2xl bg-white p-10 rounded-lg shadow-xl">
        <h2 class="text-3xl font-bold mb-4 text-center">Need furniture for your hotel or restaurant?</h2>
        <p class="text-center mb-8 text-gray-700">
            Submit your project requirements and receive offers from multiple verified suppliers.
        </p>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <input type="text" name="name" placeholder="Name" class="w-full p-3 border rounded-lg">
            <input type="text" name="company" placeholder="Company" class="w-full p-3 border rounded-lg">
            <input type="text" name="country" placeholder="Country" class="w-full p-3 border rounded-lg">
            <input type="text" name="product_type" placeholder="Product Type" class="w-full p-3 border rounded-lg">
            <input type="text" name="budget" placeholder="Budget" class="w-full p-3 border rounded-lg">
            <input type="file" name="drawing" class="w-full p-3 border rounded-lg">

            <button class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
                Get Quotes
            </button>
        </form>
    </div>
</section>

{{-- =======================
    7. ABOUT ACROVOY
======================= --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-bold mb-4">About ACROVOY</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
            ACROVOY is an international B2B platform connecting hotels, resorts and restaurants
            with trusted furniture and décor manufacturers.
            We help businesses source high-quality hospitality products with confidence.
        </p>
    </div>
</section>

{{-- =======================
    8. SUPPLIER LOCATIONS
======================= --}}
<section class="py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold mb-10">Our Supplier Locations</h2>

        <div class="flex flex-wrap justify-center gap-4">
            @foreach ($countries as $country)
                <span class="px-4 py-2 rounded-full font-semibold 
                    {{ $country === 'Vietnam' 
                        ? 'bg-yellow-200 text-yellow-900' 
                        : 'bg-blue-100 text-blue-800'
                    }}">
                    {{ $country }}
                </span>
            @endforeach
        </div>
    </div>
</section>



@endsection
