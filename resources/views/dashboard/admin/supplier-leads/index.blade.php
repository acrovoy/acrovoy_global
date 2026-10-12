@extends('dashboard.admin.layout')

@section('dashboard-content')

{{-- ============================================================
    HEADER
============================================================= --}}

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">
            Factories & Suppliers CRM
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Manage potential suppliers and track platform acquisition.
        </p>
    </div>

    <a href="{{ route('admin.supplier-leads.create') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 text-sm shadow-sm transition">
        <span class="text-lg leading-none">+</span>
        Add Supplier Lead
    </a>
</div>

<x-alerts />

{{-- ============================================================
    SUMMARY
============================================================= --}}

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
            Total Leads
        </p>

        <p class="text-2xl font-semibold text-gray-900 mt-2">
            {{ $supplierLeads->total() }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Matching current filters
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
            New Leads
        </p>

        <p class="text-2xl font-semibold text-gray-900 mt-2">
            {{ \App\Domain\SupplierLead\Models\SupplierLead::where('acquisition_status', 'new')->count() }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Awaiting initial processing
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
            In Discussion
        </p>

        <p class="text-2xl font-semibold text-gray-900 mt-2">
            {{ \App\Domain\SupplierLead\Models\SupplierLead::where('acquisition_status', 'in_discussion')->count() }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Acquisition in progress
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
            Registered
        </p>

        <p class="text-2xl font-semibold text-gray-900 mt-2">
            {{ \App\Domain\SupplierLead\Models\SupplierLead::whereIn('acquisition_status', ['registered', 'activated'])->count() }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Registered or activated
        </p>
    </div>

</div>

{{-- ============================================================
    FILTERS
============================================================= --}}

<form method="GET"
      action="{{ route('admin.supplier-leads.index') }}"
      class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold text-gray-800 text-sm">
                Filters
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Search and filter your supplier acquisition pipeline.
            </p>
        </div>

        @if(request()->hasAny(['search', 'acquisition_status', 'company_type', 'priority', 'assigned_to']))
            <a href="{{ route('admin.supplier-leads.index') }}"
               class="text-sm text-gray-500 hover:text-gray-900 hover:underline">
                Reset
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- SEARCH --}}
        <div class="xl:col-span-2">
            <label for="supplier-lead-search"
                   class="block text-xs font-medium text-gray-600 mb-1">
                Search
            </label>

            <input id="supplier-lead-search"
                   type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Company, email, website or city..."
                   class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>

        {{-- STATUS --}}
        <div>
            <label for="supplier-lead-status"
                   class="block text-xs font-medium text-gray-600 mb-1">
                Acquisition Status
            </label>

            <select id="supplier-lead-status"
                    name="acquisition_status"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">

                <option value="">All statuses</option>

                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}"
                        @selected(request('acquisition_status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach

            </select>
        </div>

        {{-- COMPANY TYPE --}}
        <div>
            <label for="supplier-lead-company-type"
                   class="block text-xs font-medium text-gray-600 mb-1">
                Company Type
            </label>

            <select id="supplier-lead-company-type"
                    name="company_type"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">

                <option value="">All types</option>

                @foreach($companyTypes as $value => $label)
                    <option value="{{ $value }}"
                        @selected(request('company_type') === $value)>
                        {{ $label }}
                    </option>
                @endforeach

            </select>
        </div>

        {{-- PRIORITY --}}
        <div>
            <label for="supplier-lead-priority"
                   class="block text-xs font-medium text-gray-600 mb-1">
                Priority
            </label>

            <select id="supplier-lead-priority"
                    name="priority"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">

                <option value="">All priorities</option>

                @foreach($priorities as $value => $label)
                    <option value="{{ $value }}"
                        @selected(request('priority') === $value)>
                        {{ $label }}
                    </option>
                @endforeach

            </select>
        </div>

        {{-- ASSIGNED USER --}}
        <div>
            <label for="supplier-lead-assigned"
                   class="block text-xs font-medium text-gray-600 mb-1">
                Assigned To
            </label>

            <select id="supplier-lead-assigned"
                    name="assigned_to"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">

                <option value="">All administrators</option>

                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        @selected((string) request('assigned_to') === (string) $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach

            </select>
        </div>

        {{-- APPLY --}}
        <div class="flex items-end">
            <button type="submit"
                    class="w-full h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition">
                Apply Filters
            </button>
        </div>

    </div>
</form>

{{-- ============================================================
    SUPPLIER LEADS TABLE
============================================================= --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- TABLE HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="font-semibold text-gray-800 text-sm">
                    Supplier Pipeline
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Internal database of factories, brands and suppliers.
                </p>
            </div>

            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-200 text-gray-700 text-xs font-medium">
                {{ $supplierLeads->total() }} records
            </span>
        </div>
    </div>

    @if($supplierLeads->count())

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-white border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Company
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Type
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Location
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Status
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Priority
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Assigned To
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Contacts
                        </th>

                        <th class="px-5 py-3 text-left font-medium text-gray-500 whitespace-nowrap">
                            Acrovoy Profile
                        </th>

                        <th class="px-5 py-3 text-right font-medium text-gray-500 whitespace-nowrap">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @foreach($supplierLeads as $lead)

                        <tr
    onclick="window.location='{{ route('admin.supplier-leads.show', $lead) }}'"
    class="cursor-pointer hover:bg-gray-50 transition"
>

                            {{-- COMPANY --}}
                            <td class="px-5 py-4 min-w-[220px]">
                                <div class="font-semibold text-gray-900">
                                    {{ $lead->company_name }}
                                </div>

                                @if($lead->website)
                                    <a href="{{ $lead->website }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="block text-xs text-gray-500 hover:text-gray-900 hover:underline mt-1 break-all">
                                        {{ $lead->website }}
                                    </a>
                                @elseif($lead->email)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $lead->email }}
                                    </div>
                                @endif

                                <div class="flex flex-wrap gap-1 mt-2">
                                    @if($lead->contacts_count)
                                        <span class="inline-flex px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs">
                                            {{ $lead->contacts_count }} contacts
                                        </span>
                                    @endif

                                    @if($lead->activities_count)
                                        <span class="inline-flex px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs">
                                            {{ $lead->activities_count }} activities
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- TYPE --}}
                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $companyTypes[$lead->company_type] ?? ucfirst(str_replace('_', ' ', $lead->company_type)) }}
                            </td>

                            {{-- LOCATION --}}
                            <td class="px-5 py-4 text-gray-600">
                                <div>
                                    {{ $lead->city ?: '—' }}
                                </div>

                                @if($lead->country_id)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $lead->country_id }}
                                    </div>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'new' => 'bg-gray-100 text-gray-700',
                                        'to_contact' => 'bg-blue-50 text-blue-700',
                                        'contacted' => 'bg-sky-50 text-sky-700',
                                        'in_discussion' => 'bg-indigo-50 text-indigo-700',
                                        'interested' => 'bg-emerald-50 text-emerald-700',
                                        'invited' => 'bg-violet-50 text-violet-700',
                                        'registered' => 'bg-amber-50 text-amber-700',
                                        'activated' => 'bg-green-50 text-green-700',
                                        'on_hold' => 'bg-orange-50 text-orange-700',
                                        'not_interested' => 'bg-red-50 text-red-700',
                                        'disqualified' => 'bg-red-100 text-red-800',
                                    ];

                                    $statusClass = $statusClasses[$lead->acquisition_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $statusClass }}">
                                    {{ $statuses[$lead->acquisition_status] ?? ucfirst(str_replace('_', ' ', $lead->acquisition_status)) }}
                                </span>
                            </td>

                            {{-- PRIORITY --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $priorityClasses = [
                                        'low' => 'bg-gray-100 text-gray-600',
                                        'normal' => 'bg-blue-50 text-blue-700',
                                        'high' => 'bg-amber-50 text-amber-700',
                                    ];
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $priorityClasses[$lead->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $priorities[$lead->priority] ?? ucfirst($lead->priority) }}
                                </span>
                            </td>

                            {{-- ASSIGNED --}}
                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                {{ $lead->assignedUser?->name ?? 'Unassigned' }}
                            </td>

                            {{-- CONTACTS --}}
                            <td class="px-5 py-4 text-gray-600 text-center">
                                {{ $lead->contacts_count ?? 0 }}
                            </td>

                            {{-- LINKED SUPPLIER --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($lead->supplier)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium">
                                        Linked #{{ $lead->supplier->id }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 text-xs">
                                        Not linked
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.supplier-leads.edit', $lead) }}"
                                   class="text-sm text-gray-700 hover:text-gray-900 hover:underline font-medium">
                                    Manage
                                </a>

                                <form action="{{ route('admin.supplier-leads.destroy', $lead) }}"
                                      method="POST"
                                      class="inline ml-3"
                                      onsubmit="return confirm('Move this supplier lead to the trash?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-sm text-red-600 hover:text-red-800 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($supplierLeads->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $supplierLeads->links() }}
            </div>
        @endif

    @else

        <div class="px-5 py-12 text-center">
            <div class="text-sm font-medium text-gray-800">
                No supplier leads found
            </div>

            <p class="text-sm text-gray-500 mt-1">
                Add a factory or supplier to start building your acquisition pipeline.
            </p>

            <a href="{{ route('admin.supplier-leads.create') }}"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 text-sm transition">
                <span class="text-lg leading-none">+</span>
                Add Supplier Lead
            </a>
        </div>

    @endif

</div>

@endsection