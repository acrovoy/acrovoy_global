
@extends('layouts.app')

@section('content')



<div class="min-h-screen bg-[#F8F7F4]"
     dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    <section id="community-feed" class="container mx-auto px-6 lg:px-12 py-12 lg:py-16">

        <div class="grid lg:grid-cols-12 gap-10">

            {{-- MAIN FEED --}}
            <main class="lg:col-span-8 min-w-0">

                {{-- Section heading --}}
                <div class="flex flex-wrap items-end justify-between gap-5 mb-8">

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.18em] text-stone-400">
                            {{ __('community.network') }}
                        </span>

                        <h2 class="mt-2 text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                            {{ __('community.publications') }}
                        </h2>

                        <p class="mt-3 text-sm leading-6 text-stone-500">
                            {{ __('community.publications_description') }}
                        </p>
                    </div>

                    @auth
                        <a href="{{ route('community.posts.create') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700 transition">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                            </svg>

                            {{ __('community.create_post') }}
                        </a>
                    @endauth

                </div>

                {{-- Search --}}
                <form action="{{ route('community.search') }}" method="GET" class="mb-7">

                    <div class="relative">

                        <svg class="absolute start-4 top-1/2 -translate-y-1/2 w-5 h-5 text-stone-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
                        </svg>

                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="{{ __('community.search_placeholder') }}"
                            class="w-full rounded-xl border border-stone-200 bg-white py-4 ps-12 pe-5 text-sm text-slate-900 placeholder:text-stone-400 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-100">

                    </div>

                </form>

                {{-- Categories --}}
                <div class="mb-9">

                    <div class="flex items-center justify-between mb-4">

                        <h3 class="text-sm font-semibold text-slate-900">
                            {{ __('community.explore_categories') }}
                        </h3>

                        <span class="text-xs text-stone-400">
                            {{ trans_choice('community.categories_count', $categories->count(), ['count' => $categories->count()]) }}
                        </span>

                    </div>

                    <div class="flex flex-wrap gap-2">

                        <a href="{{ route('community.index') }}"
                           class="rounded-full border px-4 py-2.5 text-xs font-semibold transition
                           {{ !$categoryId ? 'border-slate-900 bg-slate-900 text-white' : 'border-stone-200 bg-white text-stone-600 hover:border-stone-400 hover:text-slate-900' }}">

                            {{ __('community.all_publications') }}

                        </a>

                        @foreach($categories as $category)

                            @if($category->getTranslation(app()->getLocale()))

                                <a href="{{ route('community.index', ['category' => $category->id]) }}"
                                   class="rounded-full border px-4 py-2.5 text-xs font-semibold transition
                                   {{ (int) $categoryId === (int) $category->id ? 'border-slate-900 bg-slate-900 text-white' : 'border-stone-200 bg-white text-stone-600 hover:border-stone-400 hover:text-slate-900' }}">

                                    {{ $category->getTranslation(app()->getLocale())->name }}

                                    <span class="ms-1 opacity-60">
                                        {{ $category->published_posts_count ?? 0 }}
                                    </span>

                                </a>

                            @endif

                        @endforeach

                    </div>

                </div>

                {{-- Feed --}}
<div class="space-y-7">

    @forelse($posts as $post)

        @php
            $locale = app()->getLocale();

            $translation = $post->translations->firstWhere('locale', $locale)
                ?? $post->translations->firstWhere('locale', 'en')
                ?? $post->translations->first();

            $categoryTranslation = $post->category?->getTranslation($locale);

            $author = $post->author;

            $authorAvatar = $author?->avatar()?->cdn_url
                ?? asset('images/default-avatar.png');

            $cover = $post->cover?->cdn_url
                ?? $post->media->first()?->cdn_url;

            $postUrl = route('community.posts.show', [
                'slug' => $post->slug,
            ]);

            

        $company = $post->company_info;
        @endphp

        <article class="overflow-hidden rounded-[24px] border border-stone-200 bg-white shadow-sm hover:shadow-md transition duration-300">

            {{-- Author header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 px-5 sm:px-7 pt-6">

                <div class="flex items-center gap-3 min-w-0">

                    <img
                        src="{{ $authorAvatar }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'"
                        alt="{{ $author?->name ?? __('community.member') }}"
                        class="w-11 h-11 rounded-full object-cover border border-stone-200">

                    <div class="min-w-0">

                        <div class="text-sm font-semibold text-slate-900 truncate">
                            {{ $author?->name ?? __('community.member') }}
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 mt-1 text-xs text-stone-500">

                            <span>
                               
    {{ $company['name'] }}
                            </span>

                            @if($post->published_at)
                                <span>·</span>
                                <span>
                                    {{ $post->published_at->locale($locale)->diffForHumans() }}
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

                <span class="shrink-0 rounded-full bg-stone-100 px-3 py-1.5 text-xs font-semibold text-stone-600">
                    {{ $categoryTranslation?->name ?? __('community.general') }}
                </span>

            </div>

            {{-- Content --}}
            <div class="px-5 sm:px-7 pt-5">

                <a href="{{ $postUrl }}" class="group">

                    <h3 class="text-xl sm:text-2xl font-bold leading-snug tracking-tight text-slate-900 group-hover:text-stone-600 transition">
                        {{ $translation?->title ?? __('community.untitled') }}
                    </h3>

                </a>

                @if($translation?->excerpt)

                    <p class="mt-3 text-sm sm:text-base leading-7 text-stone-600">
                        {{ $translation->excerpt }}
                    </p>

                @endif

            </div>

            {{-- Cover --}}
            @if($cover)

                <div class="px-5 sm:px-7 pt-5">

                    <a href="{{ $postUrl }}"
                       class="block overflow-hidden rounded-2xl bg-stone-100">

                        <img
                            src="{{ $cover }}"
                            alt="{{ $translation?->title ?? __('community.untitled') }}"
                            loading="lazy"
                            class="w-full h-[230px] sm:h-[320px] object-cover hover:scale-[1.02] transition duration-700">

                    </a>

                </div>

            @endif

             {{-- Footer --}}
                            <div class="flex flex-wrap items-center justify-between gap-4 mt-5 border-t border-stone-100 px-5 sm:px-7 py-5">

                                <div class="flex items-center gap-5 text-xs sm:text-sm text-stone-500">

                                    <span class="inline-flex items-center gap-1.5">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                                  d="M7 10v12m0-12 5-7 1.5 1.5L12 10h7a2 2 0 0 1 2 2l-2 8a2 2 0 0 1-2 2H7m0-12H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/>
                                        </svg>

                                        {{ number_format($post->likes_count) }} {{ __('community.likes') }}

                                    </span>

                                    <span class="inline-flex items-center gap-1.5">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                                  d="M8 10h8m-8 4h5m-9 7 3-3h10a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v12l2-1z"/>
                                        </svg>

                                        {{ number_format($post->comments_count) }} {{ __('community.comments') }}

                                    </span>

                                    <span class="hidden sm:inline-flex items-center gap-1.5">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                                  d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>

                                        {{ number_format($post->views_count) }} {{ __('community.views') }}

                                    </span>

                                </div>

                                <a href="{{ route('community.posts.show', ['slug' => $post->slug]) }}"
   class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 hover:text-stone-500 transition">

    {{ __('community.read_publication') }}

    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
              d="M5 12h14m-6-6 6 6-6 6"/>
    </svg>
</a>

                            </div>

        </article>

    @empty

        {{-- Empty state --}}
        <div class="rounded-[24px] border border-stone-200 bg-white px-6 py-16 text-center">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-stone-100 text-stone-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 8h10M7 12h6m-9 9 3-3h10a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3v12l1-1Z"/>
                </svg>
            </div>

            <h3 class="mt-5 text-xl font-bold text-slate-900">
                {{ __('community.no_publications') }}
            </h3>

            <p class="mt-2 max-w-md mx-auto text-sm leading-6 text-stone-500">
                {{ __('community.no_publications_description') }}
            </p>

            @auth
                <a href="{{ route('community.posts.create') }}"
                   class="inline-flex items-center gap-2 mt-6 rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-700 transition">

                    {{ __('community.create_first_post') }}

                </a>
            @endauth

        </div>

    @endforelse

</div>

                {{-- Pagination --}}
                @if($posts->hasPages())
                    <div class="mt-10">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @endif

            </main>

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-6">

                {{-- About Community --}}
                <div class="rounded-[22px] border border-stone-200 bg-white p-6 shadow-sm">

                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-stone-400">
                        {{ __('community.about') }}
                    </span>

                    <h3 class="mt-3 text-2xl font-bold leading-tight text-slate-900">
                        {{ __('community.about_title') }}
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-stone-600">
                        {{ __('community.about_description') }}
                    </p>

                    <div class="mt-6 space-y-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-stone-100 text-stone-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"/>
                                </svg>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ __('community.projects') }}
                                </div>
                                <div class="text-xs text-stone-500 mt-0.5">
                                    {{ __('community.projects_short') }}
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-stone-100 text-stone-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 5h16v14H4zM8 9h8m-8 4h8"/>
                                </svg>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ __('community.product_showcase') }}
                                </div>
                                <div class="text-xs text-stone-500 mt-0.5">
                                    {{ __('community.products_short') }}
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-stone-100 text-stone-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v18m9-9H3m3.5-6.5 11 11m0-11-11 11"/>
                                </svg>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ __('community.inspiration') }}
                                </div>
                                <div class="text-xs text-stone-500 mt-0.5">
                                    {{ __('community.inspiration_short') }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Categories sidebar --}}
                <div class="rounded-[22px] border border-stone-200 bg-white p-6 shadow-sm">

                    <div class="flex items-center justify-between mb-5">

                        <h3 class="font-bold text-slate-900">
                            {{ __('community.browse_categories') }}
                        </h3>

                        <span class="text-xs text-stone-400">
                            {{ $categories->count() }}
                        </span>

                    </div>

                    <div class="space-y-1">

                        @foreach($categories as $category)

                            @if($category->getTranslation(app()->getLocale()))

                                <a href="{{ route('community.index', ['category' => $category->id]) }}"
                                   class="flex items-center justify-between gap-3 rounded-xl px-3 py-3 text-sm transition
                                   {{ (int) $categoryId === (int) $category->id ? 'bg-stone-100 text-slate-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-slate-900' }}">

                                    <span>
                                        {{ $category->getTranslation(app()->getLocale())->name }}
                                    </span>

                                    <span class="text-xs text-stone-400">
                                        {{ $category->published_posts_count ?? 0 }}
                                    </span>

                                </a>

                            @endif

                        @endforeach

                    </div>

                </div>

                {{-- Create post CTA --}}
                <div class="relative overflow-hidden rounded-[22px] bg-slate-900 p-7 text-white">

                    <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/5"></div>
                    <div class="absolute -bottom-16 -left-12 h-44 w-44 rounded-full bg-white/5"></div>

                    <div class="relative">

                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                            {{ __('community.your_voice_matters') }}
                        </span>

                        <h3 class="mt-4 text-2xl font-bold leading-tight">
                            {{ __('community.share_work_title') }}
                        </h3>

                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            {{ __('community.share_work_description') }}
                        </p>

                        @auth
                            <a href="{{ route('community.posts.create') }}"
                               class="inline-flex items-center justify-center gap-2 mt-6 w-full rounded-xl bg-white px-5 py-3.5 text-sm font-semibold text-slate-900 hover:bg-stone-100 transition">

                                {{ __('community.create_publication') }}

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6"/>
                                </svg>

                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center gap-2 mt-6 w-full rounded-xl bg-white px-5 py-3.5 text-sm font-semibold text-slate-900 hover:bg-stone-100 transition">

                                {{ __('community.sign_in_to_participate') }}

                            </a>
                        @endauth

                    </div>

                </div>

                {{-- Inspiration --}}
                <div class="overflow-hidden rounded-[22px] border border-stone-200 bg-white">

                    <img
                        src="{{ asset('images/home/banners/outdoor.jpg') }}"
                        alt="{{ __('community.inspiration') }}"
                        loading="lazy"
                        class="w-full h-48 object-cover">

                    <div class="p-5">

                        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">
                            {{ __('community.acrovoy_inspiration') }}
                        </span>

                        <h3 class="mt-2 text-lg font-bold text-slate-900">
                            {{ __('community.inspiration_card_title') }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-stone-500">
                            {{ __('community.inspiration_card_description') }}
                        </p>

                    </div>

                </div>

            </aside>

        </div>

    </section>

</div>

@endsection
