{{-- ========================================= --}}
{{-- BUYER OFFER HISTORY PANEL --}}
{{-- ========================================= --}}

@php
    $currentCounterId = $counterVersion?->id ?? null;
@endphp

<aside class="w-full flex-shrink-0">

    <div class="sticky top-20 space-y-4">

        <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden">

            {{-- HEADER --}}
            <div class="px-5 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-[15px] font-semibold text-gray-900">
                        Buyer Negotiation History
                    </h3>

                    <div class="text-xs text-gray-500 mt-1">
                        Track supplier offer and your counter revisions.
                    </div>
                </div>
            </div>

            {{-- TIMELINE --}}
            <div class="p-5">

                <div class="relative">
                    <div class="absolute left-[18px] top-0 bottom-0 w-px bg-gray-200"></div>

                    <div class="space-y-6">

                        @foreach($versions as $version)

                            @php
                                $isActive = $counterVersion?->id === $version->id;
                                $isCounter = (bool) $version->is_counter;
                                $isDraft = $isCounter && $version->status === 'draft';
                            @endphp

                            {{-- ========================================= --}}
                            {{-- CLICK LOGIC --}}
                            {{-- ========================================= --}}
                            @php
                                if ($isCounter && $isDraft) {
                                    // buyer draft → EDIT MODE
                                    $href = route('buyer.rfqs.counter-offer.create', [
                                        'rfq' => $rfq->id,
                                        'offer' => $offer->id,
                                    ]) . '?version=' . $version->id;
                                } else {
                                    // ALL OTHER VERSIONS → BACK TO OFFERS TAB WITH SELECTED VERSION
    $href = route('rfqs.workspace', [
        'rfq' => $rfq->id,
        'tab' => 'offers',
        'offer' => $offer->id,
        'counter_version' => $version->id, // optional for read-only view
    ]);
                                }
                            @endphp

                            <a
                                href="{{ $href }}"
                                class="group relative w-full text-left block"
                            >

                                <div class="flex gap-4">

                                    {{-- ICON --}}
                                    <div class="relative z-10 w-9 h-9 rounded-full border flex items-center justify-center flex-shrink-0
                                        {{ $isActive
                                            ? 'bg-blue-100 border-blue-200'
                                            : ($isCounter ? 'bg-yellow-100 border-yellow-200' : 'bg-green-100 border-green-200')
                                        }}">
                                        @if($isCounter)
                                            💬
                                        @else
                                            ✓
                                        @endif
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="flex-1 rounded-2xl p-4 border transition-all
                                        {{ $isActive
                                            ? 'bg-blue-50 border-blue-100'
                                            : 'bg-white border-gray-200 group-hover:border-gray-300'
                                        }}">

                                        <div class="flex items-start justify-between gap-3">

                                            <div>
                                                <div class="font-semibold text-sm text-gray-900">
                                                    @if($isCounter)
                                                        Counter Offer {{ $version->version_number }}
                                                    @else
                                                        Supplier Offer {{ $version->version_number }}
                                                    @endif

                                                    @if($isDraft)
                                                        <span class="text-blue-600">(Draft)</span>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $version->created_at?->format('M d, Y · H:i') }}
                                                </div>
                                            </div>

                                            <span class="px-2 py-1 rounded-md text-[10px] font-medium
                                                {{ $version->status === 'submitted'
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-gray-100 text-gray-600'
                                                }}">
                                                {{ ucfirst($version->status) }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 p-5">

                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                    <div class="flex gap-3 items-start">
                        <div class="text-gray-400">⚖️</div>
                        <div>
                            <div class="text-xs font-medium text-gray-700">
                                Buyer negotiation mode
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Click draft counter offers to continue editing. Other versions are read-only.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</aside>