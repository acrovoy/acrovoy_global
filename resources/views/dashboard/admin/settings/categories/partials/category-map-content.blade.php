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


<div class="px-4 py-4">

    {{-- ATTRIBUTES --}}
    @if($hasAttributes)

        <div class="space-y-4">

            @foreach($groupedAttributes as $groupId => $attributes)

                @php
                    $attributes = $attributes
                        ->sortBy(fn($attribute) => [
                            $attribute->pivot->sort_order ?? 0,
                            $attribute->id,
                        ])
                        ->values();

                    $group = $attributes->first()?->group;

                    $groupName = $group?->translations
                        ->firstWhere('locale', $locale)
                        ?->name
                        ?? $group?->translations
                            ->firstWhere('locale', $fallbackLocale)
                            ?->name
                        ?? $group?->name
                        ?? '—';
                @endphp

                <div
                    class="
                        border
                        border-gray-200
                        rounded-lg
                        overflow-hidden
                        
                    "
                >

                    {{-- GROUP --}}
                    <div
                        class="
                            px-4
                            py-2
                            bg-gray-50
                            border-b
                            border-gray-200
                            text-sm
                            font-semibold
                            text-gray-700
                        "
                    >
                        {{ $groupName }}
                    </div>


                    {{-- ATTRIBUTES --}}
                    <div class="divide-y divide-gray-100">

                        @foreach($attributes as $attribute)

                            @php
                                $attributeName = $attribute->translations
                                    ->firstWhere('locale', $locale)
                                    ?->name
                                    ?? $attribute->translations
                                        ->firstWhere('locale', $fallbackLocale)
                                        ?->name
                                    ?? $attribute->code;

                                $isRequired = (bool) (
                                    $attribute->pivot->is_required ?? false
                                );

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


                            <div class="px-4 py-3">

                                {{-- ATTRIBUTE HEADER --}}
                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">

                                        <div class="flex items-center gap-2">

                                          {{-- ATTRIBUTE SORT ORDER --}}
    <div
        class="
            flex
            items-center
            justify-center
            w-4
            h-4
            rounded-md
            bg-gray-100
            text-[11px]
            font-semibold
            text-gray-500
            shrink-0
        "
    >
        {{ $attribute->pivot->sort_order ?? 0 }}
    </div>

                                            <span
                                                class="
                                                    text-sm
                                                    font-medium
                                                    text-gray-900
                                                "
                                            >
                                                {{ $attributeName }}
                                            </span>

                                            @if($isRequired)

                                                <span
                                                    class="
                                                        text-xs
                                                        font-medium
                                                        text-red-500
                                                    "
                                                >
                                                    Required
                                                </span>

                                            @endif

                                        </div>

                                        <div class="-mt-0.5 text-xs text-gray-400 mb-2">
                                            {{ $attribute->code }}
                                        </div>

                                    </div>


                                    <div
                                        class="
                                            shrink-0
                                            text-xs
                                            text-gray-400
                                        "
                                    >
                                        {{ $attribute->type }}
                                    </div>

                                </div>


                                {{-- OPTIONS --}}
                                @if($hasOptions)

                                    <div
    class="
        rounded-lg
        border
        border-gray-200
        bg-white
        overflow-hidden
        max-h-64
        overflow-y-auto
    "
>
    <div class="flex flex-col">

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

            <div
                class="
                    flex
                    items-start
                    gap-2
                    px-2.5
                    py-2
                    border-b
                    border-gray-100
                    last:border-b-0
                "
            >

                <span
                    class="
                        w-5
                        shrink-0
                        text-[10px]
                        font-medium
                        text-gray-400
                    "
                >
                    {{ $option->sort_order ?? 0 }}
                </span>

                <span
                    class="
                        text-xs
                        leading-4
                        text-gray-700
                    "
                >

                    {{ $optionName }}

                    @if(
                        $optionRuName
                        && $optionRuName !== $optionName
                    )

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

                </div>

            @endforeach

        </div>

    @else

        <div
            class="
                
                text-sm
                text-gray-400
            "
        >
            
        </div>

    @endif


    {{-- CHILDREN --}}
    @if($category->children->isNotEmpty())

        <div class="space-y-3">

            @foreach($category->children as $child)

                @include(
                    'dashboard.admin.settings.categories.partials.category-map-node',
                    [
                        'category' => $child,
                        
                    ]
                )

            @endforeach

        </div>

    @endif

</div>