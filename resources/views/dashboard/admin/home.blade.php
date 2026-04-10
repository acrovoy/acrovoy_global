@extends('dashboard.admin.layout')

@section('dashboard-content')

{{-- HEADER --}}
<div class="mb-8">
    <h1 class="text-3xl font-semibold text-gray-900">
        Admin Overview
    </h1>
    <p class="text-gray-500 mt-2">
        System control panel, monitoring activity, users and marketplace health.
    </p>
</div>

@php
    // Заглушки (потом заменишь на реальные counts)
    $users = 1240;
    $suppliers = 312;
    $orders = 5890;
    $disputes = 14;
@endphp

{{-- KPI CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="text-sm text-gray-500">Users</div>
        <div class="text-2xl font-semibold text-gray-900">{{ $users }}</div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="text-sm text-gray-500">Suppliers</div>
        <div class="text-2xl font-semibold text-gray-900">{{ $suppliers }}</div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="text-sm text-gray-500">Orders</div>
        <div class="text-2xl font-semibold text-gray-900">{{ $orders }}</div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="text-sm text-gray-500">Disputes</div>
        <div class="text-2xl font-semibold text-red-600">{{ $disputes }}</div>
    </div>

</div>

{{-- SYSTEM STATUS --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-8">

    <h2 class="text-lg font-semibold text-gray-900 mb-4">
        System Status
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

        <div class="flex justify-between border rounded-lg p-3">
            <span class="text-gray-600">API Status</span>
            <span class="text-green-600 font-semibold">Operational</span>
        </div>

        <div class="flex justify-between border rounded-lg p-3">
            <span class="text-gray-600">Marketplace</span>
            <span class="text-green-600 font-semibold">Active</span>
        </div>

        <div class="flex justify-between border rounded-lg p-3">
            <span class="text-gray-600">Payments</span>
            <span class="text-yellow-600 font-semibold">Stable</span>
        </div>

    </div>

</div>

{{-- QUICK ACTIONS --}}
<div class="bg-white border border-gray-200 rounded-xl p-6">

    <h2 class="text-lg font-semibold text-gray-900 mb-4">
        Quick Actions
    </h2>

    <div class="flex flex-wrap gap-3">

        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">
            Manage Users
        </a>

        <a href="{{ route('admin.sellers.index') }}"
           class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">
            Review Suppliers
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">
            Check Orders
        </a>

        <a href="{{ route('admin.premium-plans.index') }}"
           class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">
            Premium Plans
        </a>

    </div>

</div>

@endsection