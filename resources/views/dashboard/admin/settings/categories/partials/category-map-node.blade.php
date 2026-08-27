@php
    $locale = app()->getLocale();
    $fallbackLocale = 'en';

    $categoryName = $category->translation($locale)?->name
        ?? $category->translation($fallbackLocale)?->name
        ?? $category->name
        ?? $category->slug
        ?? '—';

    $groupedAttributes = $category->attributes
        ->groupBy(fn($attribute) => $attribute->group_id ?? 0);

    $hasAttributes = $groupedAttributes->isNotEmpty();
@endphp


@if(!$hasAttributes)

    {{-- =========================================================
        COMPACT CATEGORY / SUBCATEGORY
    ========================================================== --}}
    <div class="border border-gray-200 bg-gray-50 rounded-xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-2.5">

            {{-- LEVEL --}}
            <div class="flex items-center justify-center w-6 h-6 rounded-md
                        bg-gray-200 text-[10px] font-semibold text-gray-500 shrink-0">
                {{ $level + 1 }}
            </div>

            {{-- NAME --}}
            <div class="flex items-center gap-2 min-w-0">

                <span class="text-sm font-medium text-gray-700 truncate">
                    {{ $categoryName }}
                </span>

                

                @if($category->slug)
                    <span class="text-[10px] text-gray-300 truncate">
                        {{ $category->slug }}
                    </span>
                @endif

            </div>

            {{-- CHILDREN COUNT --}}
            @if($category->children->isNotEmpty())
                <span class="ml-auto text-[10px] text-gray-400 shrink-0">
                    {{ $category->children->count() }}
                    {{ $category->children->count() === 1 ? 'subcategory' : 'subcategories' }}
                </span>
            @endif

        </div>


        {{-- CHILDREN --}}
        @if($category->children->isNotEmpty())

            <div class="px-3 pb-3 space-y-2">

                @foreach($category->children as $child)

                    @include(
                        'dashboard.admin.settings.categories.partials.category-map-node',
                        [
                            'category' => $child,
                            'level' => $level + 1,
                        ]
                    )

                @endforeach

            </div>

        @endif

    </div>


@else


    {{-- =========================================================
        CATEGORY WITH ATTRIBUTES
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">


        {{-- =====================================================
            CATEGORY HEADER
        ====================================================== --}}
        <div class="flex items-center justify-between gap-4
                    px-5 py-4
                    bg-gray-50
                    border-b border-gray-200">

            <div class="flex items-center gap-3 min-w-0">

                {{-- LEVEL --}}
                <div class="flex items-center justify-center
                            w-8 h-8 rounded-lg
                            bg-gray-900 text-white
                            text-xs font-semibold shrink-0">
                    {{ $level + 1 }}
                </div>


                {{-- CATEGORY NAME --}}
                <div class="min-w-0">

                    <div class="flex items-center gap-2 min-w-0">

                        <div class="text-sm font-semibold text-gray-900 truncate">
                            {{ $categoryName }}
                        </div>

                      

                    </div>


                    @if($category->slug)

                        <div class="mt-0.5 text-xs text-gray-400 truncate">
                            {{ $category->slug }}
                        </div>

                    @endif

                </div>

            </div>


            {{-- ATTRIBUTE COUNT --}}
            <div class="shrink-0 text-xs text-gray-400">

                {{ $category->attributes->count() }}

                {{ $category->attributes->count() === 1
                    ? 'attribute'
                    : 'attributes'
                }}

            </div>

        </div>


        {{-- =====================================================
            ATTRIBUTE GROUPS
        ====================================================== --}}
        <div class="p-5 space-y-8">

            @foreach($groupedAttributes as $groupId => $attributes)

                @php
                    $group = $attributes->first()?->group;

                    $groupName = $group?->translation($locale)?->name
    ?? $group?->translation($fallbackLocale)?->name
    ?? $group?->name
    ?? 'Other';

                   

                    $attributes = $attributes
                        ->sortBy(fn($attribute) => [
                            $attribute->pivot->sort_order ?? 0,
                            $attribute->id,
                        ])
                        ->values();
                @endphp


                {{-- =================================================
                    GROUP HEADER
                ================================================== --}}
                <section>

                    <div class="flex items-center gap-3 mb-3">

                        <div class="flex items-center gap-2 shrink-0">

                            <div class="text-xs font-semibold uppercase
                                        tracking-[0.14em] text-gray-500">
                                {{ $groupName }}
                            </div>

                           

                        </div>


                        <div class="h-px flex-1 bg-gray-200"></div>


                        <div class="text-[10px] font-medium text-gray-400 shrink-0">
                            {{ $attributes->count() }}
                        </div>

                    </div>


                    {{-- =================================================
                        3-COLUMN ATTRIBUTE GRID
                    ================================================== --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">


                        @foreach($attributes as $attribute)

                            @php

                                $attributeName = $attribute->translations
    ->firstWhere('locale', $locale)
    ?->name
    ?? $attribute->translations
        ->firstWhere('locale', $fallbackLocale)
        ?->name
    ?? $attribute->code;

                                

                                $isRequired = (bool) ($attribute->pivot->is_required ?? false);

                                $hasOptions =
                                    in_array(
                                        $attribute->type,
                                        ['select', 'multiselect']
                                    )
                                    && $attribute->options->isNotEmpty();

                                $options = $hasOptions
                                    ? $attribute->options
                                        ->sortBy(fn($option) => [
                                            $option->sort_order ?? 0,
                                            $option->id,
                                        ])
                                        ->values()
                                    : collect();

                            @endphp


                            {{-- =================================================
                                ATTRIBUTE CARD
                            ================================================== --}}
                            <div class="rounded-xl border border-gray-200
                                        bg-white
                                        overflow-hidden
                                        flex flex-col">


                                {{-- =================================================
                                    ATTRIBUTE HEADER
                                ================================================== --}}
                                <div class="p-4">

                                    <div class="flex items-start gap-3">

                                        {{-- SORT ORDER --}}
                                        <div class="flex items-center justify-center
                                                    w-7 h-7 rounded-md
                                                    bg-gray-100
                                                    text-[11px] font-semibold
                                                    text-gray-500
                                                    shrink-0">
                                            {{ $attribute->pivot->sort_order ?? 0 }}
                                        </div>


                                        {{-- MAIN INFO --}}
                                        <div class="min-w-0 flex-1">

                                            {{-- NAME --}}
                                            <div class="flex items-start gap-2">

                                                <div class="text-sm font-semibold
                                                            text-gray-900
                                                            leading-5">

                                                    {{ $attributeName }}

                                                   

                                                </div>


                                                @if($isRequired)

                                                    <span class="mt-0.5
                                                                 text-[9px]
                                                                 font-semibold
                                                                 uppercase
                                                                 tracking-wider
                                                                 text-red-500
                                                                 shrink-0">
                                                        Required
                                                    </span>

                                                @endif

                                            </div>


                                            {{-- META --}}
                                            <div class="mt-1.5 flex flex-wrap
                                                        items-center gap-x-2 gap-y-1
                                                        text-[11px] text-gray-400">

                                                <span>
                                                    {{ $attribute->code }}
                                                </span>

                                                <span class="text-gray-300">
                                                    •
                                                </span>

                                                <span>
                                                    {{ $attribute->type }}
                                                </span>


                                                {{-- UNIT --}}
                                                @if($attribute->type === 'measurement' && $attribute->unit)

                                                    <span class="text-gray-300">
                                                        •
                                                    </span>

                                                    <span class="inline-flex items-center
                                                                 px-1.5 py-0.5
                                                                 rounded
                                                                 bg-gray-100
                                                                 text-gray-500">
                                                        {{ $attribute->unit->name }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                {{-- =================================================
                                    OPTIONS
                                ================================================== --}}
                                @if($hasOptions)

                                    <div class="border-t border-gray-100
                                                bg-gray-50/50
                                                px-3 py-3">


                                        {{-- OPTIONS LABEL --}}
                                        <div class="flex items-center justify-between mb-2">

                                            <div class="text-[9px]
                                                        font-semibold
                                                        uppercase
                                                        tracking-wider
                                                        text-gray-400">
                                                Options
                                            </div>

                                            <div class="text-[9px] text-gray-300">
                                                {{ $options->count() }}
                                            </div>

                                        </div>


                                        {{-- OPTIONS LIST --}}
                                        <div class="rounded-lg
                                                    border border-gray-200
                                                    bg-white
                                                    overflow-hidden
                                                    max-h-64
                                                    overflow-y-auto">

                                            @foreach($options as $option)

                                                @php

                                                    $optionName = $option->translations
    ->firstWhere('locale', $locale)
    ?->value
    ?? $option->translations
        ->firstWhere('locale', $fallbackLocale)
        ?->value
    ?? $option->value
    ?? '—';

                                                    $optionRuName = $option->translations
                                                        ->firstWhere('locale', 'ru')
                                                        ?->value;

                                                @endphp


                                                <div class="flex items-start gap-2
                                                            px-2.5 py-2
                                                            border-b border-gray-100
                                                            last:border-b-0">


                                                    {{-- OPTION ORDER --}}
                                                    <span class="w-5 shrink-0
                                                                 text-[10px]
                                                                 font-medium
                                                                 text-gray-400">
                                                        {{ $option->sort_order ?? 0 }}
                                                    </span>


                                                    {{-- OPTION NAME --}}
                                                    <span class="text-xs
                                                                 leading-4
                                                                 text-gray-700">

                                                        {{ $optionName }}

                                                        @if($optionRuName && $optionRuName !== $optionName)

                                                            <span class="text-gray-400">
                                                                ({{ $optionRuName }})
                                                            </span>

                                                        @endif

                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </section>

            @endforeach

        </div>


        {{-- =====================================================
            CHILDREN
        ====================================================== --}}
        @if($category->children->isNotEmpty())

            <div class="p-4
                        bg-gray-50/50
                        space-y-3
                        border-t border-gray-200">

                @foreach($category->children as $child)

                    @include(
                        'dashboard.admin.settings.categories.partials.category-map-node',
                        [
                            'category' => $child,
                            'level' => $level + 1,
                        ]
                    )

                @endforeach

            </div>

        @endif

    </div>

@endif