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

        <a href="{{ route('supplier.products.create') }}"
   class="inline-flex items-center gap-2 px-4 py-2
          text-sm font-medium text-gray-700
          bg-white border border-gray-200
          rounded-lg
          hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
          active:scale-[0.98]
          transition-all duration-150 shadow-sm">

    <span class="text-lg leading-none">+</span>
    <span>Add New Product</span>

</a>
    </div>

    <x-alerts />
    
    {{-- Product Table --}}
    @include('dashboard.supplier.partials.product-list-table')

    <div class="mt-6">
    {{ $products->links() }}
</div>

</div>
@endsection

