{{-- Products --}}
<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs uppercase tracking-wide text-gray-500">
            Products in the order:
        </h3>

        
    </div>

    {{-- List --}}
    <div class="border-t border-gray-200 pt-4">
        @foreach($order->items as $item)
            <div class="py-1 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img
                        src="{{ $item->product && $item->product->mainImage
                            ? asset('storage/' . $item->product->mainImage->image_path)
                            : asset('images/no-photo.png') }}"
                        class="w-12 h-12 rounded object-contain bg-gray-50 border"
                    >
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $item->product_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $item->quantity }} × {{ number_format($item->price, 2) }} $
                        </p>
                    </div>
                </div>

                <div class="font-semibold text-gray-900">
                    {{ number_format($item->price * $item->quantity, 2) }} $
                </div>
            </div>
        @endforeach
    </div>

 {{-- Доставка --}}
    <div class="flex justify-between text-sm text-gray-700 pt-3 mt-6 border-t">
        <span class="text-xs uppercase tracking-wide text-gray-500">
            DELIVERY: <span class="text-xs text-gray-400">({{ $order->delivery_method ?? '-' }})</span>
        </span>

        @if($shipment?->provider_type === \App\Models\LogisticCompany::class)
            <span class="font-semibold">0.00 $</span>
        @elseif($shipment?->provider_type === \App\Models\Supplier::class)
            {{ number_format($order->delivery_price ?? 0, 2) }} $
        @else
            {{ number_format($order->delivery_price ?? 0, 2) }} $
        @endif

    </div>
  
    {{-- Итого --}}
    <div class="text-right mt-3 text-lg font-semibold">
        Total: {{ number_format($order->total, 2) }} $
    </div>

    @if(!empty($order->invoice_file))
        <a href="{{ route('buyer.orders.invoice', $order->id) }}"
           target="_blank"
           class="px-3 py-1.5 text-sm
                  border border-blue-300 text-blue-700
                  rounded-md
                  hover:bg-blue-50 hover:border-blue-400">
            Download Invoice
        </a>
    @else
        <button class="px-3 py-1.5 text-sm
                       border border-gray-300 text-gray-400
                       rounded-md cursor-not-allowed"
                disabled>
            Invoice not uploaded by the supplier yet
        </button>
    @endif
</div>