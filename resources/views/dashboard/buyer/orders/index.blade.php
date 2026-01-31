@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">My Orders</h2>
            <p class="text-sm text-gray-500">
                View all your orders, filter by status, and check details
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <label for="status" class="font-medium text-gray-700">Filter by status:</label>

        <div class="relative">
            <select name="status" id="status"
                    class="block appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="pending"    {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid"       {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped"    {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered"  {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed"  {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled"  {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            {{-- Arrow --}}
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">
            Apply
        </button>
    </form>

    {{-- Orders Grid --}}
    @if($orders->isEmpty())
        <div class="text-gray-500 text-center py-10">
            You have no orders yet
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($orders as $order)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition">

                {{-- Header --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-lg">
                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                               class="text-blue-600 hover:underline">
                                Order #{{ $order->id }}
                            </a>
                        </p>

                        @if(in_array($order->id, $disputedOrderIds))
                            <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                ⚠ Dispute
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-sm">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>

                {{-- Total --}}
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Total:</span>
                    <span class="font-semibold text-gray-900">{{ number_format($order->total, 2) }}₴</span>
                </div>

                {{-- Status Badge --}}
                @php
                    $statusClasses = [
                        'pending'    => 'bg-yellow-100 text-yellow-800',
                        'confirmed'  => 'bg-blue-100 text-blue-800',
                        'paid'       => 'bg-blue-100 text-blue-800',
                        'processing' => 'bg-purple-100 text-purple-800',
                        'production' => 'bg-orange-100 text-orange-800',
                        'shipped'    => 'bg-green-100 text-green-800',
                        'delivered'  => 'bg-indigo-100 text-indigo-800',
                        'completed'  => 'bg-green-200 text-green-900',
                        'cancelled'  => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ ucfirst($order->status) }}
                </span>

                {{-- Actions --}}
                <div class="mt-4 text-right">
                    <a href="{{ route('buyer.orders.show', $order->id) }}"
                       class="text-sm text-gray-700 hover:underline">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
