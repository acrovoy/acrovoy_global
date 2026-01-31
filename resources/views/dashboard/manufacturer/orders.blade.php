@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Manage Orders</h2>
        <span class="text-sm text-gray-500">Orders from your products</span>
    </div>

    @include('dashboard.manufacturer.partials.orders-table')
</div>
@endsection
