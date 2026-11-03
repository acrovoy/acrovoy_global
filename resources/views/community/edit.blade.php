@extends('dashboard.layout')

@section('dashboard-content')

<div class="mx-auto max-w-5xl space-y-8">

    {{-- PAGE HEADER --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('community.manage.index') }}" class="hover:text-gray-900">
                    My Publications
                </a>
                <span>/</span>
                <span class="text-gray-900">Edit</span>
            </div>

            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                Edit Publication
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Update your publication content, translations and images.
            </p>
        </div>


        {{-- STATUS --}}

        @php
            $statusStyles = [
                'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                'archived' => 'bg-slate-100 text-slate-600 border-slate-200',
            ];

            $statusLabels = [
                'draft' => 'Draft',
                'pending' => 'Under Review',
                'published' => 'Published',
                'rejected' => 'Rejected',
                'archived' => 'Archived',
            ];
        @endphp

        <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusStyles[$post->status] ?? 'border-gray-200 bg-gray-50 text-gray-600' }}">
            {{ $statusLabels[$post->status] ?? ucfirst($post->status) }}
        </span>

    </div>


    {{-- MODERATION NOTICE --}}

    @if($post->status === 'pending')
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>

                <div>
                    <h3 class="text-sm font-semibold text-amber-900">
                        Publication under review
                    </h3>
                    <p class="mt-1 text-sm leading-6 text-amber-800">
                        Your publication has been submitted for moderation. It will become publicly available after approval.
                    </p>
                </div>
            </div>
        </div>
    @endif


    @if($post->status === 'rejected')
        <div class="rounded-xl border border-red-200 bg-red-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m15 9-6 6M9 9l6 6"/>
                </svg>

                <div>
                    <h3 class="text-sm font-semibold text-red-900">
                        Publication requires changes
                    </h3>

                    @if($post->reject_reason)
                        <p class="mt-1 text-sm leading-6 text-red-800">
                            {{ $post->reject_reason }}
                        </p>
                    @else
                        <p class="mt-1 text-sm text-red-800">
                            Please review and update your publication before submitting it again.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif


    {{-- VALIDATION ERRORS --}}

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01"/>
                </svg>

                <div>
                    <h3 class="text-sm font-semibold text-red-800">
                        Please review the following errors
                    </h3>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif


    @include('community.partials._form')


    {{-- SUBMIT FOR MODERATION --}}

    @if(in_array($post->status, ['draft', 'rejected'], true))

        <div class="rounded-2xl border border-gray-200 bg-white p-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Ready to publish?
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Submit your publication for review by the Acrovoy team.
                    </p>
                </div>

                <form
                    action="{{ route('community.posts.submit', $post->id) }}"
                    method="POST"
                    onsubmit="return confirm('Submit this publication for moderation?');">

                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-700">

                        Submit for Review

                        <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>

                    </button>

                </form>

            </div>

        </div>

    @endif

</div>

@endsection