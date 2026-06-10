@php
    $translated = $category->translations
        ->firstWhere('locale', app()->getLocale());

    $name = $translated->name ?? 'No name';

    $hasChildren =
        $category->children->isNotEmpty()
        || $category->articles->isNotEmpty()
        || $category->children->filter(function ($child) {
            return $child->articles->isNotEmpty() || $child->children->isNotEmpty();
        })->isNotEmpty();

    $isActive =
        request()->route('slug') === $category->slug;
@endphp

<div class="mb-1">

    {{-- CATEGORY HEADER --}}
    <div
        onclick="toggleCategory('cat-{{ $category->id }}', 'arrow-{{ $category->id }}')"
        class="flex items-center justify-between
           px-3 py-2.5 rounded-xl
           cursor-pointer
           transition-all duration-200
              {{ $isActive ? 'bg-[#F4EFE6]' : 'hover:bg-[#F7F3EC]' }}">

        <div class="flex items-center gap-2">

            {{-- ARROW --}}
            @if($hasChildren)
                <svg id="arrow-{{ $category->id }}"
             class="w-4 h-4 text-[#9A9187] transition-transform duration-200"
             fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"/>
        </svg>
            @else
                <div class="w-4"></div>
            @endif

            {{-- NAME --}}
            

             <span class="text-[14px] font-medium text-[#2A2622]">
        {{ $name }}
    </span>


        </div>

    </div>

    {{-- CHILDREN CONTAINER --}}
    <div id="cat-{{ $category->id }}"
         class="hidden ml-4 mt-1 space-y-1 border-l border-[#EFE7DA] pl-3">

        {{-- ARTICLES --}}
        @foreach($category->articles as $article)

            @php
                $articleTitle = optional(
                    $article->translations
                        ->firstWhere('locale', app()->getLocale())
                )->title;
            @endphp

            <a href="{{ route('help.category', $category->slug) }}?article={{ $article->slug }}"
               class="block px-3 py-2 ml-5
          text-[13px] text-[#6F665C]
          rounded-lg
          hover:bg-[#F7F3EC]
          hover:text-[#1F1F1F]
          transition">

                {{ $articleTitle }}

            </a>

        @endforeach

        {{-- CHILD CATEGORIES (RECURSIVE) --}}
        @foreach($category->children as $child)
            @include('help.partials.category-tree', [
                'category' => $child
            ])
        @endforeach

    </div>

</div>

