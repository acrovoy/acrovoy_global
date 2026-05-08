<div class="">

    <h3 class="text-xl font-semibold mb-4">
        Custom Attributes
    </h3>

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-3">

        <div class="text-xs text-gray-500">
            Personal user fields (preferences, metadata, settings)
        </div>

        <button
            type="button"
            onclick="openAttributeDrawer()"
            class="text-sm text-gray-600 hover:text-gray-900">
            + Add attribute
        </button>

    </div>

    {{-- CUSTOM ATTRIBUTES --}}
    @if($customAttributes->count())

        <div class="mt-8">

            {{-- GROUPED ATTRIBUTES --}}
            <div class="space-y-5">

                @foreach(
                    $customAttributes
                        ->groupBy(fn($a) => $a->group?->name ?? 'General')
                    as $groupName => $attributes
                )

                    <div
                        x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }"
                        class="border border-gray-200 rounded-2xl bg-white overflow-hidden shadow-sm"
                    >

                        {{-- GROUP HEADER --}}
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full px-5 py-4 border-b border-gray-100 bg-gray-50
                                   flex items-center justify-between text-left
                                   hover:bg-gray-100/70 transition"
                        >

                            <div class="min-w-0">

                                <div class="flex items-center gap-3">

                                    <h4 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $groupName }}
                                    </h4>

                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-200 text-gray-600">
                                        {{ $attributes->count() }}
                                    </span>

                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ Str::plural('attribute', $attributes->count()) }}
                                </p>

                            </div>

                            {{-- ICON --}}
                            <svg
                                class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>

                        </button>

                        {{-- GROUP BODY --}}
                        <div
                            x-show="open"
                            x-collapse
                            class="p-5"
                        >

                            {{-- GRID --}}
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

                                @foreach($attributes as $attribute)

                                    <div
                                        class="border border-gray-100 rounded-2xl p-4 bg-gray-50/60
                                               hover:border-gray-200 hover:bg-white transition"
                                    >

                                        {{-- TOP --}}
                                        <div class="flex items-start justify-between gap-3 mb-3">

                                            <div class="min-w-0">

                                                <label class="block text-sm font-semibold text-gray-900 truncate">

                                                    {{ $attribute->name ?? $attribute->code }}

                                                </label>

                                                <div class="flex items-center gap-2 mt-1">

                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5
                                                               rounded-full text-[10px]
                                                               bg-gray-200 text-gray-600"
                                                    >
                                                        {{ strtoupper($attribute->type) }}
                                                    </span>

                                                    @if($attribute->is_required)
                                                        <span class="text-[10px] text-red-500">
                                                            Required
                                                        </span>
                                                    @endif

                                                </div>

                                            </div>

                                        </div>

                                        {{-- FIELD --}}
                                        <div>

                                            {{-- TEXT --}}
                                            @if($attribute->type === 'text')

                                                <input
                                                    type="text"
                                                    name="attributes[{{ $attribute->id }}][value]"
                                                    placeholder="Enter value..."
                                                    class="w-full border border-gray-300 rounded-xl
                                                           px-4 py-2.5 text-sm
                                                           focus:ring-2 focus:ring-gray-900/10
                                                           focus:border-gray-900 transition"
                                                >

                                            {{-- NUMBER --}}
                                            @elseif($attribute->type === 'number')

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    name="attributes[{{ $attribute->id }}][value]"
                                                    placeholder="0"
                                                    class="w-full border border-gray-300 rounded-xl
                                                           px-4 py-2.5 text-sm
                                                           focus:ring-2 focus:ring-gray-900/10
                                                           focus:border-gray-900 transition"
                                                >

                                            {{-- SELECT --}}
                                            @elseif($attribute->type === 'select')

                                                <select
                                                    name="attributes[{{ $attribute->id }}][value]"
                                                    class="w-full border border-gray-300 rounded-xl
                                                           px-4 py-2.5 text-sm bg-white
                                                           focus:ring-2 focus:ring-gray-900/10
                                                           focus:border-gray-900 transition"
                                                >

                                                    <option value="">
                                                        Select option
                                                    </option>

                                                    @foreach($attribute->options as $option)

                                                        <option value="{{ $option->id }}">
                                                            {{ $option->translations
                                                                ->firstWhere('locale', app()->getLocale())?->value }}
                                                        </option>

                                                    @endforeach

                                                </select>

                                            {{-- MULTISELECT --}}
                                            @elseif($attribute->type === 'multiselect')

                                                <div
                                                    class="border border-gray-200 rounded-xl
                                                           bg-white overflow-hidden"
                                                >

                                                    <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">

                                                        @foreach($attribute->options as $option)

                                                            <label
                                                                class="flex items-center gap-3 px-4 py-3
                                                                       hover:bg-gray-50 transition cursor-pointer"
                                                            >

                                                                <input
                                                                    type="checkbox"
                                                                    name="attributes[{{ $attribute->id }}][value][]"
                                                                    value="{{ $option->id }}"
                                                                    class="rounded border-gray-300
                                                                           text-gray-900
                                                                           focus:ring-gray-900"
                                                                >

                                                                <span class="text-sm text-gray-700">
                                                                    {{ $option->translations
                                                                        ->firstWhere('locale', app()->getLocale())?->value }}
                                                                </span>

                                                            </label>

                                                        @endforeach

                                                    </div>

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

</div>

{{-- OVERLAY --}}
<div id="attribute-overlay"
     class="fixed inset-0 bg-black/40 hidden z-40"></div>

@vite('resources/js/custom-attribute-form.js')