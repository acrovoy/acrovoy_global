@extends('project.layout')

@section('project-content')

<div class="mb-6">


    {{-- BACK --}}
    <a href="{{ route('buyer.projects.index') }} "
        class="text-sm text-gray-400 hover:text-gray-700 transition">
        ← Back to Projects
    </a>

    <div class="mt-3 border border-gray-200 px-6 py-5 shadow-lg bg-gradient-to-b from-white via-gray-50 to-gray-100 rounded-lg">

        <div class="flex items-start justify-between gap-8">

            {{-- LEFT --}}
            <div class="flex-1 min-w-0">

                {{-- ID + STATUS --}}
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">

                    <span class="font-medium text-gray-700 tracking-wide">
                        {{ $project->public_id }}
                    </span>

                    <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-gray-600 uppercase tracking-wide">
                        DRAFT
                    </span>

                </div>

                {{-- TITLE --}}
                <div class="flex items-center gap-2 mb-4">

                    <h1 class="text-xl font-semibold text-gray-900 leading-tight">

                        {{ $project->title }}
                    </h1>




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


                </div>


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
                            {{ $project->closed_at?->format('M d, H:i') }}
                        </div>


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


                    </div>



                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-col items-end gap-4 min-w-[340px]">



                    {{-- BUTTONS --}}
                    <div class="flex flex-wrap justify-end gap-2">







                        <form method="POST" action="">
                            @csrf

                            <button
                                class="px-5 py-2.5 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-black transition shadow-sm">
                                Publish RFQ
                            </button>
                        </form>










                        <a href="">
                            <button
                                class="px-5 py-2.5 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Invite
                            </button>
                        </a>

                        



                        <form method="POST" action="">
                            @csrf

                            <button
                                class="px-5 py-2.5 text-sm font-medium border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition">
                                Close
                            </button>
                        </form>





                    </div>

                </div>

            </div>

        </div>

    </div>
</div>









<div>

    {{-- DESCRIPTION --}}
    @if($project->description)

        <div class="text-sm text-gray-600 leading-relaxed max-w-2xl px-4 pb-4">

            {!! nl2br(e($project->description)) !!}


            @if($project->status->isDraft())
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
            <div class="text-xs text-gray-400"></div>
            @endif

        </div>

    @endif





</div>










<div id="rfq-drawer-overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>

<div id="rfq-drawer"
    class="fixed right-0 top-0 h-full w-[420px] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-[60] p-6">

    <h3 class="text-lg font-semibold mb-4" id="drawer-title">Edit</h3>

    <form method="POST" action="">
        @csrf
        @method('PATCH')

        <input type="hidden" name="field" id="drawer-field">

        {{-- TITLE --}}
        <div id="field-title" class="hidden">
            <label class="text-sm text-gray-600">Title</label>
            <input type="text" name="title"
                value="{{ $project->title }}"
                class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- DESCRIPTION --}}
        <div id="field-description" class="hidden">
            <label class="text-sm text-gray-600">Description</label>
            <textarea name="description" rows="6"
                class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">{{ $project->description }}</textarea>
        </div>

        {{-- DEADLINE --}}
        <div id="field-deadline" class="hidden">
            <label class="text-sm text-gray-600">Deadline</label>
            <input type="datetime-local" name="closed_at"
                value="{{ optional($project->closed_at)->format('Y-m-d\TH:i') }}"
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

@endsection