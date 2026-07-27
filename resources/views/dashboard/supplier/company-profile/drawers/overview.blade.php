<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('supplier.company.update', 'overview') }}"
    method="POST">

    @csrf

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

        {{-- Company Name --}}
        <div>

            <label
                for="name"
                class="block text-sm font-semibold text-gray-900">

                Company Name

            </label>

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