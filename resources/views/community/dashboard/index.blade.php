
@extends('dashboard.layout')

@section('dashboard-content')

<div class="flex flex-col gap-5">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-stone-900">
                My Publications
            </h1>
            <p class="mt-1 text-sm text-stone-500">
                Manage your community publications, track their performance and share your work.
            </p>
        </div>

        <a
            href="{{ route('community.posts.create') }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-stone-800 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
            </svg>
            Create Publication
        </a>
    </div>


    {{-- =========================================================
        PUBLICATION OVERVIEW
    ========================================================== --}}

    <div class="grid grid-cols-2 gap-2 xl:grid-cols-5">

        {{-- All --}}
        <a
            href="{{ route('community.manage.index') }}"
            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2 transition hover:border-stone-300
                {{ empty($status) ? 'border-stone-400 bg-stone-50' : 'border-stone-200 bg-white' }}"
        >
            <div class="min-w-0">
                <p class="text-xs text-stone-500">All publications</p>
                <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                    {{ number_format($statusCounts['all'] ?? 0) }}
                </p>
            </div>
            <span class="h-2 w-2 shrink-0 rounded-full bg-stone-400"></span>
        </a>

        {{-- Published --}}
        <a
            href="{{ route('community.manage.index', ['status' => 'published']) }}"
            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2 transition hover:border-stone-300
                {{ ($status ?? '') === 'published' ? 'border-emerald-300 bg-emerald-50/50' : 'border-stone-200 bg-white' }}"
        >
            <div class="min-w-0">
                <p class="text-xs text-stone-500">Published</p>
                <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                    {{ number_format($statusCounts['published'] ?? 0) }}
                </p>
            </div>
            <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
        </a>

        {{-- Pending --}}
        <a
            href="{{ route('community.manage.index', ['status' => 'pending']) }}"
            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2 transition hover:border-stone-300
                {{ ($status ?? '') === 'pending' ? 'border-amber-300 bg-amber-50/50' : 'border-stone-200 bg-white' }}"
        >
            <div class="min-w-0">
                <p class="text-xs text-stone-500">Under review</p>
                <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                    {{ number_format($statusCounts['pending'] ?? 0) }}
                </p>
            </div>
            <span class="h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
        </a>

        {{-- Drafts --}}
        <a
            href="{{ route('community.manage.index', ['status' => 'draft']) }}"
            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2 transition hover:border-stone-300
                {{ ($status ?? '') === 'draft' ? 'border-stone-400 bg-stone-50' : 'border-stone-200 bg-white' }}"
        >
            <div class="min-w-0">
                <p class="text-xs text-stone-500">Drafts</p>
                <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                    {{ number_format($statusCounts['draft'] ?? 0) }}
                </p>
            </div>
            <span class="h-2 w-2 shrink-0 rounded-full bg-stone-400"></span>
        </a>

        {{-- Rejected --}}
        <a
            href="{{ route('community.manage.index', ['status' => 'rejected']) }}"
            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2 transition hover:border-stone-300
                {{ ($status ?? '') === 'rejected' ? 'border-red-300 bg-red-50/50' : 'border-stone-200 bg-white' }}"
        >
            <div class="min-w-0">
                <p class="text-xs text-stone-500">Rejected</p>
                <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                    {{ number_format($statusCounts['rejected'] ?? 0) }}
                </p>
            </div>
            <span class="h-2 w-2 shrink-0 rounded-full bg-red-500"></span>
        </a>

    </div>


    {{-- =========================================================
        FILTERS
    ========================================================== --}}

    <form
        action="{{ route('community.manage.index') }}"
        method="GET"
        class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"
    >
        <div class="flex items-center justify-between gap-3 bg-stone-50/70 px-4 py-3">

            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M7 12h10m-7 7h4"/>
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-stone-800">
                        Filter publications
                    </h2>
                    <p class="mt-0.5 truncate text-xs text-stone-500">
                        Search and organize your community content
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a
                    href="{{ route('community.manage.index') }}"
                    class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium text-stone-600 transition hover:bg-white hover:text-stone-900"
                >
                    Reset filters
                </a>

                <button
                    type="button"
                    id="toggle-community-filters"
                    aria-expanded="{{ request()->hasAny(['search', 'status', 'category', 'sort']) ? 'true' : 'false' }}"
                    aria-controls="community-filters-panel"
                    aria-label="Toggle publication filters"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 transition hover:border-stone-300 hover:bg-stone-100 hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-300"
                >
                    <svg
                        id="community-filters-chevron"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform duration-200 {{ request()->hasAny(['search', 'status', 'category', 'sort']) ? 'rotate-180' : '' }}"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>
        </div>

        <div
            id="community-filters-panel"
            @if (!request()->hasAny(['search', 'status', 'category', 'sort'])) hidden @endif
        >
            <div class="grid grid-cols-1 gap-4 border-t border-stone-200 p-5 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Search --}}
                <div class="sm:col-span-2">
                    <label for="filter-search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                        Publication title
                    </label>

                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" d="m16 16 4 4"/>
                        </svg>

                        <input
                            id="filter-search"
                            type="search"
                            name="search"
                            value="{{ $search ?? request('search') }}"
                            placeholder="Search by publication title..."
                            maxlength="150"
                            class="w-full rounded-lg border border-stone-300 bg-white py-2.5 pl-9 pr-3 text-sm text-stone-800 placeholder:text-stone-400 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                        >
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="filter-status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                        Status
                    </label>

                    <select
                        id="filter-status"
                        name="status"
                        class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                    >
                        <option value="">All statuses</option>
                        <option value="draft" @selected(($status ?? '') === 'draft')>Draft</option>
                        <option value="pending" @selected(($status ?? '') === 'pending')>Under review</option>
                        <option value="published" @selected(($status ?? '') === 'published')>Published</option>
                        <option value="rejected" @selected(($status ?? '') === 'rejected')>Rejected</option>
                        <option value="archived" @selected(($status ?? '') === 'archived')>Archived</option>
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label for="filter-category" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                        Category
                    </label>

                    <select
                        id="filter-category"
                        name="category"
                        class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                    >
                        <option value="">All categories</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected((string) ($categoryFilter ?? '') === (string) $category->id)
                            >
                                {{ $category->getTranslation(app()->getLocale())?->name ?? $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div>
                    <label for="filter-sort" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                        Sort by
                    </label>

                    <select
                        id="filter-sort"
                        name="sort"
                        class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                    >
                        <option value="newest" @selected(($sort ?? 'newest') === 'newest')>Newest first</option>
                        <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest first</option>
                        <option value="title_asc" @selected(($sort ?? '') === 'title_asc')>Title: A–Z</option>
                        <option value="title_desc" @selected(($sort ?? '') === 'title_desc')>Title: Z–A</option>
                    </select>
                </div>

            </div>

            <div class="flex justify-end border-t border-stone-200 bg-stone-50/50 px-5 py-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-stone-800 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m16 16 4 4"/>
                    </svg>
                    Apply filters
                </button>
            </div>
        </div>
    </form>


    {{-- =========================================================
        PUBLICATIONS TABLE
    ========================================================== --}}

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">

        {{-- Table header --}}
        <div class="flex flex-col gap-3 border-b border-stone-200 bg-stone-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-stone-800">
                        Your publications
                    </h2>
                    <p class="mt-0.5 text-xs text-stone-500">
                        {{ number_format($posts->total()) }} {{ \Illuminate\Support\Str::plural('publication', $posts->total()) }} found
                    </p>
                </div>
            </div>

            <span class="text-xs text-stone-500">
                Showing {{ $posts->firstItem() ?? 0 }}–{{ $posts->lastItem() ?? 0 }} of {{ number_format($posts->total()) }}
            </span>
        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] border-collapse text-sm">

                <thead class="border-b border-stone-200 bg-white">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Publication
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Category
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-stone-500">
    Products
</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Engagement
                        </th>
                        
                        
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100">

                    @forelse ($posts as $post)

                        @php
                            $postTranslation = $post->translations->firstWhere('locale', app()->getLocale())
                                ?? $post->translations->firstWhere('locale', 'en')
                                ?? $post->translations->first();

                            $postTitle = $postTranslation?->title ?: 'Untitled publication';

                            $categoryTranslation = $post->category?->getTranslation(app()->getLocale());

                            $categoryName = $categoryTranslation?->name
                                ?? $post->category?->name
                                ?? 'Uncategorized';

                            $statusConfig = match ($post->status) {
                                'published' => [
                                    'label' => 'Published',
                                    'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'dot' => 'bg-emerald-500',
                                ],
                                'pending' => [
                                    'label' => 'Under review',
                                    'class' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    'dot' => 'bg-amber-500',
                                ],
                                'rejected' => [
                                    'label' => 'Rejected',
                                    'class' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    'dot' => 'bg-red-500',
                                ],
                                'archived' => [
                                    'label' => 'Archived',
                                    'class' => 'bg-stone-100 text-stone-600 ring-stone-500/20',
                                    'dot' => 'bg-stone-400',
                                ],
                                default => [
                                    'label' => 'Draft',
                                    'class' => 'bg-stone-100 text-stone-600 ring-stone-500/20',
                                    'dot' => 'bg-stone-400',
                                ],
                            };
                        @endphp

                        <tr class="transition hover:bg-stone-50/70">

                            {{-- Publication --}}
                            <td class="max-w-[360px] px-5 py-3.5">
                                <div class="flex min-w-0 items-start gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 text-stone-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A1.5 1.5 0 0 1 5.5 4H18a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5.5A1.5 1.5 0 0 1 4 18.5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h8M8 16h5"/>
                                        </svg>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="break-words font-medium leading-5 text-stone-800">
                                            {{ $postTitle }}
                                        </p>

                                        @if ($postTranslation?->excerpt)
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-stone-500">
                                                {{ $postTranslation->excerpt }}
                                            </p>
                                        @endif

                                        <p class="mt-1 text-[10px] tracking-wide text-stone-400">
                                            ID: {{ $post->id }}
                                        </p>
                                    </div>

                                </div>
                            </td>


                            {{-- Category --}}
                            <td class="px-4 py-3.5">
                                <span class="inline-flex max-w-[170px] truncate rounded-md border border-stone-200 bg-white px-2 py-1 text-xs text-stone-600">
                                    {{ $categoryName }}
                                </span>
                            </td>


                            {{-- Products --}}
<td class="px-4 py-3.5">
    <div class="flex flex-col items-center gap-1.5">
        <span
            data-post-products-count="{{ $post->id }}"
            class="text-xs font-medium text-stone-600"
        >
            {{ $post->products_count }}
        </span>

        <button
            type="button"
            data-manage-products
            data-post-id="{{ $post->id }}"
            data-post-title="{{ $post->translation(app()->getLocale())?->title ?? $post->translation('en')?->title ?? '' }}"
            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-medium text-stone-600 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-300"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
            Manage
        </button>
    </div>
</td>

                            {{-- Engagement --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-3 text-xs text-stone-500">

                                    <span class="inline-flex items-center gap-1" title="Views">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                        </svg>
                                        {{ number_format($post->views_count) }}
                                    </span>

                                    <span class="inline-flex items-center gap-1" title="Likes">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 8.8c0 5.2-8.8 10-8.8 10S3.2 14 3.2 8.8A4.3 4.3 0 0 1 12 6.7a4.3 4.3 0 0 1 8.8 2.1Z"/>
                                        </svg>
                                        {{ number_format($post->likes_count) }}
                                    </span>

                                    <span class="inline-flex items-center gap-1" title="Comments">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H5l-2 2v-9.5a7.5 7.5 0 1 1 17 0Z"/>
                                        </svg>
                                        {{ number_format($post->comments_count) }}
                                    </span>

                                </div>
                            </td>


                            

                            


                            {{-- Actions --}}
                            
                            
                            <td class="px-4 py-2">
    <div class="flex flex-col items-end gap-2">

        {{-- Status --}}
        <div class="inline-flex items-center px-1 py-1 text-xs font-semibold
            @if ($post->status === 'published')
                bg-green-100 text-green-800
            @elseif ($post->status === 'pending')
                bg-yellow-100 text-yellow-800
            @elseif ($post->status === 'rejected')
                bg-red-100 text-red-800
            @else
                bg-gray-100 text-gray-700
            @endif
        ">
            @if ($post->status === 'published')
                Published
            @elseif ($post->status === 'pending')
                On moderation
            @else
                {{ ucfirst($post->status) }}
            @endif
        </div>

        {{-- Creation date --}}
        <span
            class="text-[10px] lowercase tracking-wide text-stone-400"
            title="{{ $post->created_at?->format('Y-m-d H:i') }}"
        >
            created {{ $post->created_at?->format('d M Y') }}
        </span>

        {{-- Actions --}}
        <details class="post-actions-menu relative inline-block">

            <summary
                aria-label="Publication actions"
                title="More actions"
                class="flex h-6 w-6 cursor-pointer list-none items-center justify-center rounded-md border border-stone-200 bg-white text-stone-500 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300 [&::-webkit-details-marker]:hidden"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3.5 w-3.5"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <circle cx="5" cy="12" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="19" cy="12" r="1.5" />
                </svg>
            </summary>

            <div
                data-actions-dropdown
                class="fixed z-[9999] w-40 overflow-hidden rounded-lg border border-stone-200 bg-white p-1 shadow-xl"
            >

                {{-- View --}}
                @if ($post->status === 'published')
                    @php
                        $translation = $post->translations->firstWhere('locale', app()->getLocale())
                            ?? $post->translations->firstWhere('locale', 'en');
                    @endphp

                    @if ($translation?->slug)
                        <a
                            href="{{ route('community.posts.show', $translation->slug) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            data-close-post-actions
                            class="flex items-center rounded-md px-3 py-2 text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                            View
                        </a>
                    @endif
                @endif

                {{-- Preview --}}
                @if (in_array($post->status, ['draft', 'pending', 'rejected', 'archived'], true))
                    <a
                        href="{{ route('community.posts.preview', $post->id) }}"
                        data-close-post-actions
                        class="flex items-center rounded-md px-3 py-2 text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                            <circle cx="12" cy="12" r="2.5"/>
                        </svg>
                        Preview
                    </a>
                @endif

                {{-- Edit --}}
                @if ($post->status !== 'pending')
                    <a
                        href="{{ route('community.posts.edit', $post->id) }}"
                        data-close-post-actions
                        class="flex items-center rounded-md px-3 py-2 text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"/>
                        </svg>
                        Edit
                    </a>
                @endif

                {{-- Submit for review --}}
                @if (in_array($post->status, ['draft', 'rejected'], true))
                    <div class="my-1 border-t border-stone-100"></div>

                    <form
                        action="{{ route('community.posts.submit', $post->id) }}"
                        method="POST"
                        class="m-0"
                    >
                        @csrf

                        <button
                            type="submit"
                            data-close-post-actions
                            class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-emerald-700 transition hover:bg-emerald-50 hover:text-emerald-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                            </svg>
                            Submit for review
                        </button>
                    </form>
                @endif

                {{-- Archive --}}
                @if ($post->status === 'published')
                    <div class="my-1 border-t border-stone-100"></div>

                    <form
                        action="{{ route('community.posts.archive', $post->id) }}"
                        method="POST"
                        class="m-0"
                    >
                        @csrf

                        <button
                            type="submit"
                            data-close-post-actions
                            class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-stone-700 transition hover:bg-stone-50 hover:text-stone-950"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>
                            </svg>
                            Archive
                        </button>
                    </form>
                @endif

                {{-- Delete --}}
                <div class="my-1 border-t border-stone-100"></div>

                <form
                    action="{{ route('community.posts.destroy', $post->id) }}"
                    method="POST"
                    class="m-0"
                    onsubmit="return confirm('Are you sure you want to delete this publication?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        data-close-post-actions
                        class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-red-700 transition hover:bg-red-50 hover:text-red-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/>
                        </svg>
                        Delete
                    </button>
                </form>

            </div>
        </details>

    </div>
</td>



                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-stone-200 bg-stone-50 text-stone-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A1.5 1.5 0 0 1 5.5 4H18a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5.5A1.5 1.5 0 0 1 4 18.5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h8M8 16h5"/>
                                    </svg>
                                </div>

                                @if (request()->hasAny(['search', 'status', 'category']))
                                    <h3 class="mt-4 text-sm font-semibold text-stone-800">
                                        No publications found
                                    </h3>
                                    <p class="mx-auto mt-1 max-w-sm text-xs leading-5 text-stone-500">
                                        No publications match your current filters. Try changing your search criteria or resetting the filters.
                                    </p>

                                    <a
                                        href="{{ route('community.manage.index') }}"
                                        class="mt-4 inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50"
                                    >
                                        Reset filters
                                    </a>
                                @else
                                    <h3 class="mt-4 text-sm font-semibold text-stone-800">
                                        You haven't published anything yet
                                    </h3>
                                    <p class="mx-auto mt-1 max-w-sm text-xs leading-5 text-stone-500">
                                        Share your projects, products and ideas with professionals from the hospitality industry.
                                    </p>

                                    <a
                                        href="{{ route('community.posts.create') }}"
                                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-stone-800 px-4 py-2.5 text-xs font-medium text-white transition hover:bg-stone-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                                        </svg>
                                        Create your first publication
                                    </a>
                                @endif

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- Pagination --}}
        @if ($posts->hasPages())
            <div class="border-t border-stone-200 bg-white px-5 py-4">
                {{ $posts->withQueryString()->links() }}
            </div>
        @endif

    </div>

@include('community.partials.manage-product-drawer')

</div>


{{-- =============================================================
    FILTER PANEL SCRIPT
============================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggle-community-filters');
    const panel = document.getElementById('community-filters-panel');
    const chevron = document.getElementById('community-filters-chevron');

    if (!toggle || !panel || !chevron) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', String(!isExpanded));
        panel.hidden = isExpanded;
        chevron.classList.toggle('rotate-180', !isExpanded);
    });
});
</script>

@endsection
