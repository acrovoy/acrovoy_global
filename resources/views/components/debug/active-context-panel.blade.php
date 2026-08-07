@if(app()->environment('local'))

@php
$user = $user ?? auth()->user();

$ctx = $context ?? app(\App\Services\Company\ActiveContextService::class);

$identity = $ctx->identity();

$entity = $ctx->entity();
$participant = $ctx->participant();

$supplier = $ctx->supplier();
$buyer = $ctx->buyer();

$product = $product ?? null;
$rfq = $rfq ?? null;
$offer = $offer ?? null;
$offerVersion = $offerVersion ?? null;
@endphp

<div class="fixed bottom-0 right-0 z-[99999] w-[430px] text-xs bg-black/95 text-gray-200 border border-gray-700 rounded-t-lg shadow-2xl">

    {{-- HEADER --}}
    <div id="debug-toggle"
         class="flex justify-between items-center px-4 py-2 cursor-pointer bg-gray-900 rounded-t-lg">

        <div class="font-bold text-white">
            🔎 ActiveContext Debug
        </div>

        <div class="text-yellow-300">
            <span id="debug-state">−</span>
        </div>

    </div>

    {{-- CONTENT --}}
    <div id="debug-content" class="p-4 space-y-1">

        {{-- ========================================================= --}}
        {{-- IDENTITY --}}
        {{-- ========================================================= --}}

        <div class="text-white font-bold">
            Identity
        </div>

        <div>
            mode:
            <span class="text-yellow-300">
                {{ $identity['mode'] ?? '-' }}
            </span>
        </div>

        <div>
            platform_role:
            <span class="text-green-300">
                {{ $identity['platform_role'] ?? '-' }}
            </span>
        </div>

        <div>
            company_role:
            <span class="text-green-300">
                {{ $identity['company_role'] ?? '-' }}
            </span>
        </div>

        <div>
            entity_type:
            <span class="text-cyan-300">
                {{ class_basename($identity['entity_type'] ?? '') }}
            </span>
        </div>

        <div>
            entity_id:
            <span class="text-cyan-300">
                {{ $identity['entity_id'] ?? '-' }}
            </span>
        </div>

        
        
        <div class="text-white font-bold">
    Participant
</div>

@if($participant)

    <div>
        type:
        <span class="text-purple-300">
            {{ $participant['type'] }}
        </span>
    </div>

    <div>
        id:
        <span class="text-purple-300">
            {{ $participant['id'] }}
        </span>
    </div>

@else

    <div class="text-red-400">
        null
    </div>

@endif




        <hr class="border-gray-700 my-2">

        {{-- ========================================================= --}}
        {{-- RESOLVED ENTITY --}}
        {{-- ========================================================= --}}

        <div class="text-white font-bold">
            Resolved Entity
        </div>

        <div>
            entity():
            <span class="text-blue-300">
                {{ $entity ? class_basename($entity::class).' #'.$entity->getKey() : 'null' }}
            </span>
        </div>

        <div>
            supplier():
            @if($supplier)
                <span class="text-green-400">
                    Supplier #{{ $supplier->id }}
                </span>
            @else
                <span class="text-red-400">null</span>
            @endif
        </div>

        @if($supplier)

            <div class="ml-3 text-gray-400">
                supplierable_type:
                {{ class_basename($supplier->supplierable_type) }}
            </div>

            <div class="ml-3 text-gray-400">
                supplierable_id:
                {{ $supplier->supplierable_id }}
            </div>

        @endif

        <div>
            buyer():
            @if($buyer)
                <span class="text-green-400">
                    Buyer #{{ $buyer->id }}
                </span>
            @else
                <span class="text-red-400">null</span>
            @endif
        </div>

        @if($buyer)

            <div class="ml-3 text-gray-400">
                buyerable_type:
                {{ class_basename($buyer->buyerable_type) }}
            </div>

            <div class="ml-3 text-gray-400">
                buyerable_id:
                {{ $buyer->buyerable_id }}
            </div>

        @endif

        <hr class="border-gray-700 my-2">

        {{-- ========================================================= --}}
        {{-- HELPERS --}}
        {{-- ========================================================= --}}

        <div class="text-white font-bold">
            Context Helpers
        </div>

        <div>isGuest(): {{ $ctx->isGuest() ? 'YES' : 'NO' }}</div>
        <div>isPersonal(): {{ $ctx->isPersonal() ? 'YES' : 'NO' }}</div>
        <div>isCompany(): {{ $ctx->isCompany() ? 'YES' : 'NO' }}</div>

        <div>isSupplier(): {{ $ctx->isSupplier() ? 'YES' : 'NO' }}</div>
        <div>isBuyer(): {{ $ctx->isBuyer() ? 'YES' : 'NO' }}</div>

        <hr class="border-gray-700 my-2">


        
        {{-- ========================================================= --}}
        {{-- SESSION --}}
        {{-- ========================================================= --}}

        <div class="text-white font-bold">
            Session
        </div>

        <div>active_mode: {{ session('active_mode') }}</div>
        <div>active_company_type: {{ class_basename(session('active_company_type')) }}</div>
        <div>active_company_id: {{ session('active_company_id') }}</div>
        <div>active_personal_mode: {{ session('active_personal_mode') }}</div>

        @if($product)

            <hr class="border-gray-700 my-2">

            <div class="text-white font-bold">
                Policy
            </div>

            <div>
                update product:
                @can('update', $product)
                    <span class="text-green-400">YES</span>
                @else
                    <span class="text-red-400">NO</span>
                @endcan
            </div>

        @endif

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById('debug-toggle');
    const content = document.getElementById('debug-content');
    const state = document.getElementById('debug-state');

    toggle.addEventListener('click', () => {

        if (content.style.display === 'none') {

            content.style.display = 'block';
            state.textContent = '−';

        } else {

            content.style.display = 'none';
            state.textContent = '+';

        }

    });

});

</script>

@endif