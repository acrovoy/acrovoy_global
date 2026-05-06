@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Create RFQ</h1>
        <p class="text-sm text-gray-500 mt-1">
            Define your request details for suppliers
        </p>
    </div>

    <x-alerts />

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form method="POST" action="{{ route('buyer.rfqs.store') }}">
            @csrf

            <div class="p-6 space-y-6">

                {{-- TITLE --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Title
                    </label>

                    <input type="text"
                           name="title"
                           placeholder="e.g. CNC machined aluminum parts"
                           class="w-full mt-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        Description
                    </label>

                    <textarea name="description"
                              rows="5"
                              placeholder="Describe your requirements clearly..."
                              class="w-full mt-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400"></textarea>

                    <p class="text-xs text-gray-400 mt-1">
                        Be specific: materials, tolerances, use case, etc.
                    </p>
                </div>

                {{-- TYPE --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide">
                        RFQ Type
                    </label>

                    <select name="type"
                            class="w-full mt-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">

                        <option value="product">Product</option>
                        <option value="service">Service</option>
                        <option value="project">Project</option>

                    </select>
                </div>


                <div>
    <label class="text-xs text-gray-500 uppercase tracking-wide">
        Deadline
    </label>

    <input type="datetime-local"
           name="closed_at"
           class="w-full mt-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">

    <p class="text-xs text-gray-400 mt-1">
        When suppliers can submit offers until
    </p>
</div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">

                <div class="text-xs text-gray-400">
                    You can edit all fields later in workspace
                </div>

                <button class="px-5 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800 transition">
                    Create RFQ
                </button>

            </div>

        </form>

    </div>

</div>

@endsection