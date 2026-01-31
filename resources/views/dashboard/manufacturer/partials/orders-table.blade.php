@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Orders</h2>
            <p class="text-sm text-gray-500">
                Manage all your orders, filter by status, and view order details
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
                <option value="confirmed"  {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="paid"       {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="production" {{ request('status') === 'production' ? 'selected' : '' }}>Production</option>
                <option value="shipped"    {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered"  {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed"  {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled"  {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

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

    {{-- Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 uppercase text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">#</th>
                    <th class="px-5 py-3 text-left font-medium">Customer</th>
                    <th class="px-5 py-3 text-left font-medium">Product</th>
                    <th class="px-5 py-3 text-left font-medium">Qty</th>
                    <th class="px-5 py-3 text-left font-medium">Total</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium">Date</th>
                    <th class="px-5 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900 flex items-center gap-2">
                        {{ $order['id'] }}

                        @if(in_array($order['id'], $disputedOrderIds))
                            <a href="{{ route('manufacturer.orders.show', $order['id']) }}"
                               title="Dispute exists"
                               class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-4 h-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                                </svg>
                            </a>
                        @endif
                    </td>

                    <td class="px-5 py-3 text-gray-800">{{ $order['customer'] }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $order['product'] }}</td>
                    <td class="px-5 py-3">{{ $order['qty'] }}</td>
                    <td class="px-5 py-3">${{ $order['total'] }}</td>

                    <td class="px-5 py-3">
                        @php
                            $statusClasses = [
                                'pending'    => 'bg-yellow-100 text-yellow-800',
                                'paid'       => 'bg-blue-100 text-blue-800',
                                'confirmed'  => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-purple-100 text-purple-800',
                                'production' => 'bg-purple-100 text-purple-800',
                                'shipped'    => 'bg-green-100 text-green-800',
                                'delivered'  => 'bg-indigo-100 text-indigo-800',
                                'completed'  => 'bg-green-200 text-green-900',
                                'cancelled'  => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($order['status']) }}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-gray-800">{{ $order['date'] }}</td>

                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('manufacturer.orders.show', $order['id']) }}"
                           class="text-sm text-gray-700 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-10 text-center text-gray-500">
                        No orders yet
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
