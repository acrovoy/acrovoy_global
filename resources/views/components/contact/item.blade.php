@props([
    'contact',
])

<a
    href="{{ $contact->url }}"
    @if(Str::startsWith($contact->url, ['http://', 'https://']))
        target="_blank"
        rel="noopener noreferrer"
    @endif
    class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition"
>

    <x-contact.icon :name="$contact->icon" />

    <div class="flex-1">

        <div class="text-xs text-gray-500">

            {{ $contact->type_label }}

            @if($contact->label)
                · {{ $contact->label }}
            @endif

        </div>

        <div class="font-medium">

            {{ $contact->display_value }}

        </div>

        @if($contact->show_in_profile)
    <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">

        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 13l4 4L19 7"/>
        </svg>

        Undername contact

    </span>
@endif

    </div>

</a>