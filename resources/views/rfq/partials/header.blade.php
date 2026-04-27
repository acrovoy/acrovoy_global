<div class="mb-5">

    {{-- BACK --}}
    <a href="{{ route('buyer.rfqs.index') }}"
        class="text-sm text-gray-500 hover:text-gray-900 transition">
        ← Back to RFQs
    </a>

    {{-- HEADER BAR --}}
    <div class="mt-3 border border-gray-200 rounded-lg px-5 py-4 shadow-lg bg-gradient-to-b from-white via-gray-50 to-gray-100">

        <div class="flex items-start justify-between gap-6">

            {{-- LEFT --}}
            <div class="min-w-0 flex-1">

                {{-- TITLE --}}
                <div class="flex items-center gap-3 flex-wrap">

                    <h1 class="text-lg font-semibold text-gray-900">
                        RFQ #{{ $rfq->id }}
                    </h1>

                    <span class="text-xs px-2 py-0.5 rounded-full {{ $rfq->status->badgeClasses() }}">
                        {{ $rfq->status->label() }}
                    </span>

                </div>

                {{-- SUBTITLE --}}
                <div class="text-sm text-gray-600 mt-1">
                    {{ $rfq->title }}
                </div>

                {{-- META (same logic as table style) --}}
                <div class="flex flex-wrap gap-5 text-xs text-gray-500 mt-2">

                    <div class="flex gap-2">
                        <span class="text-gray-400">Visibility</span>
                        <span class="text-gray-800 font-medium">
                            {{ ucfirst($rfq->visibility_type->value) }}
                        </span>
                    </div>

                    @if($rfq->closed_at)
                    @php
                    $diff = now()->diffInSeconds($rfq->closed_at, false);
                    @endphp

                    <div class="flex gap-2">
                        <span class="text-gray-400">Deadline</span>

                        @if($diff > 0)
                        <span class="text-red-600 font-medium">
                            {{ now()->diffForHumans($rfq->closed_at, true) }} left
                        </span>
                        @else
                        <span class="text-gray-500 font-medium">
                            Closed
                        </span>
                        @endif
                    </div>
                    @endif

                </div>


                {{-- DESCRIPTION (secondary, collapsible) --}}
                @if($rfq->description)

                <details class="mt-3 group">

                    <summary class="text-xs text-gray-400 hover:text-gray-700 cursor-pointer select-none flex items-center gap-1 w-fit">

                        View description

                        <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-180"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>

                    </summary>

                    <div class="mt-2 text-sm text-gray-600 leading-relaxed whitespace-pre-line max-w-2xl">
                        {{ $rfq->description }}
                    </div>

                </details>

                @endif



            </div>

            {{-- RIGHT ACTIONS --}}
            <div class="flex items-center gap-2 shrink-0">

                @if($rfq->status->canPublish())
                <form method="POST" action="">
                    @csrf
                    <button class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800 transition">
                        Publish
                    </button>
                </form>
                @endif

                @if($rfq->status->canClose())
                <form method="POST" action="">
                    @csrf
                    <button class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Close
                    </button>
                </form>
                @endif

            </div>

        </div>

    </div>

</div>