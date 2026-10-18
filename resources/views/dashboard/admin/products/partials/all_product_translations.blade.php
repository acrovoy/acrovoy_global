{{-- =========================================================
    ALL PRODUCT TRANSLATIONS
========================================================== --}}
<section
    x-data="{ translationsOpen: false }"
    class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"
>
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">
            Product content and translations
        </h2>
        <p class="mt-1 text-xs text-stone-500">
            Saved languages, names, subtitles and HTML descriptions
        </p>
    </div>

    @if ($translations->isNotEmpty())
        <div class="divide-y divide-stone-100">
            @foreach ($translations as $translation)
                <div
                    @if (!$loop->first)
                        x-show="translationsOpen"
                        x-transition
                        style="display: none;"
                    @endif
                    class="px-5 py-3"
                >
                    {{-- Language --}}
                    <div class="mb-2">
                        <span class="inline-flex rounded-md border border-stone-200 bg-stone-50 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-stone-700">
                            {{ $translation->locale }}
                        </span>
                    </div>

                    <dl class="space-y-0">
                        {{-- Name --}}
                        <div class="grid grid-cols-[90px_minmax(0,1fr)] gap-3 border-b border-stone-100 py-2">
                            <dt class="pt-0.5 text-xs text-stone-500">
                                Name
                            </dt>
                            <dd class="min-w-0 break-words text-sm font-medium text-stone-900">
                                {{ $translation->name ?: '—' }}
                            </dd>
                        </div>

                        {{-- Subtitle --}}
                        <div class="grid grid-cols-[90px_minmax(0,1fr)] gap-3 border-b border-stone-100 py-2">
                            <dt class="pt-0.5 text-xs text-stone-500">
                                Subtitle
                            </dt>
                            <dd class="min-w-0 whitespace-pre-line break-words text-sm text-stone-700">
                                {{ $translation->undername ?: '—' }}
                            </dd>
                        </div>

                        {{-- Description: render formatted HTML --}}
<div class="grid grid-cols-[90px_minmax(0,1fr)] gap-3 py-2">
    <dt class="pt-1 text-xs text-stone-500">
        Description
    </dt>

    <dd class="min-w-0">
        @if (filled($translation->description))
            <div class="max-h-96 overflow-y-auto rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-stone-700
                        [&_p]:mb-3 [&_p:last-child]:mb-0
                        [&_h2]:mb-2 [&_h2]:mt-4 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:text-stone-900
                        [&_h3]:mb-2 [&_h3]:mt-3 [&_h3]:font-semibold [&_h3]:text-stone-900
                        [&_ul]:my-3 [&_ul]:list-disc [&_ul]:pl-5
                        [&_ol]:my-3 [&_ol]:list-decimal [&_ol]:pl-5
                        [&_li]:mb-1">
                {!! $translation->description !!}
            </div>
        @else
            <span class="text-sm text-stone-400">—</span>
        @endif
    </dd>
</div>
                    </dl>
                </div>

                {{-- Toggle after the first language --}}
                @if ($loop->first && $translations->count() > 1)
                    <div class="border-t border-stone-100 bg-stone-50/70 px-5 py-3">
                        <button
                            type="button"
                            @click="translationsOpen = !translationsOpen"
                            :aria-expanded="translationsOpen.toString()"
                            class="inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm font-medium text-stone-600 transition hover:border-stone-300 hover:bg-stone-100 hover:text-stone-900"
                        >
                            <svg
                                class="h-4 w-4 transition-transform duration-200"
                                :class="{ 'rotate-180': translationsOpen }"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                            <span x-text="translationsOpen ? 'Hide other languages' : 'Show other languages'"></span>

                            <span class="rounded-md bg-stone-100 px-1.5 py-0.5 text-xs text-stone-500">
                                {{ $translations->count() - 1 }}
                            </span>
                        </button>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">
            No translations have been saved.
        </p>
    @endif
</section>