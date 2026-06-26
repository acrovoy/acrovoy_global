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
            <div class="flex justify-between px-5 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-[15px] font-semibold text-gray-900">
                        Negotiation History
                    </h3>

                    <div class="text-xs text-gray-500 mt-1">
                        Track supplier offer and your counter revisions.
                    </div>
                </div>

                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 text-gray-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
            </div>

            {{-- TIMELINE --}}
            <div class="p-5">

                <div class="relative">
                    <div class="absolute left-[18px] top-0 bottom-0 w-px bg-gray-200"></div>

                    <div class="space-y-6">



                      {{-- VERSIONS --}}
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
                                        @if($isActive)
                                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14" />
                                    </svg>
                                    @else
<svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-4 h-4 text-purple-600"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />

                                    </svg>

                                    @endif
                                        @else
                                        @if($isActive)
                             <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14" />
                                    </svg>                
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 text-green-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7" />
                    </svg>
                    
                    
                    @endif
                                        @endif
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="flex-1 rounded-2xl p-4 group-hover:shadow-md border transition-all
                                        {{ $isActive
                                            ? 'bg-blue-50 border-blue-100'
                                            : 'bg-white border-gray-200 group-hover:border-gray-300'
                                        }}">

                                        <div class="flex items-start justify-between gap-3">

                                            <div>
                                                <div class="font-semibold text-sm text-gray-900">
                                                    @if($isCounter)
                                                         <div class="font-semibold text-sm text-gray-900 leading-tight">
    Your Notes
</div>

<div class="text-gray-500 text-xs leading-none -mt-1.0">
    @if($version->version_number)
        version {{ $version->version_number }}
    @endif
</div>
                                                    @else
                                                        <div class="font-semibold text-sm text-gray-900 leading-tight">
    Offer
</div>

<div class="text-gray-500 text-xs leading-none -mt-1.0">
    version {{ $version->version_number }}
</div>
                                                    @endif

                                                    @if($isDraft)
                                                        <span class="text-blue-600"></span>
                                                    @endif
                                                </div>

                                                
                                            </div>
<span class="px-2 py-1 rounded-md text-[10px] font-medium
    {{ in_array($version->status, ['submitted', 'accepted'])
        ? 'bg-green-100 text-green-700'
        : 'bg-gray-100 text-gray-600'
    }}">
    {{ ucfirst($version->status) }}
</span>

                                        </div>
                                        @if($isActive)
                                        <div class="text-xs text-gray-500 mt-1">
                                                    {{ $version->created_at?->format('M d, Y · H:i') }}
                                                </div>
<div class="mt-1 text-xs text-gray-600">
                                        Currently selected version
                                    </div>
                                    @else
                                    <div class="text-xs text-gray-500 mt-1">
                                                    {{ $version->created_at?->format('M d, Y · H:i') }}
                                                </div>
                                    @endif
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