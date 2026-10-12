@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col max-w-6xl gap-6">

    <x-alerts />

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

        <div class="flex items-start gap-4 min-w-0">

            <div class="w-14 h-14 shrink-0 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center">
                <svg class="w-7 h-7 text-stone-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 12v.01M15 15v.01M15 18v.01"/>
                </svg>
            </div>

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2">

                    <h1 class="text-xl font-semibold text-gray-900 break-words">
                        {{ $supplierLead->company_name }}
                    </h1>

                    @php
                        $statusStyles = [
                            'new' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'to_contact' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'contacted' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
                            'in_discussion' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                            'interested' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'invited' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                            'registered' => 'bg-teal-50 text-teal-700 ring-teal-600/20',
                            'activated' => 'bg-green-50 text-green-700 ring-green-600/20',
                            'on_hold' => 'bg-gray-100 text-gray-700 ring-gray-500/20',
                            'not_interested' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                            'disqualified' => 'bg-red-50 text-red-700 ring-red-600/20',
                        ];

                        $priorityStyles = [
                            'low' => 'bg-gray-100 text-gray-600',
                            'normal' => 'bg-blue-50 text-blue-700',
                            'high' => 'bg-red-50 text-red-700',
                        ];

                        $statusLabels = $statuses ?? [];
                        $priorityLabels = $priorities ?? [];
                    @endphp

                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$supplierLead->acquisition_status] ?? 'bg-gray-100 text-gray-700 ring-gray-500/20' }}">
                        {{ $statusLabels[$supplierLead->acquisition_status] ?? str($supplierLead->acquisition_status)->replace('_', ' ')->title() }}
                    </span>

                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityStyles[$supplierLead->priority] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $priorityLabels[$supplierLead->priority] ?? str($supplierLead->priority)->title() }} priority
                    </span>

                </div>

                @if($supplierLead->legal_name)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $supplierLead->legal_name }}
                    </p>
                @endif

                <p class="mt-2 text-xs text-gray-400">
                    Lead #{{ $supplierLead->id }}
                    <span class="mx-1">·</span>
                    Added {{ $supplierLead->created_at?->format('M d, Y') ?? '—' }}
                </p>

            </div>

        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">

            <a
                href="{{ route('admin.supplier-leads.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7-7-7 7-7"/>
                </svg>
                Back
            </a>

            <a
                href="{{ route('admin.supplier-leads.edit', $supplierLead) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 2.651 2.651M7 17l-3 1 1-3L16.862 3.136a1.875 1.875 0 0 1 2.652 0l1.35 1.35a1.875 1.875 0 0 1 0 2.652L9 19H7v-2Z"/>
                </svg>
                Edit Lead
            </a>

        </div>

    </div>

    {{-- QUICK INFORMATION --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-500">Company Type</p>
            <p class="mt-2 text-sm font-semibold text-gray-900">
                {{ ($companyTypes ?? [])[$supplierLead->company_type] ?? str($supplierLead->company_type)->replace('_', ' ')->title() }}
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-500">Assigned Manager</p>
            <p class="mt-2 text-sm font-semibold text-gray-900">
                {{ $supplierLead->assignedUser?->name ?? 'Unassigned' }}
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-500">Product Categories</p>
            <p class="mt-2 text-sm font-semibold text-gray-900">
                {{ $supplierLead->categories->count() }}
                {{ str('category')->plural($supplierLead->categories->count()) }}
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-500">Supplier Profile</p>

            @if($supplierLead->supplier_id)
                <p class="mt-2 text-sm font-semibold text-emerald-700">
                    Linked · #{{ $supplierLead->supplier_id }}
                </p>
            @else
                <p class="mt-2 text-sm font-semibold text-gray-500">
                    Not linked
                </p>
            @endif
        </div>

    </div>

    {{-- MAIN CONTENT --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    {{-- COMPANY INFORMATION --}}
    <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Company Information
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        General information about this factory or supplier.
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                    <div>
                        <p class="text-xs text-gray-500">Company Name</p>
                        <p class="mt-1.5 text-sm font-medium text-gray-900">
                            {{ $supplierLead->company_name ?: '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Legal Company Name</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->legal_name ?: '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Website</p>

                        @if($supplierLead->website)
                            <a
                                href="{{ $supplierLead->website }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-1.5 inline-flex items-center gap-1 text-sm text-blue-700 hover:text-blue-900 break-all"
                            >
                                {{ $supplierLead->website }}
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 4h6v6m-11 4L20 4M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/>
                                </svg>
                            </a>
                        @else
                            <p class="mt-1.5 text-sm text-gray-400">Not provided</p>
                        @endif
                    </div>


                    <div>
    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
        Company LinkedIn
    </dt>

    <dd class="mt-1 break-words text-sm font-medium text-gray-900">
        @if($supplierLead->linkedin_url)
            <a
                href="{{ $supplierLead->linkedin_url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="text-blue-700 hover:underline"
            >
                Open Company LinkedIn Page
            </a>
        @else
            —
        @endif
    </dd>
</div>



                    <div>
                        <p class="text-xs text-gray-500">Lead Source</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->source ?: '—' }}
                        </p>
                    </div>

                </div>

            </section>

            {{-- CONTACT INFORMATION --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Contact Information
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        General company contact details.
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs text-gray-500">Email</p>

                            @if($supplierLead->email)
                                <a
                                    href="mailto:{{ $supplierLead->email }}"
                                    class="mt-1 block text-sm font-medium text-gray-900 hover:text-blue-700 break-all"
                                >
                                    {{ $supplierLead->email }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-gray-400">Not provided</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h4l2 5-2.5 1.5a15 15 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2C10 21 3 14 3 5a2 2 0 0 1 2-2Z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Phone</p>

                            @if($supplierLead->phone)
                                <a
                                    href="tel:{{ $supplierLead->phone }}"
                                    class="mt-1 block text-sm font-medium text-gray-900 hover:text-blue-700"
                                >
                                    {{ $supplierLead->phone }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-gray-400">Not provided</p>
                            @endif
                        </div>
                    </div>

                </div>

            </section>

            {{-- LOCATION --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Company Location
                    </h2>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <p class="text-xs text-gray-500">Country</p>
                        <p class="mt-1.5 text-sm font-medium text-gray-900">
                            {{ $supplierLead->country?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">City</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->city ?: '—' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Address</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->address ?: '—' }}
                        </p>
                    </div>

                </div>

            </section>

            {{-- PRODUCT CATEGORIES --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Product Categories
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Product categories associated with this prospect.
                    </p>
                </div>

                <div class="p-5">

                    @if($supplierLead->categories->isNotEmpty())

                        <div class="flex flex-wrap gap-2">

                            @foreach($supplierLead->categories as $category)

                                @php
                                    $categoryName = $category->translations
                                        ->firstWhere('language_code', app()->getLocale())
                                        ?->name
                                        ?? $category->name
                                        ?? ('Category #' . $category->id);
                                @endphp

                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-stone-50 border border-stone-200 text-sm text-stone-700">
                                    {{ $categoryName }}
                                </span>

                            @endforeach

                        </div>

                    @else

                        <div class="flex flex-col items-center text-center py-6">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7h-9m9 10h-9M4 7h.01M4 17h.01M7 7h.01M7 17h.01"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-gray-700">
                                No product categories assigned
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Edit this lead to associate relevant categories.
                            </p>
                        </div>

                    @endif

                </div>

            </section>

            {{-- INTERNAL NOTES --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Internal Notes
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Private information for the Acrovoy acquisition team.
                    </p>
                </div>

                <div class="p-5">

                    @if($supplierLead->notes)
                        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $supplierLead->notes }}</div>
                    @else
                        <p class="text-sm text-gray-400">
                            No internal notes have been added.
                        </p>
                    @endif

                </div>

            </section>

        

        {{-- RIGHT COLUMN --}}
        <aside class="flex flex-col gap-6">


        {{-- CONTACTS --}}
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Contacts</h2>
            <p class="mt-1 text-sm text-gray-500">
                Contact persons at this company
            </p>
        </div>

        <a
            href="{{ route('admin.supplier-leads.contacts.create', $supplierLead) }}"
            class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700"
        >
            + Add Contact
        </a>
    </div>

    <div class="mt-5 space-y-3">
    @forelse ($supplierLead->contacts as $contact)
        <a
            href="{{ route('admin.supplier-leads.contacts.show', [$supplierLead, $contact]) }}"
            class="group flex items-center justify-between gap-4 rounded-xl border border-gray-200 border-l-4 border-l-slate-300 bg-white px-5 py-4 shadow-sm transition duration-200 hover:border-gray-300 hover:border-l-slate-600 hover:bg-slate-50 hover:shadow-md"
        >
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-gray-900 group-hover:text-slate-700">
                    {{ trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) ?: 'Unnamed contact' }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $contact->job_title ?: 'Position not specified' }}
                </p>
            </div>

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0 text-gray-400 transition group-hover:translate-x-0.5 group-hover:text-gray-700"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m9 18 6-6-6-6"
                />
            </svg>
        </a>
    @empty
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center">
            <p class="text-sm font-semibold text-gray-800">
                No contacts added yet
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Add a contact person for this company.
            </p>
        </div>
    @endforelse
</div>
</div>


            {{-- ACQUISITION STATUS --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Acquisition Overview
                    </h2>
                </div>

                <div class="p-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-500">Current Status</p>
                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                            {{ $statusLabels[$supplierLead->acquisition_status] ?? str($supplierLead->acquisition_status)->replace('_', ' ')->title() }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500">Priority</p>
                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                            {{ $priorityLabels[$supplierLead->priority] ?? str($supplierLead->priority)->title() }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500">Assigned Manager</p>
                        <p class="mt-1.5 text-sm font-medium text-gray-900">
                            {{ $supplierLead->assignedUser?->name ?? 'Unassigned' }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->creator?->name ?? '—' }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500">Last Updated</p>
                        <p class="mt-1.5 text-sm text-gray-900">
                            {{ $supplierLead->updated_at?->format('M d, Y H:i') ?? '—' }}
                        </p>
                    </div>

                </div>

                <div class="px-5 pb-5">
                    <a
                        href="{{ route('admin.supplier-leads.edit', $supplierLead) }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition"
                    >
                        Edit Acquisition Details
                    </a>
                </div>

            </section>

            {{-- LINKED SUPPLIER --}}
            <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Acrovoy Supplier Profile
                    </h2>
                </div>

                <div class="p-5">

                    @if($supplierLead->supplier)

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $supplierLead->supplier->name ?? 'Supplier #' . $supplierLead->supplier_id }}
                                </p>
                                <p class="mt-1 text-xs text-emerald-700">
                                    Connected to Acrovoy
                                </p>
                                <p class="mt-2 text-xs text-gray-500">
                                    Supplier ID: {{ $supplierLead->supplier_id }}
                                </p>
                            </div>
                        </div>

                    @else

                        <div class="text-center py-3">
                            <div class="mx-auto w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
                                </svg>
                            </div>

                            <p class="mt-3 text-sm font-medium text-gray-800">
                                Not linked yet
                            </p>

                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                                This company has not been connected to a registered Acrovoy supplier profile.
                            </p>
                        </div>

                    @endif

                </div>

            </section>

            {{-- RECORD TIMESTAMPS --}}
            <section class="bg-white border border-gray-200 rounded-xl p-5">

                <h2 class="text-sm font-semibold text-gray-900">
                    Record Information
                </h2>

                <div class="mt-4 space-y-3">

                    <div>
                        <p class="text-xs text-gray-500">Created At</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $supplierLead->created_at?->format('M d, Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Updated At</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ $supplierLead->updated_at?->format('M d, Y H:i') ?? '—' }}
                        </p>
                    </div>

                </div>

            </section>

        </aside>

    </div>

</div>

@endsection