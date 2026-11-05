@extends('layouts.app')

@section('title', $translation?->seo_title ?: $translation?->title)

@section('meta_description', $translation?->seo_description ?: $translation?->excerpt)

@section('content')

@php
    $locale = app()->getLocale();

    $author = $post->author;
    $category = $post->category;
    $categoryTranslation = $category?->getTranslation($locale);

    $categoryName = $categoryTranslation?->name
        ?? $categoryTranslation?->title
        ?? $category?->name
        ?? 'Community';

    $authorName = $author?->name ?? 'Community member';
    $authorAvatar = $author?->avatar()?->cdn_url
        ?? asset('images/default-avatar.png');

    $cover = $post->cover;
    $postMedia = $post->media ?? collect();

    if (!$cover) {
        $cover = $postMedia->firstWhere('is_main', true)
            ?? $postMedia->first();
    }

    $coverUrl = $cover?->cdn_url;

    $gallery = $postMedia->filter(function ($media) use ($cover) {
        return !$cover || $media->id !== $cover->id;
    });

    $products = $post->products ?? collect();

    $comments = $post->publishedComments ?? collect();

    $commentsCount = $post->published_comments_count
        ?? $post->comments_count
        ?? $comments->count();

    $likesCount = $post->likes_count ?? 0;
    $viewsCount = $post->views_count ?? 0;

    $publishedAt = $post->published_at ?? $post->created_at;
    
    

    $profileUrl = null;

    if (Route::has('profile.show') && $author) {
        $profileUrl = route('profile.show', $author->id);
    }

    $createPostUrl = Route::has('community.posts.create')
        ? route('community.posts.create')
        : '#create-post';

    $communityUrl = Route::has('community.index')
        ? route('community.index')
        : '#community';
@endphp

<div class="min-h-screen bg-[#F8F7F4]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-12 py-8 lg:py-12">

        {{-- Breadcrumbs --}}
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-8">
            <a href="{{ url('/') }}" class="hover:text-gray-900 transition">
                Home
            </a>

            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
            </svg>

            <a href="{{ $communityUrl }}" class="hover:text-gray-900 transition">
                Community
            </a>

            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
            </svg>

            <span class="text-gray-900 font-medium truncate max-w-[220px]">
                {{ $categoryName }}
            </span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- MAIN CONTENT --}}
            <main class="lg:col-span-8 min-w-0">

                {{-- Article Header --}}
                <header class="mb-8">

                    <div class="flex flex-wrap items-center gap-3 mb-5">

                        <span class="rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700">
                            {{ $categoryName }}
                        </span>

                        @if($publishedAt)
                            <span class="text-sm text-gray-500">
                                {{ $publishedAt->diffForHumans() }}
                            </span>
                        @endif

                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.12] text-[#171717] mb-6">
                        {{ $translation?->title }}
                    </h1>

                    @if($translation?->excerpt)
                        <p class="text-lg sm:text-xl text-gray-600 leading-relaxed max-w-3xl">
                            {{ $translation->excerpt }}
                        </p>
                    @endif

                </header>

                {{-- Author Information --}}
                <div class="flex flex-wrap items-center justify-between gap-5 border-y border-gray-200 py-6 mb-8">

                    <div class="flex items-center gap-4 min-w-0">

                        <img
                            src="{{ $authorAvatar }}"
                            alt="{{ $authorName }}"
                            class="w-14 h-14 rounded-full object-cover border border-gray-200 bg-white shrink-0"
                        >

                        <div class="min-w-0">

                            <div class="flex items-center flex-wrap gap-2">

                                <span class="font-bold text-gray-900">
                                    {{ $authorName }}
                                </span>

                                {{-- Verified badge --}}
                                @if($author?->is_verified ?? false)
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-blue-500 text-white" title="Verified">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.75-3.75a1 1 0 011.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif

                            </div>
@php
    $companyInfo = $post->company_info;
@endphp
                            <div class="text-xs text-stone-500">
        <span>{{ $companyInfo['name'] }}</span>

        @if($companyInfo['city'] || $companyInfo['country'])
            <span>·</span>
            <span>
                {{ collect([$companyInfo['city'], $companyInfo['country']])->filter()->implode(', ') }}
            </span>
        @endif
    </div>

                        </div>

                    </div>

                    
                        <a
                            href="{{ url('/supplier/' . $companyInfo['slug']) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-white hover:border-gray-400 transition"
                        >
                            View Business Profile

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17 17 7M7 7h10v10"/>
                            </svg>
                        </a>
                    

                </div>

                {{-- Cover Image --}}
                @if($coverUrl)
                    <div class="mb-10">
                        <div class="rounded-[28px] overflow-hidden bg-gray-200">
                            <img
                                src="{{ $coverUrl }}"
                                alt="{{ $translation?->title }}"
                                class="w-full h-[280px] sm:h-[400px] lg:h-[500px] object-cover"
                            >
                        </div>
                    </div>
                @endif

                {{-- Article Content --}}
                @if($translation?->content)
                    <article class="community-article-content mb-12">
                        {!! $translation->content !!}
                    </article>
                @endif

                {{-- Image Gallery --}}
                @if($gallery->isNotEmpty())
                    <section class="mb-14">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            @foreach($gallery as $media)
                                @if($media->cdn_url)
                                    <a
                                        href="{{ $media->cdn_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group block rounded-2xl overflow-hidden bg-gray-200"
                                    >
                                        <img
                                            src="{{ $media->cdn_url }}"
                                            alt="{{ $translation?->title }}"
                                            loading="lazy"
                                            class="w-full h-64 object-cover group-hover:scale-[1.03] transition duration-500"
                                        >
                                    </a>
                                @endif
                            @endforeach

                        </div>

                    </section>
                @endif

                {{-- =================================================
    ATTACHED PRODUCTS
================================================== --}}

@if ($products->isNotEmpty())

    <section class="mt-16">

        <div class="mb-7 flex flex-wrap items-end justify-between gap-4">

            <div>
                <span class="text-xs font-bold uppercase tracking-[0.18em] text-stone-400">
                    Curated selection
                </span>

                <h2 class="mt-2 text-2xl font-bold text-slate-900 lg:text-3xl">
                    Featured Products
                </h2>

                <p class="mt-2 text-sm text-stone-500">
                    Products featured in this publication.
                </p>
            </div>

            <span class="rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-600">
                {{ $products->count() }} products
            </span>

        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

            @foreach ($products as $product)

                <div class="group overflow-hidden rounded-2xl border border-stone-200 bg-white transition duration-300 hover:shadow-lg">

                    <a
    href="{{ route('product.show', $product->slug) }}"
    target="_blank"
    rel="noopener noreferrer"
    class="relative block aspect-square overflow-hidden bg-white"
>
    <img
        src="{{ $product->catalog_image_url }}"
        class="h-full w-full object-contain p-2 transition duration-700 group-hover:scale-105"
        alt="{{ $product->name }}"
        loading="lazy"
    >

    <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1.5 text-xs font-medium text-slate-700 backdrop-blur">
        Acrovoy Catalog
    </span>
</a>

                    <div class="p-5">

                        <h3 class="font-semibold leading-snug text-slate-900">
                            {{ $product->name }}
                        </h3>

                        @if ($product->supplier)
                            <div class="mt-2 text-sm text-stone-500">
                                {{ $product->supplierName() }}
                            </div>
                        @endif

                        <div class="mt-5 flex items-center justify-end border-t border-stone-100 pt-4">

                            <a
                                href="{{ route('product.show', $product->slug) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-slate-700 transition hover:text-black"
                            >
                                View Product →
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </section>

@endif

                {{-- Post Engagement --}}
                <section class="border-y border-gray-200 py-5 mb-14 mt-12">

                    <div class="flex flex-wrap items-center justify-between gap-5">

                        <div class="flex flex-wrap items-center gap-6">

                            {{-- Like placeholder --}}
                            <button
                                type="button"
                                disabled
                                title="Likes will be available soon"
                                class="inline-flex items-center gap-2 text-gray-500 cursor-not-allowed opacity-70"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                </svg>

                                <span class="text-sm font-semibold">
                                    {{ number_format($likesCount) }}
                                </span>
                            </button>

                            <a href="#discussion" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                                </svg>

                                <span class="text-sm font-semibold">
                                    {{ number_format($commentsCount) }}
                                </span>
                            </a>

                            <div class="inline-flex items-center gap-2 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/>
                                    <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                </svg>

                                <span class="text-sm font-semibold">
                                    {{ number_format($viewsCount) }} views
                                </span>
                            </div>

                        </div>

                        <button
                            type="button"
                            onclick="shareCommunityPost()"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0L7 9m5-5 5 5M5 14v5a1 1 0 001 1h12a1 1 0 001-1v-5"/>
                            </svg>

                            Share
                        </button>

                    </div>

                </section>

                {{-- Discussion --}}
                <section id="discussion" class="scroll-mt-24 mb-16 mt-12">

                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">

                        <div>
                            <div class="text-xs font-bold uppercase tracking-[0.2em] text-[#A18A65] mb-2">
                                Community
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-black text-gray-900">
                                Discussion
                            </h2>
                        </div>

                        <span class="px-3.5 py-2 rounded-full bg-white border border-gray-200 text-sm font-semibold text-gray-600">
                            {{ number_format($commentsCount) }} comments
                        </span>

                    </div>

                    {{-- Comment form placeholder --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-8">

                        <div class="flex items-start gap-4">

                            @auth
                                <img
                                    src="{{ auth()->user()->avatar()?->cdn_url ?? asset('images/default-avatar.png') }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-11 h-11 rounded-full object-cover border border-gray-200 shrink-0"
                                >
                            @else
                                <div class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                                    </svg>
                                </div>
                            @endauth

                            <div class="flex-1 min-w-0">

                                <textarea
                                    disabled
                                    rows="3"
                                    placeholder="Share your thoughts about this publication..."
                                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500 placeholder:text-gray-400 cursor-not-allowed"
                                ></textarea>

                                <div class="flex flex-wrap items-center justify-between gap-3 mt-3">

                                    <span class="text-xs text-gray-400">
                                        Comment submission will be available soon.
                                    </span>

                                    <button
                                        type="button"
                                        disabled
                                        class="px-5 py-2.5 rounded-xl bg-gray-200 text-gray-400 text-sm font-bold cursor-not-allowed"
                                    >
                                        Post Comment
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Comments list --}}
                    @forelse($comments as $comment)

                        @continue($comment->status !== \App\Domain\Community\Models\PostComment::STATUS_PUBLISHED)

                        @php
                            $commentAuthor = $comment->author;
                            $commentAuthorName = $commentAuthor?->name ?? 'Community member';
                            $commentAvatar = $commentAuthor?->avatar()?->cdn_url
                                ?? asset('images/default-avatar.png');

                            $publishedReplies = $comment->replies
                                ->where('status', \App\Domain\Community\Models\PostComment::STATUS_PUBLISHED);
                        @endphp

                        <div class="border-b border-gray-200 pb-7 mb-7">

                            <div class="flex items-start gap-4">

                                <img
                                    src="{{ $commentAvatar }}"
                                    alt="{{ $commentAuthorName }}"
                                    class="w-11 h-11 rounded-full object-cover border border-gray-200 shrink-0"
                                    loading="lazy"
                                >

                                <div class="flex-1 min-w-0">

                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-2">

                                        <span class="font-bold text-gray-900">
                                            {{ $commentAuthorName }}
                                        </span>

                                        <span class="text-xs text-gray-400">
                                            {{ $comment->created_at?->diffForHumans() }}
                                        </span>

                                    </div>

                                    <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">
                                        {{ $comment->content }}
                                    </div>

                                    {{-- Replies --}}
                                    @if($publishedReplies->isNotEmpty())
                                        <div class="mt-5 pl-4 sm:pl-6 border-l-2 border-gray-200 space-y-5">

                                            @foreach($publishedReplies as $reply)
                                                @php
                                                    $replyAuthor = $reply->author;
                                                    $replyAuthorName = $replyAuthor?->name ?? 'Community member';
                                                    $replyAvatar = $replyAuthor?->avatar()?->cdn_url
                                                        ?? asset('images/default-avatar.png');
                                                @endphp

                                                <div class="flex items-start gap-3">

                                                    <img
                                                        src="{{ $replyAvatar }}"
                                                        alt="{{ $replyAuthorName }}"
                                                        class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0"
                                                        loading="lazy"
                                                    >

                                                    <div class="flex-1 min-w-0">

                                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1.5">

                                                            <span class="text-sm font-bold text-gray-900">
                                                                {{ $replyAuthorName }}
                                                            </span>

                                                            <span class="text-xs text-gray-400">
                                                                {{ $reply->created_at?->diffForHumans() }}
                                                            </span>

                                                        </div>

                                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">
                                                            {{ $reply->content }}
                                                        </p>

                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>
                                    @endif

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="bg-white border border-gray-200 rounded-2xl px-6 py-12 text-center">

                            <div class="w-14 h-14 rounded-full bg-[#F4F1EB] flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-[#9B896D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                                </svg>
                            </div>

                            <h3 class="font-bold text-gray-900 mb-2">
                                No comments yet
                            </h3>

                            <p class="text-sm text-gray-500">
                                Be the first to join the discussion when commenting becomes available.
                            </p>

                        </div>

                    @endforelse

                </section>

            </main>

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-6">

                {{-- Publication Details --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">

                    <h3 class="text-lg font-black text-gray-900 mb-6">
                        Publication Details
                    </h3>

                    <div class="space-y-5">

                        <div class="flex items-start justify-between gap-4">

                            <span class="text-sm text-gray-500">
                                Category
                            </span>

                            <span class="text-sm font-semibold text-gray-900 text-right">
                                {{ $categoryName }}
                            </span>

                        </div>

                        @if($publishedAt)
                            <div class="flex items-start justify-between gap-4">

                                <span class="text-sm text-gray-500">
                                    Published
                                </span>

                                <span class="text-sm font-semibold text-gray-900 text-right">
                                    {{ $publishedAt->format('M d, Y') }}
                                </span>

                            </div>
                        @endif

                        <div class="flex items-start justify-between gap-4">

                            <span class="text-sm text-gray-500">
                                Products
                            </span>

                            <span class="text-sm font-semibold text-gray-900 text-right">
                                {{ $products->count() }}
                            </span>

                        </div>

                        

                    </div>

                </div>

                {{-- Explore Community --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">

                   

                    <h3 class="text-lg font-black text-gray-900 mb-2">
                        Explore Community
                    </h3>

                    <p class="text-sm text-gray-500 leading-relaxed mb-5">
                        Discover ideas, projects and inspiration from professionals across the hospitality industry.
                    </p>

                    <a
                        href="{{ $communityUrl }}"
                        class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 hover:text-[#9B835D] transition"
                    >
                        Explore Publications

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>
                    </a>

                </div>

                {{-- CTA --}}

                <div class="relative overflow-hidden rounded-2xl bg-slate-900 p-7 text-white">

                    <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/5"></div>

                    <div class="relative">

                        <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                            Acrovoy Community
                        </span>

                        <h3 class="mt-4 text-2xl font-bold leading-tight">
                            Share your ideas with the industry.
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-300">
                            Publish your projects, showcase products and connect with hospitality professionals.
                        </p>

                        <a
                            href="{{ route('community.posts.create') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-stone-100"
                        >
                            Create a Post
                            <span class="ml-2" aria-hidden="true">→</span>
                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </div>
</div>

{{-- Share --}}
<script>
    async function shareCommunityPost() {
        const shareData = {
            title: @json($translation?->title),
            text: @json($translation?->excerpt),
            url: window.location.href
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                return;
            }

            await navigator.clipboard.writeText(window.location.href);

            alert('Link copied to clipboard.');
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Unable to share publication:', error);
            }
        }
    }
</script>

{{-- Article typography --}}
<style>
    .community-article-content {
        color: #404040;
        font-size: 16px;
        line-height: 1.85;
        overflow-wrap: break-word;
    }

    .community-article-content > * + * {
        margin-top: 1.35rem;
    }

    .community-article-content h1,
    .community-article-content h2,
    .community-article-content h3,
    .community-article-content h4 {
        color: #171717;
        font-weight: 800;
        line-height: 1.3;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .community-article-content h1 {
        font-size: 2rem;
    }

    .community-article-content h2 {
        font-size: 1.65rem;
    }

    .community-article-content h3 {
        font-size: 1.35rem;
    }

    .community-article-content p {
        margin-bottom: 1.25rem;
    }

    .community-article-content a {
        color: #927B56;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .community-article-content ul,
    .community-article-content ol {
        padding-left: 1.5rem;
        margin: 1.25rem 0;
    }

    .community-article-content ul {
        list-style: disc;
    }

    .community-article-content ol {
        list-style: decimal;
    }

    .community-article-content li {
        padding-left: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .community-article-content blockquote {
        border-left: 3px solid #C5B28F;
        padding: 0.75rem 1.25rem;
        color: #57534E;
        background: #F5F3EF;
        border-radius: 0 0.75rem 0.75rem 0;
        margin: 1.75rem 0;
    }

    .community-article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
        margin: 1.5rem 0;
    }

    .community-article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        font-size: 0.9rem;
    }

    .community-article-content th,
    .community-article-content td {
        border: 1px solid #E5E7EB;
        padding: 0.75rem;
        text-align: left;
    }

    .community-article-content th {
        background: #F5F3EF;
        color: #292524;
        font-weight: 700;
    }

    .community-article-content iframe {
        max-width: 100%;
        border-radius: 1rem;
    }

    @media (max-width: 640px) {
        .community-article-content {
            font-size: 15px;
        }

        .community-article-content h1 {
            font-size: 1.7rem;
        }

        .community-article-content h2 {
            font-size: 1.45rem;
        }
    }
</style>

@endsection