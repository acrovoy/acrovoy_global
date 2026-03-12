@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Product List</h2>
            <p class="text-sm text-gray-500">
                Manage all your products, edit details, prices, and inventory.
            </p>
        </div>

        <a href="{{ route('manufacturer.products.create') }}"
           class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            + Add New Product
        </a>
    </div>

    <x-alerts />
    
    {{-- Product Table --}}
    @include('dashboard.manufacturer.partials.product-list-table')

</div>
@endsection

