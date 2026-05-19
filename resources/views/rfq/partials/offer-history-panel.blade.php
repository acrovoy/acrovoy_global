{{-- ========================================= --}}
{{-- OFFER VERSION HISTORY PANEL --}}
{{-- ========================================= --}}

<aside class="w-full flex-shrink-0">

    <div class="sticky top-20 space-y-4">

        <div class="bg-white border border-gray-200 rounded-md shadow-sm overflow-hidden">

            {{-- HEADER --}}
            <div class="px-5 py-5 border-b border-gray-100">

                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="text-[15px] font-semibold text-gray-900">
                            Offer Version History
                        </h3>

                        <div class="text-xs text-gray-500 mt-1">
                            Review all submitted revisions and drafts.
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

            </div>

            {{-- TIMELINE --}}
            <div class="p-5">

                <div class="relative">

                    <div class="absolute left-[18px] top-0 bottom-0 w-px bg-gray-200"></div>

                    <div class="space-y-6">

                       

                        {{-- VERSIONS --}}
                        {{-- VERSIONS --}}
@foreach($versions as $version)

    @php
        $isActive = isset($offerVersion) && $offerVersion->id === $version->id;

        /**
         * =========================================================
         * HIDE BUYER COUNTER DRAFTS
         * =========================================================
         * Показываем:
         * - обычные версии
         * - counter версии только если submitted
         *
         * Скрываем:
         * - is_counter = 1 + status = draft
         */
        $isHiddenCounterDraft =
            ($version->is_counter ?? false)
            && $version->status === 'draft';

        if ($isHiddenCounterDraft) {
            continue;
        }
    @endphp

    @if($isActive)
        {{-- ACTIVE = SAME STYLE AS DRAFT --}}
        <button
            type="button"
            class="group relative w-full text-left"
            data-version-id="{{ $version->id }}"
            data-status="{{ $version->status }}"
        >

            <div class="flex gap-4">

                <div class="relative z-10 w-9 h-9 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14"/>
                    </svg>
                </div>

                <div class="flex-1 bg-blue-50 border border-blue-100 rounded-2xl p-4">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <div class="font-semibold text-sm text-gray-900">
                                Version {{ $version->version_number }} (Viewing)
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                {{ optional($version->created_at)->format('M d, Y · H:i') }}
                            </div>
                        </div>

                        <span class="px-2 py-1 rounded-md bg-blue-100 text-blue-700 text-[10px] font-medium">
                            {{ ucfirst($version->status) }}
                        </span>

                    </div>

                    <div class="mt-3 text-xs text-gray-600">
                        Currently selected version
                    </div>

                </div>

            </div>

        </button>
    @else
        {{-- NORMAL VERSION --}}
        <button
            type="button"
            class="group relative w-full text-left"
            data-version-id="{{ $version->id }}"
            data-status="{{ $version->status }}"
        >

            <div class="flex gap-4">

                <div class="relative z-10 w-9 h-9 rounded-full bg-green-100 border border-green-200 flex items-center justify-center flex-shrink-0">

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

                </div>

                <div class="flex-1 border border-gray-200 rounded-2xl p-4 transition-all group-hover:border-gray-300 group-hover:shadow-md bg-white">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <div class="font-semibold text-sm text-gray-900">
                                Version {{ $version->version_number }}
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                {{ optional($version->created_at)->format('M d, Y · H:i') }}
                            </div>
                        </div>

                        <span class="px-2 py-1 rounded-md
                            {{ $version->status === 'submitted'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-600' }}
                            text-[10px] font-medium">
                            {{ ucfirst($version->status) }}
                        </span>

                    </div>

                    <div class="mt-3 text-xs text-gray-600 leading-relaxed">
                        Submitted version snapshot.
                    </div>

                </div>

            </div>

        </button>
    @endif

@endforeach

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 p-5">

                <button
                    type="button"
                    class="w-full rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-all px-4 py-3 text-sm font-medium text-gray-700"
                >
                    View All Versions
                </button>

                <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">

                    <div class="flex gap-3 items-start">

                        <div class="mt-0.5 text-gray-400">🔒</div>

                        <div>
                            <div class="text-xs font-medium text-gray-700">
                                Submitted versions are read-only
                            </div>

                            <div class="text-xs text-gray-500 mt-1 leading-relaxed">
                                To make changes, create a new revision version.
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</aside>

{{-- ========================= --}}
{{-- VERSION SWITCH SCRIPT --}}
{{-- ========================= --}}
<script>
document.addEventListener('click', function (e) {

    const btn = e.target.closest('[data-version-id]');
    if (!btn) return;

    const versionId = btn.dataset.versionId;

    const url = new URL(window.location.href);
    url.searchParams.set('version', versionId);

    window.location.href = url.toString();
});
</script>