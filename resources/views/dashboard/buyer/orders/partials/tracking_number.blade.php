{{-- Tracking Number --}}
@if(!empty($order->tracking_number))
    <div class="border rounded p-3 bg-gray-50 mt-4">
        <h3 class="font-semibold mb-1">General Tracking Number</h3>
        <input type="text"
               readonly
               value="{{ $order->tracking_number }}"
               class="w-full border rounded px-3 py-2 text-sm bg-gray-100 cursor-text"
               onclick="this.select(); document.execCommand('copy');"
               title="Click to copy">
        <p class="text-gray-500 text-xs mt-1">Click on the field to copy the tracking number</p>
    </div>
@endif