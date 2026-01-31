@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6 max-w-6xl">

    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Add product
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Create a new product listing for your catalog
            </p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="p-6">
            @include('dashboard.manufacturer.partials.add-product-form')
        </div>
    </div>

</div>
@endsection
