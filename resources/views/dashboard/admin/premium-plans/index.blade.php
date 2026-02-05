@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
    <div>
        <h2 class="text-2xl font-bold">Free & Premium Plans</h2>
        <p class="text-gray-600 text-sm">
            Manage all seller plans here.
        </p>
    </div>

    <div class="flex items-center gap-3">
    <a href="{{ route('admin.exchange-rates.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Premium Users
            </a>

    <a href="{{ route('admin.premium-plans.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                + Add New Plan
            </a>
    </div>
</div>

{{-- Уведомление об успешном действии --}}
@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800">
        {{ session('success') }}
    </div>
@endif

<h3 class="text-xl font-bold mb-4">Seller Plans</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    @php
    
    @endphp

    @foreach($sellerPlans as $plan)
        @include('dashboard.admin.premium-plans.partials.plan-card', ['plan' => $plan])
    @endforeach
</div>

<h3 class="text-xl font-bold mb-4">Buyer Plans</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($buyerPlans as $plan)
        @include('dashboard.admin.premium-plans.partials.plan-card', ['plan' => $plan])
    @endforeach
</div>

@endsection
