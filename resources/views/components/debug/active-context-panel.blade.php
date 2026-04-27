@if(app()->environment('local'))

@php
    $user = $user ?? auth()->user();

    try {
        $ctx = $context ?? app(\App\Services\Company\ActiveContextService::class);
    } catch (\Throwable $e) {
        $ctx = null;
    }

    $rfq = $rfq ?? null;
    $offer = $offer ?? null;
    $offerVersion = $offerVersion ?? null;
@endphp

<div id="debug-panel"
     class="fixed bottom-4 right-4 z-50 w-[360px]
     bg-black text-green-400 text-xs font-mono
     rounded-lg shadow-2xl">

    {{-- HEADER (CLICK TO TOGGLE) --}}
    <div id="debug-toggle"
         class="flex justify-between items-center px-4 py-2 cursor-pointer bg-gray-900 rounded-t-lg">

        <div class="text-white font-bold">
            🔎 ActiveContext Debug
        </div>

        <div class="text-yellow-300 text-xs">
            <span id="debug-state">−</span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div id="debug-content" class="p-4 space-y-1" style="display:none">

        {{-- USER --}}
        <div>
            user_id: {{ $user?->id ?? 'guest' }}
        </div>

        {{-- CONTEXT --}}
        <div>
            mode:
            <span class="text-yellow-300">
                {{ $ctx?->mode() ?? 'null' }}
            </span>
        </div>

        <div>
            company_id: {{ $ctx?->id() ?? 'null' }}
        </div>

        <div>
            company_type:
            {{ $ctx?->type() ? class_basename($ctx->type()) : 'null' }}
        </div>

        <div>
            role: {{ $ctx?->role() ?? 'null' }}
        </div>

        <hr class="border-gray-700 my-1">

        {{-- RFQ CONTEXT --}}
        <div class="text-white font-bold">
            RFQ Context
        </div>

        <div>rfq_id: {{ $rfq?->id ?? 'null' }}</div>
        <div>rfq_status: {{ $rfq?->status ?? 'null' }}</div>
        <div>rfq_stage: {{ $rfq?->stage?->name ?? 'null' }}</div>

        <hr class="border-gray-700 my-1">

        {{-- NEGOTIATION --}}
        <div class="text-white font-bold">
            Negotiation
        </div>

        <div>offer_id: {{ $offer?->id ?? 'null' }}</div>
        <div>offer_status: {{ $offer?->status ?? 'null' }}</div>
        <div>version: {{ $offerVersion?->version ?? 'null' }}</div>
        <div>parent_version: {{ $offerVersion?->parent_version_id ?? 'null' }}</div>

        <hr class="border-gray-700 my-1">

        {{-- LEGACY --}}
        <div class="text-white">
            Legacy Supplier Link
        </div>

        <div>
            supplier_id: {{ $user?->supplier?->id ?? 'null' }}
        </div>

        {{-- POLICY --}}
        @if($product)

            <hr class="border-gray-700 my-1">

            <div class="text-white">
                Policy Check
            </div>

            <div>
                can update product:
                @can('update', $product)
                    <span class="text-green-400">YES</span>
                @else
                    <span class="text-red-400">NO</span>
                @endcan
            </div>

        @endif

    </div>
</div>

{{-- TOGGLE SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('debug-panel');
    const toggle = document.getElementById('debug-toggle');
    const content = document.getElementById('debug-content');
    const state = document.getElementById('debug-state');

    let open = false;

    toggle.addEventListener('click', function () {
        open = !open;

        if (open) {
            content.style.display = 'block';
            state.innerText = '−';
            panel.style.width = '360px';
        } else {
            content.style.display = 'none';
            state.innerText = '+';
            panel.style.width = '200px';
        }
    });
});
</script>

@endif