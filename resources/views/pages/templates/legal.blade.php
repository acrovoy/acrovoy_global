@extends('layouts.app')

@section('content')

<div class="bg-gray-50 min-h-screen">

    <div class="mx-auto max-w-7xl px-6 py-12">

        <div class="grid gap-12 lg:grid-cols-[260px_1fr]">

            {{-- LEFT INFO --}}
            <aside class="hidden lg:block">

                <div class="sticky top-8">

                    <div class="rounded-2xl border border-gray-200 bg-white p-6">

                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
                            Documentation
                        </div>

                        <h1 class="mt-4 text-2xl font-semibold leading-tight text-gray-900">
                            {{ $page->translation?->title }}
                        </h1>

                        @if($page->translation?->excerpt)

                            <p class="mt-4 text-sm leading-7 text-gray-600">
                                {{ $page->translation?->excerpt }}
                            </p>

                        @endif

                        <div class="mt-8 space-y-4 border-t border-gray-200 pt-6">

                            <div>

                                <div class="text-xs uppercase tracking-wide text-gray-500">
                                    Published
                                </div>

                                <div class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $page->published_at?->format('F d, Y') }}
                                </div>

                            </div>

                            <div>

                                <div class="text-xs uppercase tracking-wide text-gray-500">
                                    Updated
                                </div>

                                <div class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $page->updated_at?->format('F d, Y') }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </aside>


            {{-- DOCUMENT --}}
            <article>

                <div class="rounded-2xl border border-gray-200 bg-white">

                    <div class="border-b border-gray-200 px-10 py-8">

                        <div class="flex flex-wrap items-center gap-3">

                            <span class="rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Official Document
                            </span>

                            <span class="text-sm text-gray-500">
                                Last updated
                                {{ $page->updated_at?->format('F d, Y') }}
                            </span>

                        </div>

                        <h1 class="mt-5 text-4xl font-semibold tracking-tight text-gray-900">
                            {{ $page->translation?->title }}
                        </h1>

                        @if($page->translation?->excerpt)

                            <p class="mt-5 max-w-3xl text-lg leading-8 text-gray-600">
                                {{ $page->translation?->excerpt }}
                            </p>

                        @endif

                    </div>


                    <div class="px-10 py-12 lg:px-14">

                        <div class="prose max-w-none">

                            {!! $page->translation?->content !!}

                        </div>

                    </div>

                </div>


                <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-8">

                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                        <div>

                            <h2 class="text-xl font-semibold text-gray-900">
                                Questions about this document?
                            </h2>

                            <p class="mt-2 text-gray-600">
                                If you require clarification regarding this policy,
                                our support team will be happy to assist you.
                            </p>

                        </div>

                        <a href="{{ route('help.index') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white transition hover:bg-black">

                            Contact Support

                        </a>

                    </div>

                </div>

            </article>

        </div>

    </div>

</div>

@endsection