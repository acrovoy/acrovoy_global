@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-5xl mx-auto">

    {{-- BREADCRUMB --}}
    <div class="mb-6">
        <a href="{{ route('dashboard.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4"> ← Back to companies </a>
    </div>


    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-8">

        <div>
            <div class="flex items-center gap-2 mb-2">

                <span class="inline-flex items-center rounded-full
                             bg-gray-100 px-2.5 py-1
                             text-[11px] font-semibold uppercase tracking-wide
                             text-gray-600">
                    {{ ucfirst($company->type) }}
                </span>

                <span class="text-xs text-gray-400">
                    Company #{{ $company->id }}
                </span>

            </div>

            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
                Edit company
            </h1>

            <p class="mt-2 text-sm text-gray-500 max-w-2xl">
                Update the information and settings for
                <span class="font-medium text-gray-700">
                    {{ $company->name }}
                </span>.
            </p>
        </div>

        {{-- COMPANY STATUS --}}
        <div class="shrink-0">

            <div class="inline-flex items-center gap-2
                        rounded-lg border border-gray-200
                        bg-white px-3 py-2
                        shadow-sm">

                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                <span class="text-xs font-medium text-gray-600">
                    Active company
                </span>

            </div>

        </div>

    </div>


    {{-- CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_280px] gap-6 items-start">

        {{-- MAIN FORM --}}
        <div>
            @include('dashboard.companies.partials.form', [
                'company' => $company,
                'type' => $type ?? $company->type ?? null
            ])
        </div>


        {{-- SIDEBAR --}}
        <aside class="space-y-4">

            {{-- COMPANY INFO --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Company information
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        Current company details
                    </p>
                </div>

                <div class="p-5 space-y-4">

                    {{-- TYPE --}}
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                            Type
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-800">
                            {{ ucfirst($company->type) }}
                        </div>
                    </div>

                    {{-- ID --}}
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                            Company ID
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-800">
                            #{{ $company->id }}
                        </div>
                    </div>

                    {{-- SLUG --}}
                    @if($company->slug)
                        <div>
                            <div class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                URL identifier
                            </div>

                            <div class="mt-1 text-sm font-medium text-gray-800 break-all">
                                {{ $company->slug }}
                            </div>
                        </div>
                    @endif

                </div>

            </div>


            {{-- TYPE LOCKED --}}
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 shrink-0 rounded-lg
                                bg-white border border-gray-200
                                flex items-center justify-center">

                        <svg class="w-4.5 h-4.5 text-gray-500"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm2-9V7a4 4 0 118 0v3"/>
                        </svg>

                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">
                            Company type is locked
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            The company type cannot be changed after the company has been created.
                        </p>
                    </div>

                </div>

            </div>


            {{-- OWNERSHIP --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 shrink-0 rounded-lg
                                bg-gray-50 border border-gray-200
                                flex items-center justify-center">

                        <svg class="w-4.5 h-4.5 text-gray-500"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm8-3v6m3-3h-6"/>
                        </svg>

                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">
                            Company workspace
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            Your access and permissions are managed through your company membership.
                        </p>
                    </div>

                </div>

            </div>

        </aside>

    </div>

</div>

@endsection