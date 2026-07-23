@if(app()->environment('local'))

@php
$user = $user ?? auth()->user();


$ctx = $context ?? app(\App\Services\Company\ActiveContextService::class);
$identity = $ctx?->identity() ?? [];


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




        <div>
    mode:
    <span class="text-yellow-300">
        {{ $identity['mode'] ?? 'null' }}
    </span>
</div>

<div>
    entity_id:
    {{ $identity['entity_id'] ?? 'null' }}
</div>

<div>
    entity_type:
    {{ isset($identity['entity_type'])
        ? class_basename($identity['entity_type'])
        : 'null' }}
</div>

<div>
    platform_role:
    <span class="text-cyan-300">
        {{ $identity['platform_role'] ?? 'null' }}
    </span>
</div>

<div>
    company_role:
    <span class="text-purple-300">
        {{ $identity['company_role'] ?? 'null' }}
    </span>
</div>

<div>
    user_id:
    {{ $identity['user_id'] ?? 'null' }}
</div>

        <hr class="border-gray-700 my-1 mt-2">

        <div class="text-white font-bold mt-2">
            Session RAW
        </div>

        <div>active_mode: {{ session('active_mode') }}</div>
        <div>active_company_type: {{ session('active_company_type') }}</div>
        <div>active_company_id: {{ session('active_company_id') }}</div>



        <div>active_personal_mode: {{ session('active_personal_mode') }}</div>



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