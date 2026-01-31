@extends('dashboard.layout')

@section('dashboard-content')
    <h2 class="text-2xl font-bold mb-6">My Orders</h2>

    @if($orders->isEmpty())
        <div class="text-gray-500 text-center py-10">
            You have no orders yet
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
    <div class="border rounded-lg p-4 flex justify-between items-center">
        <div>
            <p class="font-semibold">
                <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-blue-600 hover:underline">
                    Order #{{ $order->id }}
                </a>
            </p>
            <p class="text-sm text-gray-500">
                {{ $order->created_at->format('d M Y') }}
            </p>
        </div>

        <div class="text-right">
            <p class="font-semibold">
                ${{ number_format($order->total, 2) }}
            </p>
            <span class="text-sm px-2 py-1 rounded
                {{ $order->status === 'Completed'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-yellow-100 text-yellow-700' }}">
                {{ $order->status }}
            </span>
        </div>
    </div>
@endforeach
        </div>
    @endif
@endsection
