{{-- Tracking number & Invoice --}}
<div class=" flex border rounded-lg p-4 bg-gray-50">
    <h3 class="font-semibold">Invoice (PDF):</h3>

        @if(!empty($order['invoice_file']))
            <a href="{{ asset('storage/' . $order->invoice_file) }}" target="_blank"
               class="text-blue-600 hover:underline text-sm ml-4">
                View current invoice
            </a>
        @endif

        
    
</div>