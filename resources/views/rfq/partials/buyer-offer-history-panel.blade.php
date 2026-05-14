{{-- ========================================= --}}
{{-- BUYER OFFER HISTORY PANEL --}}
{{-- ========================================= --}}

<aside class="w-full flex-shrink-0">

    <div class="sticky top-20 space-y-4">

        <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden">

            {{-- HEADER --}}
            <div class="px-5 py-5 border-b border-gray-100">

                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="text-[15px] font-semibold text-gray-900">
                            Buyer Negotiation History
                        </h3>

                        <div class="text-xs text-gray-500 mt-1">
                            Track supplier offer and your counter revisions.
                        </div>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                        🔄
                    </div>

                </div>

            </div>

            {{-- TIMELINE --}}
            <div class="p-5">

                <div class="relative">

                    <div class="absolute left-[18px] top-0 bottom-0 w-px bg-gray-200"></div>

                    <div class="space-y-6">

                        {{-- ========================= --}}
                        {{-- ALL VERSIONS TIMELINE --}}
                        {{-- ========================= --}}
                        @foreach($versions as $version)

                            @php
                                $isActive = isset($counterVersion)
                                    && $counterVersion
                                    && $counterVersion->id === $version->id;

                                $isCounter = $version->is_counter ?? false;
                            @endphp

                            <button
                                type="button"
                                class="group relative w-full text-left"
                                data-version-id="{{ $version->id }}"
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

                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="flex-1 rounded-2xl p-4 border transition-all
                                        {{ $isActive
                                            ? 'bg-blue-50 border-blue-100'
                                            : 'bg-white border-gray-200 group-hover:border-gray-300 group-hover:shadow-md'
                                        }}">

                                        <div class="flex items-start justify-between gap-3">

                                            <div>
                                                <div class="font-semibold text-sm text-gray-900">
                                                    @if($isCounter)
                                                        Counter Offer {{ $version->version_number }}
                                                    @else
                                                        Supplier Offer {{ $version->version_number }}
                                                    @endif

                                                    @if($isActive)
                                                        (Viewing)
                                                    @endif
                                                </div>

                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ optional($version->created_at)->format('M d, Y · H:i') }}
                                                </div>
                                            </div>

                                            <span class="px-2 py-1 rounded-md text-[10px] font-medium
                                                {{ $version->status === 'submitted'
                                                    ? ($isCounter ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700')
                                                    : 'bg-gray-100 text-gray-600'
                                                }}">
                                                {{ ucfirst($version->status) }}
                                            </span>

                                        </div>

                                        <div class="mt-3 text-xs text-gray-600">
                                            @if($isCounter)
                                                Buyer negotiation revision.
                                            @else
                                                Initial supplier proposal.
                                            @endif
                                        </div>

                                    </div>

                                </div>

                            </button>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 p-5">

                <button
                    class="w-full rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700"
                >
                    Create Counter Offer
                </button>

                <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">

                    <div class="flex gap-3 items-start">

                        <div class="text-gray-400">⚖️</div>

                        <div>
                            <div class="text-xs font-medium text-gray-700">
                                Buyer negotiation mode
                            </div>

                            <div class="text-xs text-gray-500 mt-1 leading-relaxed">
                                You can respond to supplier offers with counter revisions.
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</aside>

{{-- ========================= --}}
{{-- VERSION SWITCH --}}
{{-- ========================= --}}
<script>
document.addEventListener('click', function (e) {

    const btn = e.target.closest('[data-version-id]');
    if (!btn) return;

    const versionId = btn.dataset.versionId;

    const url = new URL(window.location.href);
    url.searchParams.set('counter_version', versionId);

    window.location.href = url.toString();
});
</script>