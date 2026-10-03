@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-5xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('dashboard.companies.index', ['type' => $type]) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4"> ← Тo companies </a>
    </div>

    {{-- Page header --}}
    <div class="flex flex-col gap-2 mb-8">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                Create company
            </h1>

            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                New
            </span>
        </div>

        <p class="text-sm leading-6 text-gray-500 max-w-2xl">
            Create a company profile to manage your business activities on ACROVOY.
            You can complete additional company details later.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_280px] gap-6 items-start">

        {{-- Main form --}}
        <div>
            @include('dashboard.companies.partials.form', [
                'company' => null,
                'type' => $type
            ])
        </div>

        {{-- Side information --}}
        <aside class="space-y-4">

            {{-- What happens next --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    What happens next
                </h3>

                <div class="space-y-4">

                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600">
                            1
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                Company is created
                            </p>

                            <p class="mt-0.5 text-xs leading-5 text-gray-500">
                                Your account becomes the company owner.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600">
                            2
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                Complete the profile
                            </p>

                            <p class="mt-0.5 text-xs leading-5 text-gray-500">
                                Add contact details, address, logo and other information.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600">
                            3
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                Start working
                            </p>

                            <p class="mt-0.5 text-xs leading-5 text-gray-500">
                                Use the company workspace according to its role on ACROVOY.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Privacy / ownership --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">

                <div class="flex items-start gap-3">

                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-gray-500"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 15v2m0-10a4 4 0 00-4 4v1h8v-1a4 4 0 00-4-4zm0 0V5m-7 6h14v9H5v-9z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            You will be the owner
                        </p>

                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            The account creating this company is automatically added
                            as its owner. Additional team members can be managed later.
                        </p>
                    </div>

                </div>
            </div>

        </aside>

    </div>

</div>

@endsection