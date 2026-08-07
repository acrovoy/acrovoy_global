@extends('layouts.app')

@section('content')

<div class="bg-white">

    {{-- Intro --}}
    <section class="border-b border-gray-100">

        <div class="mx-auto max-w-6xl px-6 py-20">

            <div class="max-w-3xl">

                <span class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">
                    Company
                </span>

                <h1 class="mt-6 text-5xl font-semibold tracking-tight text-gray-900">
                    {{ $page->translation?->title }}
                </h1>

                @if($page->translation?->excerpt)

                    <p class="mt-8 text-xl leading-9 text-gray-600">
                        {{ $page->translation?->excerpt }}
                    </p>

                @endif

            </div>

        </div>

    </section>


    {{-- Content --}}
    <section>

        <div class="mx-auto grid max-w-6xl grid-cols-12 gap-16 px-6 py-20">

            {{-- Left --}}
            <aside class="col-span-12 lg:col-span-3">

                <div class="sticky top-32">

                    <div class="text-sm font-semibold uppercase tracking-wider text-gray-400">
                        Acrovoy
                    </div>

                    <div class="mt-6 space-y-5 text-sm text-gray-600">

                        <div>

                            <div class="text-xs uppercase tracking-wider text-gray-400">
                                Founded
                            </div>

                            <div class="mt-1">
                                2026
                            </div>

                        </div>

                        <div>

                            <div class="text-xs uppercase tracking-wider text-gray-400">
                                Platform
                            </div>

                            <div class="mt-1">
                                Global B2B Marketplace
                            </div>

                        </div>

                        <div>

                            <div class="text-xs uppercase tracking-wider text-gray-400">
                                Updated
                            </div>

                            <div class="mt-1">
                                {{ $page->updated_at?->format('F d, Y') }}
                            </div>

                        </div>

                    </div>

                </div>

            </aside>


            {{-- Right --}}
            <article class="col-span-12 lg:col-span-9">

                <div class="prose prose-lg max-w-none">

                    {!! $page->translation?->content !!}

                </div>

            </article>

        </div>

    </section>

</div>

@endsection