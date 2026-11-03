@extends('layouts.app')

@section('content')

@php
    $locale = app()->getLocale();

    $translation = $translation ?? $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', 'en');

    abort_unless($translation, 404);

    $categoryTranslation = $post->category?->getTranslation($locale);

    $categoryName = $categoryTranslation?->name
        ?? $post->category?->name
        ?? 'Uncategorized';

    $authorName = $post->author?->name ?? 'Acrovoy Member';

    $statusLabels = [
        'draft' => 'Draft',
        'pending' => 'On moderation',
        'published' => 'Published',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
    ];

    $statusColors = [
        'draft' => 'bg-stone-100 text-stone-700',
        'pending' => 'bg-amber-100 text-amber-800',
        'published' => 'bg-emerald-100 text-emerald-800',
        'rejected' => 'bg-red-100 text-red-800',
        'archived' => 'bg-slate-100 text-slate-700',
    ];

    $statusDescriptions = [
        'draft' => 'This publication is saved as a draft. Only you can see it.',
        'pending' => 'This publication has been submitted for moderation. It is not yet visible in the public Community.',
        'rejected' => 'This publication was rejected during moderation. Review the feedback, make corrections and submit it again.',
        'archived' => 'This publication is archived and is not visible in the public Community.',
        'published' => 'This is a preview of your published publication.',
    ];

    $postMedia = collect($postMedia ?? $post->media ?? []);

    $cover = $postMedia->firstWhere('is_main', true)
        ?? $postMedia->first();

    $gallery = $cover
        ? $postMedia->reject(fn ($item) => $item->id === $cover->id)
        : $postMedia;

    $products = $post->products ?? collect();

    $publishedAt = $post->published_at;

    $initials = collect(explode(' ', trim($authorName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

@endphp

<div class="min-h-screen bg-[#F8F7F4]">

    {{-- =========================================================
        PREVIEW NOTICE
    ========================================================== --}}

    <div class="border-b border-stone-200 bg-white">

        <div class="container mx-auto flex flex-col gap-4 px-6 py-4 lg:px-12 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-start gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-600">

                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>

                </div>

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <span class="text-sm font-semibold text-slate-900">
                            Publication preview
                        </span>

                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide {{ $statusColors[$post->status] ?? 'bg-stone-100 text-stone-700' }}">
                            {{ $statusLabels[$post->status] ?? ucfirst($post->status) }}
                        </span>

                    </div>

                    <p class="mt-1 text-xs leading-5 text-stone-500">
                        {{ $statusDescriptions[$post->status] ?? 'This publication is not available to the public.' }}
                    </p>

                </div>

            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">

                <a
                    href="{{ route('community.posts.edit', $post->id) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"/>
                    </svg>
                    Edit
                </a>

                @if (in_array($post->status, ['draft', 'rejected'], true))

                    <form
                        action="{{ route('community.posts.submit', $post->id) }}"
                        method="POST"
                        onsubmit="return confirm('Submit this publication for moderation?')"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-black"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                            </svg>
                            Submit for review
                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
        BREADCRUMB
    ========================================================== --}}

    <div class="container mx-auto px-6 py-4 lg:px-12">

        <nav class="flex items-center gap-2 text-sm text-stone-500">

            <a href="/" class="transition hover:text-slate-900">
                Home
            </a>

            <span>/</span>

            <a href="{{ route('community.manage.index') }}" class="transition hover:text-slate-900">
    My Publications
</a>

            <span>/</span>

            <span class="truncate font-medium text-slate-900">
                {{ $categoryName }}
            </span>

        </nav>

    </div>


    {{-- =========================================================
        PAGE CONTENT
    ========================================================== --}}

    <div class="container mx-auto px-6 py-8 lg:px-12 lg:py-12">

        <div class="grid gap-10 lg:grid-cols-12">


            {{-- =================================================
                MAIN CONTENT
            ================================================== --}}

            <main class="min-w-0 lg:col-span-8">


                {{-- POST HEADER --}}

                <div class="mb-8">

                    <div class="mb-5 flex flex-wrap items-center gap-3">

                        <span class="rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700">
                            {{ $categoryName }}
                        </span>

                        @if ($publishedAt)
                            <span class="text-sm text-stone-500">
                                {{ $publishedAt->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-sm text-stone-500">
                                Not published yet
                            </span>
                        @endif

                    </div>

                    <h1 class="text-3xl font-black leading-tight tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                        {{ $translation->title }}
                    </h1>

                    @if ($translation->excerpt)

                        <p class="mt-6 max-w-3xl text-lg leading-8 text-stone-600 lg:text-xl">
                            {{ $translation->excerpt }}
                        </p>

                    @endif

                </div>


                {{-- AUTHOR INFORMATION --}}

<div class="mb-8 flex flex-wrap items-center justify-between gap-5">

    <div class="flex min-w-0 items-center gap-4">

        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full border border-stone-200 bg-stone-100">

            <img
                src="{{ $post->author?->avatar()?->cdn_url ?? asset('images/default-avatar.png') }}"
                alt="{{ $authorName }}"
                class="h-full w-full object-cover"
                onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
            >

        </div>

        <div class="min-w-0">

            <div class="font-semibold text-slate-900">
                {{ $authorName }}
            </div>

            <div class="mt-1 text-sm text-stone-500">

    @php
        $address = $company?->primaryAddress;

        $city = $address?->city;

        $country = $address?->country?->name
            ?? $company?->country?->name;

        $location = collect([$city, $country])
            ->filter()
            ->implode(', ');
    @endphp

    {{ $company?->name ?? 'Acrovoy Member' }}

    @if ($location)
        · {{ $location }}
    @endif

</div>

        </div>

        


    </div>

<span class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-3 text-xs font-medium text-stone-500">

                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>

                        Created {{ $post->created_at?->format('d M Y') }}

                    </span>


                {{-- COVER IMAGE --}}

                @if ($cover)

                    <div class="overflow-hidden rounded-[28px] bg-stone-200 shadow-sm">

                        <img
                            src="{{ $cover->cdn_url }}"
                            class="h-[300px] w-full object-cover sm:h-[400px] lg:h-[500px]"
                            alt="{{ $translation->title }}"
                        >

                    </div>

                @else

                    <div class="flex h-[300px] items-center justify-center rounded-[28px] border border-dashed border-stone-300 bg-stone-100 sm:h-[400px] lg:h-[500px]">

                        <div class="text-center">

                            <svg class="mx-auto h-12 w-12 text-stone-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                            </svg>

                            <p class="mt-3 text-sm text-stone-400">
                                No cover image uploaded
                            </p>

                        </div>

                    </div>

                @endif


                {{-- POST CONTENT --}}

                <article class="mt-10">

                    <div class="post-content max-w-3xl text-base leading-8 text-stone-700 lg:text-lg">

                        {!! $translation->content !!}

                    </div>


                    {{-- GALLERY --}}

                    @if ($gallery->isNotEmpty())

                        <div class="mt-10 grid gap-5 sm:grid-cols-2">

                            @foreach ($gallery as $image)

                                <div class="overflow-hidden rounded-2xl bg-stone-200">

                                    <img
                                        src="{{ $image->cdn_url }}"
                                        class="h-64 w-full object-cover transition duration-700 hover:scale-105"
                                        alt="{{ $translation->title }} - image {{ $loop->iteration }}"
                                        loading="lazy"
                                    >

                                </div>

                            @endforeach

                        </div>

                    @endif

                </article>


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
    class="relative block aspect-square overflow-hidden bg-stone-50"
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


                {{-- =================================================
                    PREVIEW FOOTER
                ================================================== --}}

                <section class="mt-12 border-y border-stone-200 py-6">

                    <div class="flex flex-wrap items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-semibold text-slate-800">
                                This is a preview
                            </p>

                            <p class="mt-1 text-xs leading-5 text-stone-500">
                                Public engagement features will be available after publication approval.
                            </p>

                        </div>

                        <a
                            href="{{ route('community.posts.edit', $post->id) }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 transition hover:text-black"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"/>
                            </svg>
                            Edit publication
                        </a>

                    </div>

                </section>


            </main>


            {{-- =================================================
                SIDEBAR
            ================================================== --}}

            <aside class="space-y-6 lg:col-span-4">


                {{-- PUBLICATION DETAILS --}}

                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">

                    <h3 class="font-bold text-slate-900">
                        Publication Details
                    </h3>

                    <div class="mt-5 space-y-4">

                        <div class="flex items-start justify-between gap-4 text-sm">

                            <span class="text-stone-500">
                                Status
                            </span>

                            <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $statusColors[$post->status] ?? 'bg-stone-100 text-stone-700' }}">
                                {{ $statusLabels[$post->status] ?? ucfirst($post->status) }}
                            </span>

                        </div>


                        <div class="flex justify-between gap-4 text-sm">

                            <span class="text-stone-500">
                                Category
                            </span>

                            <span class="text-right font-medium text-slate-800">
                                {{ $categoryName }}
                            </span>

                        </div>


                        <div class="flex justify-between gap-4 text-sm">

                            <span class="text-stone-500">
                                Created
                            </span>

                            <span class="font-medium text-slate-800">
                                {{ $post->created_at?->format('M d, Y') }}
                            </span>

                        </div>


                        @if ($publishedAt)

                            <div class="flex justify-between gap-4 text-sm">

                                <span class="text-stone-500">
                                    Published
                                </span>

                                <span class="font-medium text-slate-800">
                                    {{ $publishedAt->format('M d, Y') }}
                                </span>

                            </div>

                        @endif


                        <div class="flex justify-between gap-4 text-sm">

                            <span class="text-stone-500">
                                Products
                            </span>

                            <span class="font-medium text-slate-800">
                                {{ $products->count() }}
                            </span>

                        </div>


                        <div class="flex justify-between gap-4 text-sm">

                            <span class="text-stone-500">
                                Images
                            </span>

                            <span class="font-medium text-slate-800">
                                {{ $postMedia->count() }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- EXPLORE COMMUNITY --}}

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
                        Discover ideas, projects, product collections and insights shared by hospitality professionals.
                    </p>

                    <a
                        href="{{ route('community.index') }}"
                        class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-slate-800 transition hover:text-black"
                    >
                        Explore publications
                        <span aria-hidden="true">→</span>
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


{{-- =========================================================
    PAGE STYLES
========================================================== --}}

<style>
    .post-content {
        overflow-wrap: break-word;
    }

    .post-content > * + * {
        margin-top: 1.5rem;
    }

    .post-content p {
        line-height: 1.9;
    }

    .post-content h2 {
        margin-top: 2.5rem;
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1.3;
        color: #0f172a;
    }

    .post-content h3 {
        margin-top: 2rem;
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.4;
        color: #0f172a;
    }

    .post-content ul,
    .post-content ol {
        padding-left: 1.5rem;
    }

    .post-content ul {
        list-style-type: disc;
    }

    .post-content ol {
        list-style-type: decimal;
    }

    .post-content li {
        padding-left: 0.25rem;
        margin-top: 0.5rem;
    }

    .post-content blockquote {
        border-left: 3px solid #a8a29e;
        padding-left: 1.25rem;
        color: #57534e;
        font-style: italic;
    }

    .post-content a {
        color: #334155;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
    }

    .post-content strong,
    .post-content b {
        font-weight: 700;
    }
</style>

@endsection