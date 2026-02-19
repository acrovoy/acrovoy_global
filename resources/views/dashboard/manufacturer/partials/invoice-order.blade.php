{{-- Tracking number & Invoice --}}
<div class="border rounded-lg p-4 bg-gray-50">
    <h3 class="font-semibold mb-3">Invoice (PDF)</h3>

    <form method="POST"
          action="{{ route('manufacturer.orders.update-tracking', $order['id']) }}"
          enctype="multipart/form-data"
          class="flex flex-col gap-3">
        @csrf

        @php
            $isCompleted = in_array($order['status'], ['completed', 'cancelled', 'pending']);
        @endphp

        {{-- Tracking Number --}}
        <!-- <label class="text-sm font-medium">Tracking Number</label>
        <input type="text"
               name="tracking_number"
               value="{{ $order['tracking_number'] ?? '' }}"
               class="border rounded px-3 py-2 text-sm w-full"
               placeholder="Enter tracking number"
               @if($isCompleted) disabled @endif> -->

        {{-- Invoice --}}
                
        <div class="relative w-full">
            <input type="file" name="invoice_file" accept="application/pdf"
                class="opacity-0 absolute inset-0 w-full h-full cursor-pointer"
                @if(in_array($order['status'], ['pending', 'cancelled'])) disabled @endif
                onchange="document.getElementById('invoice-label').innerText = this.files[0]?.name || 'Choose a file'">

            <button type="button"
                    class="w-full px-4 py-2 bg-gray-200 border border-gray-700 rounded hover:bg-gray-300 text-gray-700 text-sm text-left cursor-pointer"
                    @if(in_array($order['status'], ['pending', 'cancelled'])) disabled @endif>
                <span id="invoice-label">Choose a file</span>
            </button>
        </div>

        @if(!empty($order['invoice_file']))
            <a href="{{ asset('storage/' . $order['invoice_file']) }}" target="_blank"
               class="text-blue-600 hover:underline text-sm">
                View current invoice
            </a>
        @endif

        <button type="submit"
                class="self-start px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                @if($isCompleted) disabled @endif>
            Upload
        </button>
    </form>
</div>