@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#F8F7F4]">

    <!-- {{-- ==========================================================
        BREADCRUMB
    =========================================================== --}}
    <div class="border-b border-stone-200 bg-white">
        <div class="container mx-auto px-6 lg:px-12 py-4">
            <nav class="flex items-center gap-2 text-sm text-stone-500">
                <a href="/" class="hover:text-slate-900">Home</a>
                <span>/</span>
                <a href="#community" class="hover:text-slate-900">Community</a>
                <span>/</span>
                <span class="text-slate-900 font-medium truncate">
                    {{ $post['category'] }}
                </span>
            </nav>
        </div>
    </div> -->

    <div class="container mx-auto px-6 lg:px-12 py-10 lg:py-14">

        <div class="grid lg:grid-cols-12 gap-10">

            {{-- ==================================================
                MAIN CONTENT
            =================================================== --}}
            <main class="lg:col-span-8 min-w-0">

                {{-- POST HEADER --}}
                <div class="mb-8">

                    <div class="flex flex-wrap items-center gap-3 mb-5">

                        <span class="rounded-full bg-white border border-stone-200 px-4 py-2 text-xs font-semibold text-slate-700">
                            {{ $post['category'] }}
                        </span>

                        <span class="text-sm text-stone-500">
                            {{ $post['published_at']->diffForHumans() }}
                        </span>

                    </div>

                    <h1 class="text-3xl lg:text-5xl font-black leading-tight tracking-tight text-slate-900">
                        {{ $post['title'] }}
                    </h1>

                    <p class="mt-6 text-lg lg:text-xl leading-8 text-stone-600 max-w-3xl">
                        {{ $post['excerpt'] }}
                    </p>

                </div>

                {{-- AUTHOR INFORMATION --}}
                <div class="flex flex-wrap items-center justify-between gap-5 mb-8">

                    <div class="flex items-center gap-4">

                        <img
                            src="{{ asset('images/777.png') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=Studio+Design&background=e7e5e4&color=44403c'"
                            class="w-14 h-14 rounded-full object-cover border border-stone-200"
                            alt="{{ $post['author']['name'] }}">

                        <div>

                            <div class="flex items-center gap-2">

                                <span class="font-semibold text-slate-900">
                                    Kevin Berbow
                                </span>

                                @if($post['author']['verified'])
                                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 1.5 12.5 3l3-.2.9 2.9 2.4 1.8-1 2.9 1 2.9-2.4 1.8-.9 2.9-3-.2L10 19l-2.5-1.5-3 .2-.9-2.9-2.4-1.8 1-2.9-1-2.9 2.4-1.8.9-2.9 3 .2L10 1.5Zm3.2 6.2a.8.8 0 0 1 0 1.1l-3.8 3.8a.8.8 0 0 1-1.1 0l-1.8-1.8a.8.8 0 1 1 1.1-1.1l1.2 1.2 3.2-3.2a.8.8 0 0 1 1.2 0Z" clip-rule="evenodd"/>
                                    </svg>
                                @endif

                            </div>

                            <div class="text-sm text-stone-500 mt-1">
                                {{ $post['author']['type'] }}
                                <span class="mx-1">·</span>
                                {{ $post['author']['location'] }}
                            </div>

                        </div>

                    </div>

                    <a href="{{ $post['author']['website'] }}"
                       class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-stone-50 transition">
                        View Profile
                    </a>

                </div>

                {{-- COVER IMAGE --}}
                <div class="overflow-hidden rounded-[28px] bg-stone-200 shadow-sm">

                    <img
                        src="{{ $post['cover'] }}"
                        class="w-full h-[300px] sm:h-[400px] lg:h-[500px] object-cover"
                        alt="{{ $post['title'] }}">

                </div>

                {{-- POST CONTENT --}}
                <article class="mt-10">

                    <div class="max-w-3xl">

                        @foreach($post['content'] as $paragraph)

                            <p class="mb-7 text-base lg:text-lg leading-8 text-stone-700">
                                {{ $paragraph }}
                            </p>

                        @endforeach

                    </div>

                    {{-- GALLERY --}}
                    @if(!empty($post['images']))

                        <div class="grid sm:grid-cols-2 gap-5 mt-10">

                            @foreach($post['images'] as $image)

                                <div class="overflow-hidden rounded-2xl bg-stone-200">
                                    <img
                                        src="{{ $image }}"
                                        class="w-full h-64 object-cover hover:scale-105 transition duration-700"
                                        alt="Project gallery">
                                </div>

                            @endforeach

                        </div>

                    @endif

                </article>

                {{-- ==================================================
                    ATTACHED PRODUCTS
                =================================================== --}}
                <section class="mt-16">

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-7">

                        <div>

                            <span class="text-xs font-bold uppercase tracking-[0.18em] text-stone-400">
                                Curated selection
                            </span>

                            <h2 class="mt-2 text-2xl lg:text-3xl font-bold text-slate-900">
                                Featured Products
                            </h2>

                            <p class="mt-2 text-sm text-stone-500">
                                Products featured in this publication.
                            </p>

                        </div>

                        <span class="rounded-full bg-white border border-stone-200 px-4 py-2 text-sm font-medium text-stone-600">
                            {{ count($post['products']) }} products
                        </span>

                    </div>

                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">

                        @foreach($post['products'] as $product)

                            <div class="group overflow-hidden rounded-2xl border border-stone-200 bg-white hover:shadow-lg transition duration-300">

                                <a href="{{ $product['url'] }}" class="block relative overflow-hidden bg-stone-100">

                                    <img
                                        src="{{ $product['image'] }}"
                                        class="w-full h-52 object-cover group-hover:scale-105 transition duration-700"
                                        alt="{{ $product['name'] }}">

                                    <span class="absolute top-3 left-3 rounded-full bg-white/95 backdrop-blur px-3 py-1.5 text-xs font-medium text-slate-700">
                                        Acrovoy Catalog
                                    </span>

                                </a>

                                <div class="p-5">

                                    <div class="text-xs text-stone-500">
                                        {{ $product['category'] }}
                                    </div>

                                    <h3 class="mt-2 font-semibold text-slate-900 leading-snug">
                                        {{ $product['name'] }}
                                    </h3>

                                    <div class="mt-2 text-sm text-stone-500">
                                        {{ $product['supplier'] }}
                                    </div>

                                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-stone-100">

                                        <span class="text-sm font-semibold text-slate-900">
                                            {{ $product['price'] }}
                                        </span>

                                        <a href="{{ $product['url'] }}"
                                           class="text-sm font-semibold text-slate-700 hover:text-black">
                                            View Product →
                                        </a>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

                {{-- ==================================================
                    POST ENGAGEMENT
                =================================================== --}}
                <section class="mt-12 border-y border-stone-200 py-6">

                    <div class="flex flex-wrap items-center justify-between gap-5">

                        <div class="flex items-center gap-6">

                            {{-- Like --}}
                            <button
                                type="button"
                                onclick="togglePostLike(this)"
                                class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-rose-600 transition">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20.8 8.8c0 5.4-10.8 11-10.8 11S-.8 14.2 3.2 8.8C5.4 5.8 9.1 6.1 10 9c.9-2.9 4.6-3.2 6.8-.2 1 1.3 1.4 2.8 1.4 4Z" transform="translate(1 -1)"/>
                                </svg>

                                <span>{{ $post['likes'] }} Likes</span>

                            </button>

                            {{-- Comments --}}
                            <a href="#comments"
                               class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-slate-900">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 10h8m-8 4h5m-9 7 3-3h10a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v12l2-1z"/>
                                </svg>

                                {{ $post['comments_count'] }} Comments

                            </a>

                            {{-- Views --}}
                            <span class="inline-flex items-center gap-2 text-sm text-stone-500">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                {{ $post['views'] }} Views

                            </span>

                        </div>

                        {{-- Share --}}
                        <button
                            type="button"
                            onclick="sharePost()"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-black">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 16V3m0 0L7 8m5-5 5 5M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6"/>
                            </svg>

                            Share Post

                        </button>

                    </div>

                </section>

                {{-- ==================================================
                    COMMENTS
                =================================================== --}}
                <section id="comments" class="mt-14 scroll-mt-24">

                    <div class="flex items-center justify-between mb-8">

                        <div>
                            <h2 class="text-2xl font-bold text-slate-900">
                                Discussion
                            </h2>

                            <p class="mt-2 text-sm text-stone-500">
                                Join the conversation.
                            </p>
                        </div>

                        <span class="text-sm text-stone-500">
                            {{ $post['comments_count'] }} comments
                        </span>

                    </div>

                    {{-- Comment form --}}
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">

                        <div class="flex gap-4">

                            <div class="flex-shrink-0">
                                <div class="w-11 h-11 rounded-full bg-stone-100 flex items-center justify-center text-sm font-bold text-stone-500">
                                    Y
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">

                                <textarea
                                    rows="3"
                                    placeholder="Share your thoughts about this publication..."
                                    class="w-full resize-y rounded-xl border border-stone-200 bg-stone-50 p-4 text-sm text-slate-900 placeholder:text-stone-400 focus:bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition"></textarea>

                                <div class="flex justify-end mt-3">

                                    <button
                                        type="button"
                                        disabled
                                        title="Comment submission will be available soon"
                                        class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white opacity-50 cursor-not-allowed">
                                        Post Comment
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Existing comments --}}
                    <div class="mt-8 space-y-7">

                        @foreach($comments as $comment)

                            <div class="flex gap-4">

                                <img
                                    src="{{ $comment['avatar'] }}"
                                    class="w-11 h-11 rounded-full object-cover flex-shrink-0"
                                    alt="{{ $comment['author'] }}">

                                <div class="flex-1 min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="font-semibold text-sm text-slate-900">
                                            {{ $comment['author'] }}
                                        </span>

                                        <span class="text-xs text-stone-400">
                                            {{ $comment['date']->diffForHumans() }}
                                        </span>

                                    </div>

                                    <p class="mt-2 text-sm leading-7 text-stone-600">
                                        {{ $comment['text'] }}
                                    </p>

                                    <button
                                        type="button"
                                        class="mt-3 text-xs font-semibold text-stone-500 hover:text-slate-900">
                                        ♡ {{ $comment['likes'] }} Likes
                                    </button>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    {{-- Placeholder for pagination --}}
                    <div class="mt-8 text-center">
                        <button
                            type="button"
                            disabled
                            class="text-sm font-semibold text-stone-400 cursor-not-allowed">
                            Load more comments
                        </button>
                    </div>

                </section>

            </main>

            {{-- ==================================================
                SIDEBAR
            =================================================== --}}
            <aside class="lg:col-span-4 space-y-6">

                <!-- {{-- AUTHOR CARD --}}
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">

                    <span class="text-xs font-bold uppercase tracking-widest text-stone-400">
                        About the author
                    </span>

                    <div class="flex items-center gap-4 mt-5">

                        <img
                            src="{{ asset('images/777.png') }}"
                            class="w-16 h-16 rounded-full object-cover border border-stone-200"
                            alt="{{ $post['author']['name'] }}">

                        <div class="min-w-0">

                            <div class="font-bold text-slate-900">
                                Kevin Berbow
                            </div>

                            <div class="mt-1 text-xs text-stone-500">
                                {{ $post['author']['type'] }}
                            </div>

                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">

                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="text-xl font-bold text-slate-900">
                                {{ number_format($post['author']['followers']) }}
                            </div>
                            <div class="mt-1 text-xs text-stone-500">
                                Followers
                            </div>
                        </div>

                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="text-xl font-bold text-slate-900">
                                {{ $post['views'] }}
                            </div>
                            <div class="mt-1 text-xs text-stone-500">
                                Post views
                            </div>
                        </div>

                    </div>

                    <a href="{{ $post['author']['website'] }}"
                       class="flex justify-center items-center w-full mt-5 rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white hover:bg-black transition">
                        View Company Profile
                    </a>

                </div> -->

                {{-- PUBLICATION DETAILS --}}
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">

                    <h3 class="font-bold text-slate-900">
                        Publication Details
                    </h3>

                    <div class="mt-5 space-y-4">

                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-500">Category</span>
                            <span class="font-medium text-slate-800 text-right">
                                {{ $post['category'] }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-500">Published</span>
                            <span class="font-medium text-slate-800">
                                {{ $post['published_at']->format('M d, Y') }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-500">Products</span>
                            <span class="font-medium text-slate-800">
                                {{ count($post['products']) }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-500">Location</span>
                            <span class="font-medium text-slate-800">
                                {{ $post['author']['location'] }}
                            </span>
                        </div>

                    </div>

                </div>

                {{-- MORE FROM COMMUNITY --}}
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">

                    <div class="flex items-center justify-between">

                        <h3 class="font-bold text-slate-900">
                            Explore Community
                        </h3>

                        <span class="text-xs text-stone-400">
                            Acrovoy
                        </span>

                    </div>

                    <p class="mt-3 text-sm leading-6 text-stone-500">
                        Discover ideas, projects, product collections
                        and insights shared by hospitality professionals.
                    </p>

                    <a href="#community"
                       class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-slate-800 hover:text-black">
                        Explore publications →
                    </a>

                </div>

                {{-- CTA --}}
                <div class="relative overflow-hidden rounded-2xl bg-slate-900 p-7 text-white">

                    <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full bg-white/5"></div>

                    <div class="relative">

                        <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                            Acrovoy Community
                        </span>

                        <h3 class="mt-4 text-2xl font-bold leading-tight">
                            Share your ideas with the industry.
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-300">
                            Publish your projects, showcase products
                            and connect with hospitality professionals.
                        </p>

                        <a href="#create-post"
                           class="inline-flex items-center justify-center mt-6 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-stone-100 transition">
                            Create a Post →
                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </div>

</div>

{{-- ==========================================================
    PAGE INTERACTIONS
========================================================== --}}
<script>
    function togglePostLike(button) {
        const svg = button.querySelector('svg');
        const label = button.querySelector('span');

        const liked = button.dataset.liked === 'true';

        button.dataset.liked = liked ? 'false' : 'true';

        svg.classList.toggle('fill-rose-500', !liked);
        svg.classList.toggle('text-rose-600', !liked);
        svg.classList.toggle('fill-none', liked);

        const count = parseInt(label.textContent) || 0;

        label.textContent = (liked ? count - 1 : count + 1) + ' Likes';
    }

    async function sharePost() {
        const data = {
            title: @json($post['title']),
            text: @json($post['excerpt']),
            url: window.location.href
        };

        try {
            if (navigator.share) {
                await navigator.share(data);
            } else if (navigator.clipboard) {
                await navigator.clipboard.writeText(data.url);
                alert('Link copied to clipboard.');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Unable to share publication:', error);
            }
        }
    }
</script>

@endsection