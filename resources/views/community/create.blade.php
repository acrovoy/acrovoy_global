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
                <span class="text-gray-900">Create</span>
            </div>

            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                Create Publication
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Share your projects, products, ideas and insights with the Acrovoy community.
            </p>
        </div>

        <span class="inline-flex w-fit items-center rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600">
            New Publication
        </span>

    </div>


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

</div>

@endsection