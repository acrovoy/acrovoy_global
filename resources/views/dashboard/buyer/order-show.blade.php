@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-6">Order #{{ $order->id }}</h2>

{{-- Order Items --}}
<div class="bg-white p-6 rounded-lg shadow-sm mb-6">
    <h3 class="font-semibold text-lg mb-4">Items</h3>
    <ul class="divide-y">
        @foreach($order->items as $item)
        <li class="py-2 flex justify-between">
            <span>{{ $item->name }} × {{ $item->quantity }}</span>
            <span>${{ number_format($item->price, 2) }}</span>
        </li>
        @endforeach
    </ul>
    <div class="text-right mt-4 font-semibold">
        Total: ${{ number_format($order->total, 2) }}
    </div>
</div>

{{-- Status Timeline --}}
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h3 class="font-semibold text-lg mb-4">Status Timeline</h3>
    <ul class="relative border-l border-gray-200">
        @foreach($order->status_history as $status)
        <li class="mb-6 ml-4">
            <span class="absolute w-3 h-3 bg-blue-500 rounded-full -left-1.5 border border-white"></span>
            <div class="flex justify-between items-center">
                <span class="font-medium">{{ $status['status'] }}</span>
                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($status['date'])->format('d M Y') }}</span>
            </div>
        </li>
        @endforeach
    </ul> {{-- закрытие status timeline --}}
</div> {{-- конец таймлайна --}}

{{-- Footer --}}
<div class="mt-6 flex justify-between items-center text-sm text-gray-500">
    <span>
        Order date: {{ \Carbon\Carbon::parse($order->status_history[0]['date'])->format('d M Y') }}
    </span>
    <a href="{{ route('buyer.orders') }}"
       class="text-blue-600 hover:underline">
        ← Back to orders
    </a>
</div>



@endsection
