    {{-- Notification --}}
    @if($order['provider_type'] === \App\Models\LogisticCompany::class && $r_order->delivery_price == 0)
        <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">

            <div class="mt-0.5">
                <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10A8 8 0 11.002 9.999 8 8 0 0118 10zM9 7a1 1 0 112 0v3a1 1 0 01-.293.707l-2 2a1 1 0 11-1.414-1.414L9 9.586V7z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <div class="font-medium">
                    Awaiting delivery details
                </div>
                <div class="mt-1 text-amber-700">
                    Waiting for the delivery price and estimated delivery time from the shipping provider.
                </div>
            </div>
        </div>
    @else
    @endif