@php
    $level = $level ?? 0;

    $locale = app()->getLocale();
    $fallbackLocale = 'en';

    $categoryName = $category->translation($locale)?->name
        ?? $category->translation($fallbackLocale)?->name
        ?? $category->name
        ?? $category->slug
        ?? '—';

    $children = $category->children ?? collect();
@endphp

<div
    class="
        rounded-2xl
        border
        border-gray-200
        bg-white
        shadow-sm
        overflow-hidden
    "
    data-category-node
    data-category-id="{{ $category->id }}"
    data-category-level="{{ $level }}"
    data-category-loaded="false"
>

    {{-- CATEGORY HEADER --}}
    <button
        type="button"
        class="
            w-full
            flex
            items-center
            gap-3
            cursor-pointer
            select-none
            px-5
            py-3
            bg-white
            hover:bg-gray-50
        "
        data-category-toggle
        data-category-id="{{ $category->id }}"
    >

        {{-- ARROW --}}
        <svg
            class="
                h-4
                w-4
                shrink-0
                text-gray-400
                transition-transform
                duration-150
            "
            data-category-arrow
            viewBox="0 0 20 20"
            fill="currentColor"
        >
            <path
                fill-rule="evenodd"
                d="M7.21 14.77a.75.75 0 01-.02-1.06L10.9 10 7.19 6.29a.75.75 0 111.06-1.06l4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 01-1.04 0z"
                clip-rule="evenodd"
            />
        </svg>

        {{-- LEVEL --}}
        <div
            class="
                flex
                items-center
                justify-center
                w-7
                h-7
                rounded-md
                bg-gray-100
                text-[10px]
                font-semibold
                text-gray-500
                shrink-0
            "
        >
            {{ $category->sort_order }}
        </div>

        {{-- CATEGORY NAME --}}
        <div
            class="
                flex
                items-center
                gap-2
                min-w-0
                flex-1
            "
        >

            <span
                class="
                    text-sm
                    font-semibold
                    text-gray-900
                    truncate
                "
            >
                {{ $categoryName }}
            </span>

            @if($category->slug)

                <span
                    class="
                        text-[10px]
                        text-gray-300
                        truncate
                    "
                >
                    {{ $category->slug }}
                </span>

            @endif

        </div>

        {{-- CHILDREN COUNT --}}
        @if($children->isNotEmpty())

            <span
                class="
                    text-xs
                    text-gray-400
                    shrink-0
                "
            >
                {{ $children->count() }}

                {{ $children->count() === 1
                    ? 'child'
                    : 'children'
                }}
            </span>

        @endif

        {{-- LOADING --}}
        <span
            class="
                hidden
                text-xs
                text-gray-400
                shrink-0
            "
            data-category-loading
        >
            Loading...
        </span>

    </button>


    {{-- LAZY-LOADED CONTENT --}}
    <div
        class="
            hidden
            border-t
            border-gray-200
        "
        data-category-content
        data-category-id="{{ $category->id }}"
    ></div>

</div>