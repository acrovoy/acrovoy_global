<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('buyer.company.update', 'overview') }}"
    method="POST">

    @csrf

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

        {{-- Company Name --}}
        <div>

            <div class="flex items-center gap-2">

    <label
        for="name"
        class="block text-sm font-semibold text-gray-900">

        @if($is_personal)
            Name
        @else
            Company Name
        @endif

    </label>


    @if($is_personal)

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    Personal Name
                </div>

                <div class="text-gray-200 text-sm normal-case">
                    Enter your first and last name as it should appear on your supplier profile.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1 normal-case">
                    <li>Use your real first and last name.</li>
                    <li>Do not enter a company name in this field.</li>
                    <li>This name will represent you as a supplier.</li>
                </ul>

                <div class="text-gray-400 text-xs border-t border-gray-700 pt-2 normal-case">
                    Recommendation: Use the name of the person responsible for business communication.
                </div>

            </div>
        </x-help-tooltip>

    @endif

</div>

            <p class="mt-1 text-xs text-gray-500">
                This name is displayed throughout the marketplace.
            </p>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $company->name) }}"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                       shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        </div>

        {{-- ================= BUSINESS TYPE ================= --}}
<div>

    <label class="block text-sm font-semibold text-gray-900">
    Business Type
</label>

<p class="mt-1 text-xs text-gray-500">
    Select one or more business types that describe your business activities.
</p>

    {{-- Selected --}}
    <div class="mt-4">

        <div
            id="selected-business-types"
            class="flex flex-wrap gap-2 min-h-[44px] rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3">

        </div>

    </div>

    {{-- Search --}}
    <div class="mt-4">

        <input
            type="text"
            id="businessTypeSearch"
            placeholder="Search business types..."
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm
                   focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

    </div>

    <input
        type="hidden"
        id="initial-business-types"
        value='@json(
            $company->businessTypes->map(function ($type){
                return [
                    "id" => (string)$type->id,
                    "name" => $type->translation?->name ?? $type->slug,
                ];
            })
        )'>

    {{-- Options --}}
    <div
        class="grid grid-cols-2 gap-3 mt-4">

        @foreach($businessTypes as $type)

            @php
                $name = $type->translation?->name ?? $type->slug;
            @endphp

            <button
                type="button"
                class="business-type-option flex items-center justify-between rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm transition hover:border-blue-400 hover:bg-blue-50"
                data-id="{{ $type->id }}"
                data-name="{{ $name }}">

                <span>{{ $name }}</span>

                <svg
                    class="w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 5v14m-7-7h14"/>

                </svg>

            </button>

        @endforeach

    </div>

    <input
        type="hidden"
        name="business_types_selected"
        id="businessTypesSelectedInput">

</div>


        {{-- Short Description --}}
        <div>

            <label
                for="short_description"
                class="block text-sm font-semibold text-gray-900">

                Short Description

            </label>

            <p class="mt-1 text-xs text-gray-500">
                A short introduction shown in listings and search results.
            </p>

            <textarea
                id="short_description"
                name="short_description"
                rows="4"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                       shadow-sm resize-none transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('short_description', $company->short_description) }}</textarea>

        </div>

        {{-- Company Description --}}
        <div>

            <label
                for="description"
                class="block text-sm font-semibold text-gray-900">

                Company Description

            </label>

            <p class="mt-1 text-xs text-gray-500">
                Tell buyers about your company, experience and manufacturing capabilities.
            </p>

            <textarea
                id="description"
                name="description"
                rows="10"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                       shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('description', $company->description) }}</textarea>

        </div>


        {{-- Country --}}
        <div>

            <label
                for="country"
                class="block text-sm font-semibold text-gray-900">

                Registration Country

            </label>

            <p class="mt-1 text-xs text-gray-500">
                Let buyer know the region where the company from
            </p>

            <select
    name="country_id"
    id="country_id"
    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
           shadow-sm transition
           focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

    <option value="">Select country</option>

    @foreach($countries as $country)
        <option
            value="{{ $country->id }}"
            @selected(old('country_id', $company->country_id) == $country->id)>

            {{ $country->name }}

        </option>
    @endforeach

</select>

        </div>

    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 bg-gray-50 px-6 py-5">

        <div class="flex items-center justify-between">

            <button
                type="button"
                onclick="closeDrawer()"
                class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5
                       text-sm font-medium text-gray-700 shadow-sm transition
                       hover:bg-gray-100">

                Cancel

            </button>

            <button
                type="submit"
                class="inline-flex items-center rounded-xl bg-gray-900 px-6 py-2.5
                       text-sm font-semibold text-white shadow-lg transition
                       hover:bg-black">

                <svg
                    class="mr-2 h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 13l4 4L19 7"/>

                </svg>

                Save Changes

            </button>

        </div>

    </div>

</form>