@php
    $contacts = $contacts ?? collect();
@endphp

@props([
    'contacts',
    'title' => 'Contact Information',
    'description' => 'Primary business contact details.',
    
    'ownerType' => null,
    'ownerId' => null,
    'editable' => false,

    'public' => false,
])

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                {{ $title }}
            </h2>

            @if($description)
                <p class="mt-1 text-sm text-gray-500">
                    {{ $description }}
                </p>
            @endif

        </div>

        @if($editable && $ownerType && $ownerId)

<button
    type="button"
    onclick="openDrawer({
        title: 'Manage Contacts',
        description: 'Add, edit and remove contact methods.',
        url: '{{ route('contacts.drawer', [
            'type' => $ownerType,
            'id' => $ownerId,
        ]) }}'
    })"
    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-gray-300">

    <svg
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A4 4 0 019.707 15.707L7 16l.293-2.707A4 4 0 018.464 10.88L15.232 5.232z"/>

    </svg>

    Edit

</button>

@endif

    </div>

    {{-- Contacts --}}
    @if($contacts->isNotEmpty())

        <div class="divide-y divide-gray-100">

            @foreach($contacts->sortBy('sort_order') as $contact)

                <x-contact.item 
    :contact="$contact"
    :public="$public"
/>

            @endforeach

        </div>

    @else

        <div class="px-6 py-10 text-center text-sm text-gray-500">
            No contact information available.
        </div>

    @endif

</div>