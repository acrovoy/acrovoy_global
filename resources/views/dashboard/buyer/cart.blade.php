@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="">
        <h2 class="text-2xl font-semibold text-gray-900">My Cart</h2>
        <p class="text-sm text-gray-500">
                View all your product items.
            </p>
    </div>

    @if($cartItems->isEmpty())
        <div class="text-gray-500 text-center py-10">
            Your cart is empty
        </div>
    @else
        {{-- Cart Items Grid --}}
        <div class="space-y-4">
            @foreach($cartItems as $item)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex flex-col md:flex-row justify-between items-start md:items-center hover:shadow-md transition">

                    {{-- Left: Image + Name --}}
                    <div class="flex items-start md:items-center gap-4 flex-1">
                        <div class="w-20 h-20 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                            <img src="{{ $item->product?->mainImage ? asset('storage/' . $item->product->mainImage->image_path) : asset('images/no-image.png') }}"
                                 alt="{{ $item->product?->translated_name ?? 'Product unavailable' }}"
                                 class="w-full h-full object-cover">
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $item->product?->translated_name ?? 'Product unavailable' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Price: ${{ number_format($item->price, 2) }}
                            </p>
                        </div>
                    </div>

                    {{-- Right: Quantity & Total --}}
                    <div class="flex items-center gap-4 mt-4 md:mt-0">

                        {{-- Quantity control --}}
                        <form action="{{ route('buyer.cart.update', $item->id) }}"
                              method="POST"
                              class="flex items-center gap-2 bg-gray-50 border rounded-lg px-2 py-1">
                            @csrf
                            @method('PATCH')

                            <button name="action" value="decrease" class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
                                −
                            </button>

                            <span class="min-w-[32px] text-center font-semibold">
                                {{ $item->quantity }}
                            </span>

                            <button name="action" value="increase" class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
                                +
                            </button>
                        </form>

                        {{-- Item total + remove --}}
                        <div class="text-right flex flex-col items-end gap-1 min-w-[90px]">
                            <p class="font-semibold text-gray-900">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </p>

                            <form action="{{ route('buyer.cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button class="text-sm text-red-500 hover:underline">
                                    Remove
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Cart Total --}}
        <div class="flex flex-col md:flex-row justify-between items-center mt-6 border-t border-gray-200 pt-4 gap-4">
            <span class="text-lg font-semibold text-gray-900">Total</span>
            <span class="text-lg font-bold text-gray-900">${{ number_format($total, 2) }}</span>

            {{-- Checkout --}}
            <a href="{{ route('buyer.orders.checkout') }}"
               class="px-6 py-3 bg-[#1877f2] text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                Checkout
            </a>
        </div>
    @endif
</div>
@endsection
