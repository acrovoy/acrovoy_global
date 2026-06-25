@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">
            Create RFQ
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Define procurement requirements and invite suppliers to submit offers.
        </p>
    </div>

    <x-alerts />

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form method="POST" action="{{ route('buyer.rfqs.store') }}">
            @csrf

            <div class="p-6 space-y-8">

                {{-- TITLE --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Title
                    </label>

                    <input type="text"
                           name="title"
                           placeholder="Restaurant furniture for dining areas, including tables, chairs, and bar seating..."
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-900">
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Description
                    </label>

                    <textarea name="description"
                              rows="6"
                              placeholder="Write a short request to suppliers. Describe what you need, expected outcome, and key context..."
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-900"></textarea>

                    <p class="text-xs text-gray-400 mt-2">
                        The more detail you provide, the more accurate supplier offers will be.
                    </p>
                </div>

                {{-- TYPE --}}
                <div class="space-y-2">

    <label class="text-xs text-gray-500 uppercase tracking-wide">
        RFQ Type
    </label>

    <div class="grid gap-2">

        <label class="border rounded-lg p-3 cursor-pointer hover:border-gray-400 transition flex items-start gap-3">
            <input type="radio"
       name="type"
       value="product"
       checked
       class="
           mt-1
           text-gray-900 focus:ring-gray-900
       ">

            <div>
                <div class="text-sm font-medium text-gray-900">
                    Product Procurement
                </div>
                <div class="text-xs text-gray-500">
                    Standard purchase of goods (materials, equipment, furniture, etc.)
                </div>
            </div>
        </label>

        <label class="border rounded-lg p-3 cursor-pointer hover:border-gray-400 transition flex items-start gap-3">
            <input type="radio"
       name="type"
       value="project"
       class="mt-1 text-gray-900 focus:ring-gray-900">

            <div>
                <div class="text-sm font-medium text-gray-900">
                    Project / Turnkey Solution
                </div>
                <div class="text-xs text-gray-500">
                    End-to-end execution including design, production, and delivery
                </div>
            </div>
        </label>

    </div>

</div>

                {{-- DEADLINE --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Deadline
                    </label>

                    <input type="datetime-local"
                           name="closed_at"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-900">

                    <p class="text-xs text-gray-400 mt-2">
                        Suppliers will be able to submit offers until this time.
                    </p>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">

                <div class="text-xs text-gray-400">
                    You can update all fields later in the RFQ workspace
                </div>

                <button class="px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg
                               hover:bg-gray-800 transition">
                    Create RFQ
                </button>

            </div>

        </form>

    </div>

</div>

@endsection