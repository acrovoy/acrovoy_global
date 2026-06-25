<div class="mb-6">

@php
    $isReadonly = $rfq->status->isClosed();
@endphp

    {{-- BACK --}}
    <a href="
        @if($isBuyer ?? false)
           {{ route('buyer.rfqs.index') }} 
        @else
            {{ route('supplier.rfqs.index') }}
        @endif
    "
        class="text-sm text-gray-400 hover:text-gray-700 transition">
        ← Back to RFQs
    </a>

    <div class="mt-3 border border-gray-200 px-6 py-5 shadow-lg bg-gradient-to-b from-white via-gray-50 to-gray-100 rounded-lg">

        <div class="flex items-start justify-between gap-8">

            {{-- LEFT --}}
            <div class="flex-1 min-w-0">

                {{-- ID + STATUS --}}
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">

                    <span class="font-medium text-gray-700 tracking-wide">
                        {{ $rfq->public_id }}
                    </span>

                    <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-gray-600 uppercase tracking-wide">
                        {{ $rfq->status->label() }}
                    </span>

                </div>

                {{-- TITLE --}}
                <div class="flex items-center gap-2 mb-4">

                    <h1 class="text-xl font-semibold text-gray-900 leading-tight">
                        {{ $rfq->title }}
                    </h1>

                    @if($isBuyer ?? false)
                    @if($rfq->status->isDraft())
                    <button onclick="console.log('CLICK'); openRfqDrawer('title')"
                        class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                        title="Edit title">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z" />
                        </svg>

                    </button>
                    @else
                        <div class="text-xs text-gray-400">
                            
                        </div>
                    @endif
                    @endif

                </div>

                @php
                    $completed =
                    ($requirementsCompleted ? 1 : 0) +
                    ($participantsCompleted ? 1 : 0) +
                    ($deliveryCompleted ? 1 : 0);

                    $percent = ($completed / 3) * 100;
                    @endphp

                    @if($isBuyer ?? false)

                    {{-- RFQ READY CARD --}}
                    <div class="w-full bg-white border border-gray-200 rounded-xl p-4 shadow-sm">

                        <div class="flex items-center justify-between mb-3">

                            <div>
                                <div class="text-[11px] uppercase tracking-[0.15em] text-gray-400">
                                    RFQ Readiness
                                </div>

                                <div class="text-sm font-semibold text-gray-900">
                                    Completion Status
                                </div>
                            </div>

                            <div class="text-sm font-semibold text-gray-900">
                                {{ $completed }}/3
                            </div>

                        </div>

                        {{-- PROGRESS --}}
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden mb-4">
                            <div
                                class="h-full bg-gray-900 transition-all duration-500"
                                style="width: {{ $percent }}%">
                            </div>
                        </div>

                        {{-- ITEMS --}}
                        <div class="space-y-2">

                            <div class="flex items-center justify-between">

                                <div class="flex items-center gap-2">

                                    @if($requirementsCompleted)
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    @endif

                                    <span class="text-sm text-gray-700">
                                        Requirements
                                    </span>

                                </div>

                                <span class="text-xs font-medium {{ $requirementsCompleted ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $requirementsCompleted ? 'Completed' : 'Missing' }}
                                </span>

                            </div>

                            <div class="flex items-center justify-between">

                                <div class="flex items-center gap-2">

                                    @if($participantsCompleted)
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    @endif

                                    <span class="text-sm text-gray-700">
                                        Participants
                                    </span>

                                </div>

                                <span class="text-xs font-medium {{ $participantsCompleted ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $participantsCompleted ? 'Completed' : 'Missing' }}
                                </span>

                            </div>

                            <div class="flex items-center justify-between">

                                <div class="flex items-center gap-2">

                                    @if($deliveryCompleted)
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    @endif

                                    <span class="text-sm text-gray-700">
                                        Delivery Address
                                    </span>

                                </div>

                                <span class="text-xs font-medium {{ $deliveryCompleted ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $deliveryCompleted ? 'Completed' : 'Missing' }}
                                </span>

                            </div>

                        </div>

                    </div>

                    @endif



            </div>

            {{-- RIGHT --}}
            <div class="flex flex-col items-end gap-4 shrink-0">

                {{-- DEADLINE --}}
                <div class="text-right">

                    <div class="text-[11px] text-gray-400 uppercase tracking-wide">
                        Deadline
                    </div>

                    <div class="flex items-center justify-end gap-1">

                        <div class="text-sm font-medium text-gray-800">
                            {{ $rfq->closed_at?->format('M d, H:i') }}
                        </div>

                        @if($isBuyer ?? false)
                        
                            @if($rfq->status->isLocked())
                            
                            @else
                                <button onclick="openRfqDrawer('deadline')"
                                    class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                    title="Edit deadline">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z" />
                                    </svg>

                                </button>
                            @endif
                        @endif

                    </div>



                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-col items-end gap-4 min-w-[340px]">

                    

                    {{-- BUTTONS --}}
                    <div class="flex flex-wrap justify-end gap-2">

                        @if($isBuyer ?? false)

                            @if($rfq->status->canPublish())

                                @if($canPublish)

                                <form method="POST" action="{{ route('buyer.rfqs.publish', $rfq) }}">
                                    @csrf

                                    <button
                                        class="px-5 py-2.5 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-black transition shadow-sm">
                                        Publish RFQ
                                    </button>
                                </form>

                                @else

                                <button
                                    disabled
                                    class="px-5 py-2.5 text-sm font-medium bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                                    Publish RFQ
                                </button>

                                @endif

                            @endif

                            @if($rfq->status->isPublished())

                           <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'participants']) }}">
                                <button
                                    class="px-5 py-2.5 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Invite
                                </button>
                            </a>

                            @endif

                            @if($rfq->status->canClose())

                           <form method="POST" action="{{ route('buyer.rfqs.close', $rfq) }}">
                                @csrf

                                <button
                                    class="px-5 py-2.5 text-sm font-medium border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition">
                                    Close
                                </button>
                            </form>

                            @endif

                        @else

                        <button
                            class="px-5 py-2.5 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-black transition">
                            See Requirements
                        </button>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<div>
    {{-- DESCRIPTION --}}
    @if($rfq->description)

    <div class="text-sm text-gray-600 leading-relaxed max-w-2xl px-4 pb-4">

        {!! nl2br(e($rfq->description)) !!}

        @if($isBuyer ?? false)
        @if($rfq->status->isDraft())
        <button onclick="openRfqDrawer('description')"
            class="mt-2 p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
            title="Edit description">

            <svg xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z" />
            </svg>

        </button>
        @else
        <div class="text-xs text-gray-400">
            
        </div>
    @endif
        @endif

    </div>

    @endif


    @if($rfq->deliveryAddress)

    <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50 text-sm">

        <div class="flex items-start justify-between gap-4">

            <div>
                <div class="mb-2">
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wide">
                        Delivery Address For This RFQ
                    </h3>
                </div>
                @if($isBuyer ?? false)
                <div class="text-sm">

                    <div class="text-xs text-gray-500 uppercase tracking-wide">
                        Contact Person
                    </div>

                    <div class="text-gray-900 font-medium">
                        {{ $rfq->deliveryAddress->first_name }}
                        {{ $rfq->deliveryAddress->last_name }}
                    </div>

                </div>
                @endif
                <div class="mt-3 grid grid-cols-[90px_1fr] gap-y-1 text-sm">
                    @if($isBuyer ?? false)
                    <div class="text-gray-500">Street</div>
                    <div>{{ $rfq->deliveryAddress->street }}</div>
                    @endif
                    <div class="text-gray-500">City</div>
                    <div>{{ $rfq->deliveryAddress->city }}</div>

                    <div class="text-gray-500">Region</div>
                    <div>{{ $rfq->deliveryAddress->regionLocation?->name }}</div>

                    <div class="text-gray-500">Country</div>
                    <div> {{ \App\Models\Country::find($rfq->deliveryAddress->country)?->name ?? '—' }}</div>
                    @if($isBuyer ?? false)
                    <div class="text-gray-500">Phone</div>
                    <div>{{ $rfq->deliveryAddress->phone }}</div>
                    @endif
                </div>
            </div>
            @if($isBuyer ?? false)
            @if($rfq->status->isDraft())
            <button type="button"
                onclick="openAddressDrawer()"
                class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                Change Delivery Address and Contact
            </button>
            @else
            @endif
            @endif
        </div>


        @if($isBuyer ?? false)
        @else
        @if($rfq->deliveryAddress)

        <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-100 text-sm mt-3">

            <div class="flex items-start justify-between">

                {{-- текущий код адреса --}}

            </div>

            {{-- SHIPPING TEMPLATES --}}
            <div class="{{ $isReadonly ? 'opacity-60 pointer-events-none select-none' : '' }}">

    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-semibold text-gray-900">
            Available Shipping Templates
        </h4>

        <div class="text-gray-400 text-[12px]">
            Optional setup for suppliers offering delivery services.
        </div>
    </div>

    @if($shippingTemplates->count())
        <div class="space-y-2">

            @foreach($shippingTemplates as $template)

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition bg-white">

                    <div class="flex items-start justify-between gap-4">

                        <div class="flex-1">

                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Shipping Template
                                </span>

                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-md">
                                    {{ $template->title }}
                                </span>
                            </div>

                            @if(!empty($template->description))
                                <p class="text-gray-700 text-sm">
                                    {{ $template->description }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-2">

                                @if(!empty($template->delivery_time))
                                    <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg">
                                        <span class="text-sm text-blue-900 font-medium">
                                            Delivery Time
                                        </span>

                                        <span class="font-semibold text-blue-900">
                                            {{ $template->delivery_time }} days
                                        </span>
                                    </div>
                                @endif

                                @if(!empty($template->price))
                                    <div class="inline-flex items-center gap-2 bg-green-50 border border-green-100 px-3 py-1.5 rounded-lg">
                                        <span class="text-sm text-green-900 font-medium">
                                            Price
                                        </span>

                                        <span class="font-semibold text-green-900">
                                            ${{ number_format($template->price, 2) }}
                                        </span>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>
    @else

        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-4 text-center">

            <div class="text-sm text-gray-500">
                No matching shipping templates found for this destination.
            </div>

            @if(!$isBuyer && !$isReadonly)
                <div class="mt-2">
                    <a href="{{ route('supplier.shipping-templates.create') }}"
                       class="text-sm text-blue-600 hover:underline">
                        Create Shipping Template for this Destination
                    </a>
                </div>
            @endif

        </div>

    @endif

</div>

        </div>

        @endif
        @endif


    </div>

    @else
    @if($isBuyer)
    <div class="mb-3 p-3 border border-yellow-200 bg-yellow-50 rounded-lg">

        <div class="flex items-center justify-between gap-4">

            <div class="text-sm text-yellow-700">
                Delivery address not selected
            </div>

            <button type="button"
                onclick="openAddressDrawer()"
                class="shrink-0 px-3 py-1.5 text-sm border border-yellow-300 rounded-md bg-white hover:bg-yellow-100">
                Select Address
            </button>

        </div>

    </div>
    @endif
    @endif





</div>

<div id="rfq-drawer-overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>

<div id="rfq-drawer"
    class="fixed right-0 top-0 h-full w-[420px] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-[60] p-6">

    <h3 class="text-lg font-semibold mb-4" id="drawer-title">Edit</h3>

    <form method="POST" action="{{ route('buyer.rfqs.update.field', $rfq) }}">
        @csrf
        @method('PATCH')

        <input type="hidden" name="field" id="drawer-field">

        {{-- TITLE --}}
        <div id="field-title" class="hidden">
            <label class="text-sm text-gray-600">Title</label>
            <input type="text" name="title"
                value="{{ $rfq->title }}"
                class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- DESCRIPTION --}}
        <div id="field-description" class="hidden">
            <label class="text-sm text-gray-600">Description</label>
            <textarea name="description" rows="6"
                class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">{{ $rfq->description }}</textarea>
        </div>

        {{-- DEADLINE --}}
        <div id="field-deadline" class="hidden">
            <label class="text-sm text-gray-600">Deadline</label>
            <input type="datetime-local" name="closed_at"
                value="{{ optional($rfq->closed_at)->format('Y-m-d\TH:i') }}"
                class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <button type="button" onclick="closeRfqDrawer()"
                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancel
            </button>

            <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                Save
            </button>
        </div>

    </form>
</div>

@include('dashboard.buyer.orders.modals.select_saved_address')

<script>
/**
 * =========================
 * GLOBAL DRAWER CONTROLLER (FIXED)
 * =========================
 */

function closeAllDrawers() {
    const overlays = [
        'address-drawer-overlay',
        'rfq-drawer-overlay'
    ];

    const drawers = [
        'address-drawer',
        'rfq-drawer'
    ];

    overlays.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    drawers.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('translate-x-full');
    });
}

/**
 * =========================
 * ADDRESS DRAWER
 * =========================
 */
window.openAddressDrawer = function () {
    closeAllDrawers();

    const overlay = document.getElementById('address-drawer-overlay');
    const drawer = document.getElementById('address-drawer');

    if (!overlay || !drawer) return;

    overlay.classList.remove('hidden');
    drawer.classList.remove('translate-x-full');
};

window.closeAddressDrawer = function () {
    const overlay = document.getElementById('address-drawer-overlay');
    const drawer = document.getElementById('address-drawer');

    if (!overlay || !drawer) return;

    overlay.classList.add('hidden');
    drawer.classList.add('translate-x-full');
};

/**
 * =========================
 * RFQ DRAWER (FIXED CORE)
 * =========================
 */
window.openRfqDrawer = function (field) {

    closeAllDrawers();

    const overlay = document.getElementById('rfq-drawer-overlay');
    const drawer = document.getElementById('rfq-drawer');

    if (!overlay || !drawer) return;

    overlay.classList.remove('hidden');
    drawer.classList.remove('translate-x-full');

    // 🔥 1. ВСЕГДА очищаем ВСЕ поля
    document.querySelectorAll('[id^="field-"]').forEach(el => {
        el.classList.add('hidden');
    });

    // 🔥 2. проверка field
    if (!field) {
        console.warn('openRfqDrawer: field is empty');
        return;
    }

    const active = document.getElementById('field-' + field);

    if (!active) {
        console.warn('openRfqDrawer: field not found ->', field);
        return;
    }

    active.classList.remove('hidden');

    // 🔥 3. UI updates
    const hiddenInput = document.getElementById('drawer-field');
    const title = document.getElementById('drawer-title');

    if (hiddenInput) hiddenInput.value = field;
    if (title) title.innerText = 'Edit ' + field;
};

/**
 * =========================
 * CLOSE RFQ DRAWER
 * =========================
 */
window.closeRfqDrawer = function () {
    const overlay = document.getElementById('rfq-drawer-overlay');
    const drawer = document.getElementById('rfq-drawer');

    if (!overlay || !drawer) return;

    overlay.classList.add('hidden');
    drawer.classList.add('translate-x-full');
};

/**
 * =========================
 * OVERLAY EVENTS
 * =========================
 */
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('rfq-drawer-overlay')
        ?.addEventListener('click', closeRfqDrawer);

    document.getElementById('address-drawer-overlay')
        ?.addEventListener('click', closeAddressDrawer);
});
</script>