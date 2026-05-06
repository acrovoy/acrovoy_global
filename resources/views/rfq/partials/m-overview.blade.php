<div class="mb-6">

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
                <div class="flex items-center gap-2">

                    <h1 class="text-xl font-semibold text-gray-900 leading-tight">
                        {{ $rfq->title }}
                    </h1>

                    @if($isBuyer ?? false)
                        <button onclick="openRfqDrawer('title')"
                            class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                            title="Edit title">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                 class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z"/>
                            </svg>

                        </button>
                    @endif

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
                            {{ $rfq->closed_at?->format('M d, H:i') }}
                        </div>

                        @if($isBuyer ?? false)
                            <button onclick="openRfqDrawer('deadline')"
                                class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                title="Edit deadline">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z"/>
                                </svg>

                            </button>
                        @endif

                    </div>

                    

                </div>

                {{-- ACTIONS --}}
                <div class="flex gap-2">

                    @if($isBuyer ?? false)

                        @if($rfq->status->canPublish())
                            <button class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800">
                                Publish
                            </button>
                        @endif

                        @if($rfq->status->isPublished())
                            <button class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Invite
                            </button>
                        @endif

                        @if($rfq->status->canClose())
                            <button class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700">
                                Close
                            </button>
                        @endif

                    @else

                        <button class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800">
                            See Requirements
                        </button>

                    @endif

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
                            <button onclick="openRfqDrawer('description')"
                                class="mt-2 p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                title="Edit description">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5 12.075-12.075z"/>
                                </svg>

                            </button>
                        @endif

                    </div>

                @endif

</div>

<div id="rfq-drawer-overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>

<div id="rfq-drawer"
     class="fixed right-0 top-0 h-full w-[420px] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50 p-6">

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

<script>
function openRfqDrawer(field) {

    document.getElementById('rfq-drawer-overlay').classList.remove('hidden');
    document.getElementById('rfq-drawer').classList.remove('translate-x-full');

    // hide all fields
    document.getElementById('field-title').classList.add('hidden');
    document.getElementById('field-description').classList.add('hidden');
    document.getElementById('field-deadline').classList.add('hidden');

    // show selected
    document.getElementById('field-' + field).classList.remove('hidden');

    document.getElementById('drawer-field').value = field;
    document.getElementById('drawer-title').innerText = 'Edit ' + field;
}

function closeRfqDrawer() {
    document.getElementById('rfq-drawer-overlay').classList.add('hidden');
    document.getElementById('rfq-drawer').classList.add('translate-x-full');
}

document.getElementById('rfq-drawer-overlay')
    .addEventListener('click', closeRfqDrawer);
</script>