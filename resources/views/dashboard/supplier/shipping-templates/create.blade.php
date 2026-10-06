@extends('dashboard.layout')

@section('dashboard-content')

    <a href="{{ route('supplier.shipping-templates.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to shipping templates
        </a>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Add Shipping Template
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Create a shipping template to define the delivery type, price,
            delivery time, and destinations for your products.
        </p>
    </div>

    @include('dashboard.supplier.partials.shipping-template-form', [
        'shippingTemplate' => null
    ])

@endsection