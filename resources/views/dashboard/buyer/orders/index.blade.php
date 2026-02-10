@extends('dashboard.layout')

@section('dashboard-content')
<div class="mb-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">My Orders</h2>
            <p class="text-sm text-gray-500">
                View all your orders, filter by status, and check details
            </p>
        </div>
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

{{-- Orders Table --}}
@if($orders->isEmpty())
    <div class="text-gray-500 text-center py-10">
        You have no orders yet
    </div>
@else
    <div class="bg-white border rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Order</th>
                    <th class="px-4 py-2 text-left font-medium">Products</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                    <th class="px-4 py-2 text-left font-medium">Total Amount</th>
                    
                    <th class="px-4 py-2 text-left font-medium">Dispute</th>
                    <th class="px-4 py-2 text-left font-medium">Created</th>
                    <th class="px-4 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-mono text-gray-800">
                            <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-blue-600 hover:underline">
                                #{{ $order->id }}
                            </a>
                        </td>
                        
                        
                        <td class="px-4 py-2 text-gray-800">
                            <ul class="list-none space-y-0 max-h-40 overflow-y-auto text-xs text-gray-500">
                                @foreach($order->items as $item)
                                    <li>{{ $item->product->name ?? $item->product_name }} x{{ $item->quantity }}</li>
                                @endforeach
                            </ul>
                        </td>



                        <td class="px-4 py-2">
                            @php
                                $statusClasses = [
                                    'pending'    => 'bg-yellow-100 text-yellow-800',
                                    'confirmed'  => 'bg-blue-100 text-blue-800',
                                    'paid'       => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-purple-100 text-purple-800',
                                    'production' => 'bg-orange-100 text-orange-800',
                                    'shipped'    => 'bg-green-100 text-green-800',
                                    'delivered'  => 'bg-indigo-100 text-indigo-800',
                                    'completed'  => 'bg-gray-200 text-green-900',
                                    'cancelled'  => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-medium inline-block {{ $statusClasses[$order->status] ?? 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>


                        <td class="px-4 py-2 font-semibold text-gray-900">
                            {{ number_format($order->total, 2) }}$
                        </td>
                        
                        <td class="px-4 py-2">
                            @if(in_array($order->id, $disputedOrderIds))
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    ⚠ Dispute
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2 text-gray-800">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                               class="px-3 py-1 bg-gray-900 text-white text-xs rounded-lg hover:bg-gray-800 transition">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
