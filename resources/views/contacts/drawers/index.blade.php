

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

        {{-- Contacts --}}
        <section class="space-y-5">


            <button
                type="button"
                id="add-contact-btn"
                class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-gray-300 bg-white px-5 py-4 text-sm font-medium text-gray-700 transition hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 5v14m-7-7h14" />

                </svg>

                Add Contact

            </button>

            <div
                id="contact-create-form"
                class="hidden rounded-2xl border border-gray-200 bg-gray-50 p-6">

                <div class="space-y-8">

                    {{-- Header --}}
                    <div>

                        <h3 id="contact-form-title"
                        class="text-base font-semibold text-gray-900">
                            Add Contact
                        </h3>

                        <p 
                        id="contact-form-description"
                        class="mt-1 text-sm text-gray-500">
                            Add a new way for customers to contact this company.
                        </p>

                    </div>

                    <form
                        id="contact-form"
                        class="drawer-form flex h-full flex-col gap-6"
                        action="{{ route('contacts.store') }}"
                        method="POST">

                        @csrf


                        <input
                            type="hidden"
                            name="contactable_type"
                            value="{{ get_class($owner) }}">

                        <input
                            type="hidden"
                            name="contactable_id"
                            value="{{ $owner->id }}">

                        {{-- Contact Type --}}
                        <div>

                            <label class="block text-sm font-semibold text-gray-900">
                                Contact Type
                            </label>

                            <p class="mt-1 text-xs text-gray-500">
                                Select the type of contact information.
                            </p>

                            <select
                                id="contact-type"
                                name="type"
                                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                                <option value="">Select type...</option>

                                @foreach(\App\Domain\Contact\Enums\ContactType::cases() as $type)

                                <option value="{{ $type->value }}">
                                    {{ $type->label() }}
                                </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Value --}}
                        <div>

                            <label class="block text-sm font-semibold text-gray-900">
                                Contact Value
                            </label>

                            <p class="mt-1 text-xs text-gray-500">
                                Enter phone number, email, website or username.
                            </p>

                            <input
                                id="contact-value"
                                type="text"
                                name="value"
                                placeholder="Select contact type first"
                                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                        </div>

                        {{-- Label --}}
                        <div>

                            <label class="block text-sm font-semibold text-gray-900">
                                Label
                            </label>

                            <p class="mt-1 text-xs text-gray-500">
                                Optional label like Sales, Office, Support or WhatsApp.
                            </p>

                            <input
                                type="hidden"
                                id="contact-defaults"
                                value='@json(\App\Domain\Contact\Enums\ContactType::frontend())'>

                            <input
                                type="text"
                                id="contact-label"
                                name="label"
                                placeholder="Sales department"
                                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                        </div>

                        {{-- Options --}}
                        <div class="space-y-4">

                            <label class="flex items-start gap-3">

                                <input
                                    type="checkbox"
                                    name="is_primary"
                                    value="1"
                                    class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                <div>

                                    <div class="text-sm font-medium text-gray-900">
                                        Primary contact
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        This contact will be displayed first.
                                    </div>

                                </div>

                            </label>

                            <label class="flex items-start gap-3">

                                <input
                                    type="checkbox"
                                    name="is_public"
                                    value="1"
                                    checked
                                    class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                <div>

                                    <div class="text-sm font-medium text-gray-900">
                                        Public
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        Visible to buyers on the company profile.
                                    </div>

                                </div>

                            </label>

                            <label class="flex items-start gap-3">

    <input
        id="show-in-profile"
        type="checkbox"
        name="show_in_profile"
        value="1"
        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

    <div>

        <div class="text-sm font-medium text-gray-900">
            Show under team member
        </div>

        <div class="text-xs text-gray-500">
            Display this contact beneath the assigned team member on the public company profile.
        </div>

    </div>

</label>


                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">

                            <button
                                type="button"
                                id="cancel-add-contact"
                                class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">

                                Cancel

                            </button>

                            <button
                                type="submit"
                                id="save-contact"
                                class="inline-flex items-center rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-black">

                                Add Contact

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Contact Methods
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Manage phone numbers, email addresses, messengers and social networks used by this profile.
                </p>

            </div>

            @forelse($contacts as $contact)

            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div class="flex items-start gap-4 flex-1">

                        <x-contact.icon
                            :name="$contact->icon"
                            class="shrink-0" />

                        <div class="min-w-0 flex-1">

                            <div class="flex items-center gap-2 flex-wrap">

                                <h4 class="font-semibold text-gray-900">

                                    {{ $contact->type_label }}

                                </h4>

                                @if($contact->is_primary)

                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                    Primary
                                </span>

                                @endif

                                @unless($contact->is_public)

                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                    Private
                                </span>

                                @endunless

                            </div>

                            @if($contact->label)

                            <p class="mt-1 text-xs text-gray-500">

                                {{ $contact->label }}

                            </p>

                            @endif

                            <a
                                href="{{ $contact->url }}"
                                class="mt-2 inline-block break-all text-sm font-medium text-blue-600 hover:text-blue-700">

                                {{ $contact->display_value }}

                            </a>

                        </div>

                    </div>

                    <div class="flex items-center gap-2">

                        <button
                            type="button"
                            class="edit-contact rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-50 hover:text-blue-600"
                            data-url="{{ route('contacts.update', $contact) }}"
                            data-type="{{ $contact->type->value }}"
                            data-value="{{ $contact->value }}"
                            data-label="{{ $contact->label }}"
                            data-primary="{{ $contact->is_primary ? 1 : 0 }}"
                            data-public="{{ $contact->is_public ? 1 : 0 }}"
                            title="Edit">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A4 4 0 019.707 15.707L7 16l.293-2.707A4 4 0 018.464 10.88L15.232 5.232z" />

                            </svg>

                        </button>

                        <button
                            type="button"
                            class="delete-contact rounded-lg border border-red-200 p-2 text-red-500 transition hover:bg-red-50"
                            data-url="{{ route('contacts.destroy', $contact) }}"
                            title="Delete">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 7L5 7M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" />

                            </svg>

                        </button>



                    </div>

                </div>

            </div>

            @empty

            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center">

                <div class="text-sm font-medium text-gray-900">
                    No contacts yet
                </div>

                <p class="mt-2 text-sm text-gray-500">
                    Add your first contact method so buyers can easily reach you.
                </p>

            </div>

            @endforelse




        </section>

    </div>

   {{-- Footer --}}
<div class="border-t border-gray-200 bg-gray-50 px-6 py-5">

    

</div>

