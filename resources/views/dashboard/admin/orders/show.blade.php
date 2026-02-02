@extends('dashboard.admin.layout')

@section('dashboard-content')

<a href="{{ route('admin.orders.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to orders
        </a>

<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Order #{{ $orderData['id'] }}</h2>
        <span class="px-3 py-1 rounded text-sm
            @if($orderData['status'] === 'pending') bg-yellow-100 text-yellow-800
            @elseif($orderData['status'] === 'paid') bg-blue-100 text-blue-800
            @elseif($orderData['status'] === 'shipped') bg-green-100 text-green-800
            @elseif($orderData['status'] === 'completed') bg-gray-100 text-gray-800
            @endif
        ">
            {{ ucfirst($orderData['status']) }}
        </span>
    </div>

    {{-- Customer --}}
    <div class="border rounded-lg p-4 bg-gray-50">
        <h3 class="font-semibold mb-2">Customer & User</h3>
        <p><strong>Customer:</strong> {{ $orderData['customer'] }}</p>
        <p><strong>User:</strong> {{ $orderData['user_name'] }} ({{ $orderData['email'] }})</p>
        <p><strong>Order date:</strong> {{ $orderData['date'] }}</p>
    </div>

    {{-- Contact & Shipping --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-2">Contact & Shipping</h3>
        <div class="text-sm space-y-1">
            <p><strong>Name:</strong> {{ $orderData['first_name'] }} {{ $orderData['last_name'] }}</p>
            <p><strong>Country:</strong> {{ $orderData['country'] }}</p>
            <p><strong>City:</strong> {{ $orderData['city'] }}</p>
            @if(!empty($orderData['region']))
                <p><strong>Region:</strong> {{ $orderData['region'] }}</p>
            @endif
            <p><strong>Street:</strong> {{ $orderData['street'] }}</p>
            <p><strong>Postal code:</strong> {{ $orderData['postal_code'] }}</p>
            @if(!empty($orderData['phone']))
                <p><strong>Phone:</strong> {{ $orderData['phone'] }}</p>
            @endif
        </div>
    </div>

    {{-- Order Items --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-3">Order Items</h3>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 text-left px-2">Product</th>
                    <th class="py-2 text-center px-2">Qty</th>
                    <th class="py-2 text-right px-2">Price</th>
                    <th class="py-2 text-right px-2">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderData['items'] as $item)
                    <tr class="border-t">
                        <td class="py-2 px-2">{{ $item['product'] }}</td>
                        <td class="py-2 px-2 text-center">{{ $item['qty'] }}</td>
                        <td class="py-2 px-2 text-right">${{ $item['price'] }}</td>
                        <td class="py-2 px-2 text-right font-semibold">${{ $item['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tracking & Invoice --}}
    <div class="border rounded-lg p-4 bg-gray-50">
        <h3 class="font-semibold mb-3">Shipping & Invoice</h3>
        <p><strong>Tracking number:</strong> {{ $orderData['tracking_number'] ?? '-' }}</p>
        @if(!empty($orderData['invoice_file']))
            <p>
                <a href="{{ asset('storage/' . $orderData['invoice_file']) }}" target="_blank"
                   class="text-blue-600 hover:underline text-sm">
                   View invoice
                </a>
            </p>
        @endif
    </div>

    {{-- Status Timeline --}}
    <div class="border rounded-lg p-4 bg-white">
        <h3 class="font-semibold mb-3">Status Timeline</h3>
        <ol class="relative border-l border-gray-300">
            @forelse($orderData['status_history'] as $history)
                <li class="mb-6 ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full
                        @if($history->status === 'cancelled') bg-red-500
                        @elseif($history->status === 'completed') bg-green-600
                        @else bg-blue-600
                        @endif text-white text-sm">✓</span>
                    <h4 class="font-medium">{{ ucfirst($history->status) }}</h4>
                    <time class="block text-sm text-gray-500">{{ $history->created_at->format('d.m.Y H:i') }}</time>
                    @if($history->comment)
                        <p class="mt-1 text-gray-600">{{ $history->comment }}</p>
                    @endif
                </li>
            @empty
                <li class="ml-6 text-gray-500">No status history</li>
            @endforelse
        </ol>
    </div>

    {{-- Disputes --}}
    <div class="border rounded-lg p-4 bg-gray-50">
        <h3 class="font-semibold mb-3">Disputes</h3>
        @if(count($orderData['disputes']) > 0)
            <ul class="space-y-3">
                @foreach($orderData['disputes'] as $dispute)
                    <li class="border rounded-lg p-4 bg-white">
                        <p><strong>Customer:</strong> {{ $dispute->user->name ?? '—' }}</p>
                        <p><strong>Reason:</strong> {{ $dispute->reason }}</p>
                        <p><strong>Requested action:</strong> {{ ucfirst($dispute->action) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}</p>
                        @if($dispute->buyer_comment)
                            <p class="mt-1 bg-red-100 p-2 rounded">Comment: {{ $dispute->buyer_comment }}</p>
                        @endif
                        @if($dispute->admin_comment)
                            <p class="mt-1 bg-purple-100 p-2 rounded">Admin decision: {{ $dispute->admin_comment }}</p>
                        @endif
                        @if($dispute->attachment)
                            <p><a href="{{ asset('storage/' . $dispute->attachment) }}" target="_blank" class="text-blue-600 hover:underline">View attachment</a></p>
                        @endif
                    </li>
                @endforeach
            </ul>


            

        <h2 class="text-xl font-bold mt-6 mb-4">Dispute History</h2>

@if($orderData['disputes']->isEmpty())
    <p>No disputes for this order.</p>
@else
    <table class="w-full border rounded text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Reason</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Who / Comment</th>
                <th class="px-4 py-2">Attachment</th>
                <th class="px-4 py-2">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderData['disputes'] as $dispute)
            <tr class="border-t align-top">
                <td class="px-4 py-2">{{ $dispute->id }}</td>
                <td class="px-4 py-2">{{ $dispute->reason }}</td>
                <td class="px-4 py-2 
                    @if($dispute->status === 'admin_review') bg-red-100 text-red-800 font-semibold @endif
                ">{{ ucfirst(str_replace('_',' ',$dispute->status)) }}</td>
                <td class="px-4 py-2 space-y-1">
                    
                        <div class="bg-red-100 text-blue-800 p-2 rounded-lg">
            <strong>Buyer:</strong> {{ $dispute->buyer_comment }}
        </div>
                        <div class="bg-blue-100 text-blue-800 p-2 rounded-lg">
            <strong>Supplier:</strong> {{ $dispute->supplier_comment }}
        </div>
                        <div class="bg-purple-100 text-purple-800 p-2 rounded-lg">
            <strong>Admin:</strong> {{ $dispute->admin_comment }}
        </div>
                   
                </td>
                <td class="px-4 py-2">
                    @if($dispute->attachment)
                        <a href="{{ asset('storage/' . $dispute->attachment) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                    @else
                        -
                    @endif
                </td>


                @if(auth()->user()->role === 'admin')
    <form method="POST"
          action="{{ route('admin.orders.disputes.adminComment', $dispute->id) }}"
          class="mt-4 space-y-2">
        @csrf

        <label class="block text-sm font-medium text-gray-700">
            Admin comment
        </label>

        <textarea
            name="admin_comment"
            rows="3"
            required
            class="w-full border rounded-lg p-2 text-sm focus:ring focus:ring-purple-200"
            placeholder="Write admin decision or clarification...">{{ old('admin_comment', $dispute->admin_comment) }}</textarea>

        <div class="flex justify-end mb-4">
           
            <button
                type="submit"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">
                Send message
            </button>
        </div>
    </form>



    <form action="{{ route('admin.disputes.update', $dispute->id) }}" method="POST" class="mt-3 space-y-2">
        @csrf
        @method('PATCH')

       
        {{-- Status buttons --}}
        <div class="flex gap-2">
            <button
                name="status"
                value="pending"
                class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                Pending
            </button>

            <button
                name="status"
                value="resolved"
                class="px-3 py-1 text-sm rounded bg-green-100 text-green-800 hover:bg-green-200">
                Resolved
            </button>
        </div>
    </form>


@endif




                <td class="px-4 py-2">{{ $dispute->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif




        @else
            <p class="text-gray-500 italic">No disputes for this order.</p>
        @endif
    </div>

    {{-- Back --}}
    
    <div class="flex justify-start">
        <a href="{{ route('admin.orders.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to orders
        </a>
    </div>

</div>
@endsection
