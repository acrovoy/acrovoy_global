@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $isEditing ? 'Edit Contact' : 'Add Contact' }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $supplierLead->company_name }}
            </p>
        </div>

        <a href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
           class="text-sm font-medium text-gray-600 hover:text-gray-900">
            Cancel
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ $isEditing
            ? route('admin.supplier-leads.contacts.update', [$supplierLead, $contact])
            : route('admin.supplier-leads.contacts.store', $supplierLead) }}"
        method="POST"
        class="space-y-6"
    >
        @csrf

        @if ($isEditing)
            @method('PUT')
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Contact Information</h2>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                @foreach ([
                    'first_name' => ['First Name', true],
                    'last_name' => ['Last Name', false],
                    'job_title' => ['Job Title', false],
                    'department' => ['Department', false],
                    'email' => ['Email', false],
                    'phone' => ['Phone', false],
                    'whatsapp' => ['WhatsApp', false],
                    'telegram' => ['Telegram', false],
                    'linkedin_url' => ['LinkedIn URL', false],
                ] as $field => [$label, $required])

                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-medium text-gray-700">
                            {{ $label }}
                            @if ($required)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>

                        <input
                            id="{{ $field }}"
                            name="{{ $field }}"
                            type="{{ $field === 'email' ? 'email' : ($field === 'linkedin_url' ? 'url' : 'text') }}"
                            value="{{ old($field, $contact->{$field}) }}"
                            @if ($required) required @endif
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                        >
                    </div>

                @endforeach

                <div class="md:col-span-2">
                    <label for="notes" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Internal Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                    >{{ old('notes', $contact->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Contact Settings</h2>

            <div class="mt-4 space-y-4">

                @foreach ([
                    'is_primary' => 'Primary contact',
                    'is_decision_maker' => 'Decision maker',
                    'is_active' => 'Active contact',
                ] as $field => $label)

                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            type="checkbox"
                            name="{{ $field }}"
                            value="1"
                            @checked((bool) old($field, $isEditing ? $contact->{$field} : ($field === 'is_active')))
                            class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                        >

                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </label>

                @endforeach

            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.supplier-leads.show', $supplierLead) }}"
               class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700"
            >
                {{ $isEditing ? 'Save Changes' : 'Add Contact' }}
            </button>
        </div>
    </form>

</div>
@endsection