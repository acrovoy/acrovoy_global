@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-6">Add Shipping Template</h2>

@include('dashboard.manufacturer.partials.shipping-template-form', [
    'shippingTemplate' => null
])
@endsection
