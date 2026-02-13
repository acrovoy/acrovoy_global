@extends('dashboard.admin.layout')

@section('dashboard-content')

<a href="{{ route('admin.shipping-templates.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to shipping templates 
        </a>


<h2 class="text-2xl font-bold">Add Shipping Template</h2>
<p class="text-sm text-gray-500 mb-6">
                    Create a shipping template to set prices, delivery times, and assign locations for your products.
                </p>

@include('dashboard.admin.partials.shipping-template-form', [
    'shippingTemplate' => null
])
@endsection
