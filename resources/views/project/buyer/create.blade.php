@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">
            Create Project
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            A project groups multiple procurement requests into a single purchasing process.
            After creating the project, you can add individual RFQs for each product you need to source.
        </p>
    </div>

    <x-alerts />

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form method="POST" action="{{ route('buyer.projects.store') }}">
            @csrf

            <div class="p-6 space-y-8">

                {{-- PROJECT NAME --}}
                <div>

                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">
                        Project Name
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Hotel Renovation 2027, New Restaurant Opening, Office Furniture Procurement..."
                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm
                               focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 focus:outline-none">

                    <p class="mt-2 text-xs text-gray-400">
                        Choose a name that helps you easily identify this procurement project.
                    </p>

                </div>

                {{-- DESCRIPTION --}}
                <div>

                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">
                        Project Description
                    </label>

                    <textarea
                        name="description"
                        rows="7"
                        placeholder="Describe the overall procurement project, business objectives, delivery expectations, location, special requirements, or any information that applies to all RFQs within this project."
                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm
                               focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 focus:outline-none">{{ old('description') }}</textarea>

                    <p class="mt-2 text-xs text-gray-400">
                        This information describes the project as a whole. Product-specific requirements will be defined later in individual RFQs.
                    </p>

                </div>

                {{-- TARGET DATE --}}
                <div>

                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">
                        Target Completion Date
                    </label>

                    <input
                        type="datetime-local"
                        name="closed_at"
                        value="{{ old('closed_at') }}"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm
                               focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 focus:outline-none">

                    <p class="mt-2 text-xs text-gray-400">
                        Optional. The desired date by which the entire procurement project should be completed.
                    </p>

                </div>

                {{-- INFO BOX --}}
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

                    <div class="flex items-start gap-3">

                        <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>

                        <div>

                            <h3 class="text-sm font-semibold text-blue-900">
                                What happens next?
                            </h3>

                            <ul class="mt-2 space-y-1 text-sm text-blue-800 list-disc list-inside">

                                <li>Create your procurement project.</li>

                                <li>Add one RFQ for each product you want to purchase.</li>

                                <li>Receive supplier offers separately for every RFQ.</li>

                                <li>Manage all procurement activities from one project workspace.</li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">

                <div class="text-xs text-gray-500">
                    You can edit project information at any time.
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800">

                    Create Project

                </button>

            </div>

        </form>

    </div>

</div>

@endsection