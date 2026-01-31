@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Shipping Center</h2>
        <span class="text-sm text-gray-500">Manage all your shipments</span>
    </div>

    <div class="bg-white border rounded-xl shadow-sm p-6">
        <p class="text-gray-700 mb-4">
            Here you can manage all shipments of your orders.
        </p>

        {{-- Placeholder for table, buttons, or forms --}}
        <div class="mt-4">
            <p class="text-gray-500 text-center py-10">The list of current shipments will appear here.</p>
        </div>
    </div>
</div>
@endsection
