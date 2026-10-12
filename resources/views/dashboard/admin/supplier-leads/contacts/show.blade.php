@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="min-h-screen bg-[#F7F3EA]">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- BREADCRUMBS --}}
        <div class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a
                href="{{ route('admin.supplier-leads.index') }}"
                class="transition hover:text-gray-900"
            >
                Supplier Leads
            </a>

            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 18 6-6-6-6" />
            </svg>

            <a
                href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
                class="max-w-xs truncate transition hover:text-gray-900"
            >
                {{ $supplierLead->company_name }}
            </a>

            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 18 6-6-6-6" />
            </svg>

            <span class="font-medium text-gray-900">Contact Details</span>
        </div>

        {{-- HEADER --}}
        <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-gray-200 bg-white text-xl font-semibold text-gray-600 shadow-sm">
                    {{ strtoupper(substr($contact->first_name ?? '', 0, 1) . substr($contact->last_name ?? '', 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                        {{ trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) }}
                    </h1>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $contact->job_title ?: 'Position not specified' }}
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        Contact at
                        <a
                            href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
                            class="font-medium text-gray-800 underline decoration-gray-300 underline-offset-4 hover:text-black"
                        >
                            {{ $supplierLead->company_name }}
                        </a>
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($contact->is_primary)
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                Primary Contact
                            </span>
                        @endif

                        @if($contact->is_decision_maker)
                            <span class="inline-flex items-center rounded-full bg-violet-50 px-3 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200">
                                Decision Maker
                            </span>
                        @endif

                        @if($contact->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-200">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 12H5m7 7-7-7 7-7" />
                    </svg>
                    Back to Company
                </a>

                <a
                    href="{{ route('admin.supplier-leads.contacts.edit', [$supplierLead, $contact]) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15.232 5.232 3.536 3.536M4 20l4.5-1 10.268-10.268a2.5 2.5 0 0 0-3.536-3.536L4.964 15.464 4 20Z" />
                    </svg>
                    Edit Contact
                </a>
            </div>
        </div>

       

        {{-- MAIN CONTENT --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

            {{-- CONTACT INFORMATION --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1v6m3-3h-6" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900">Contact Information</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Personal details</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">First Name</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $contact->first_name ?: '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Last Name</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $contact->last_name ?: '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Job Title</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $contact->job_title ?: '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $contact->department ?: '—' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- CONTACT CHANNELS --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900">Contact Channels</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Ways to reach this person</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Email</dt>
                        <dd class="mt-1 break-words text-sm font-medium text-gray-900">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="hover:underline">
                                    {{ $contact->email }}
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="hover:underline">
                                    {{ $contact->phone }}
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">WhatsApp</dt>
                        <dd class="mt-1 break-words text-sm font-medium text-gray-900">
                            @if($contact->whatsapp)
                                <a
                                    href="https://wa.me/{{ preg_replace('/\D+/', '', $contact->whatsapp) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:underline"
                                >
                                    {{ $contact->whatsapp }}
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Telegram</dt>
                        <dd class="mt-1 break-words text-sm font-medium text-gray-900">
                            @if($contact->telegram)
                                @php
                                    $telegramValue = ltrim(trim($contact->telegram), '@');
                                @endphp

                                <a
                                    href="https://t.me/{{ $telegramValue }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:underline"
                                >
                                    {{ $contact->telegram }}
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">LinkedIn</dt>
                        <dd class="mt-1 break-words text-sm font-medium text-gray-900">
                            @if($contact->linkedin_url)
                                <a
                                    href="{{ $contact->linkedin_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-700 hover:underline"
                                >
                                    Open LinkedIn Profile
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- CONTACT STATUS --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12l2 2 4-4m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900">Contact Status</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Role and availability</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-sm text-gray-600">Primary Contact</dt>
                        <dd>
                            @if($contact->is_primary)
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Yes</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">No</span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-sm text-gray-600">Decision Maker</dt>
                        <dd>
                            @if($contact->is_decision_maker)
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700">Yes</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">No</span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-sm text-gray-600">Status</dt>
                        <dd>
                            @if($contact->is_active)
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <p class="text-xs leading-5 text-gray-500">
                        Contact roles help identify the main company representative and the person involved in purchasing decisions.
                    </p>
                </div>
            </section>

            {{-- NOTES --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:col-span-2 xl:col-span-3">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900">Internal Notes</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Private notes for your acquisition team</p>
                    </div>
                </div>

                @if(filled($contact->notes))
                    <div class="whitespace-pre-line break-words text-sm leading-7 text-gray-700">{{ $contact->notes }}</div>
                @else
                    <p class="text-sm text-gray-400">No notes have been added for this contact.</p>
                @endif
            </section>

            {{-- COMPANY --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:col-span-2 xl:col-span-3">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M14 9v.01M14 12v.01M14 15v.01" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="font-semibold text-gray-900">Associated Company</h2>
                            <p class="mt-0.5 text-sm text-gray-500">
                                {{ $supplierLead->company_name }}
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        View Company
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 17 17 7M7 7h10v10" />
                        </svg>
                    </a>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection