<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('supplier.company.update', 'address') }}"
    method="POST">

    @csrf

    @php
        $address = $company->primaryAddress;
    @endphp

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8 min-h-0">

        {{-- Country --}}
        <div>

            <label
                for="country_id"
                class="block text-sm font-semibold text-gray-900">

                Country

            </label>

            <p class="mt-1 text-xs text-gray-500">
                Select the country where your business is located.
            </p>

            <select
                id="country_id"
                name="country_id"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                <option value="">Select country</option>

                @foreach($countries as $country)

                    <option
                        value="{{ $country->id }}"
                        @selected(old('country_id', $address?->country_id) == $country->id)>

                        {{ $country->name }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- State / Province --}}
        <div>

            <label
                for="state"
                class="block text-sm font-semibold text-gray-900">

                State / Province

            </label>

            <p class="mt-1 text-xs text-gray-500">
                State, province or region.
            </p>

            <input
                id="state"
                type="text"
                name="state"
                value="{{ old('state', $address?->state) }}"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        </div>

        {{-- City --}}
        <div>

            <label
                for="city"
                class="block text-sm font-semibold text-gray-900">

                City

            </label>

            <p class="mt-1 text-xs text-gray-500">
                City where your business is located.
            </p>

            <input
                id="city"
                type="text"
                name="city"
                value="{{ old('city', $address?->city) }}"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        </div>

        {{-- Postal Code --}}
        <div>

            <label
                for="postal_code"
                class="block text-sm font-semibold text-gray-900">

                Postal Code

            </label>

            <p class="mt-1 text-xs text-gray-500">
                ZIP or postal code.
            </p>

            <input
                id="postal_code"
                type="text"
                name="postal_code"
                value="{{ old('postal_code', $address?->postal_code) }}"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        </div>

        {{-- Street Address --}}
        <div>

            <label
                for="address_line_1"
                class="block text-sm font-semibold text-gray-900">

                Street Address

            </label>

            <p class="mt-1 text-xs text-gray-500">
                Street name, house number or building.
            </p>

            <textarea
                id="address_line_1"
                name="address_line_1"
                rows="3"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm resize-none transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('address_line1', $address?->address_line_1) }}</textarea>

        </div>

        {{-- Building / Office --}}
        <div>

            <label
                for="address_line_2"
                class="block text-sm font-semibold text-gray-900">

                Building / Office

            </label>

            <p class="mt-1 text-xs text-gray-500">
                Apartment, office, suite, floor or other location details.
            </p>

            <input
                id="address_line_2"
                type="text"
                name="address_line_2"
                value="{{ old('address_line_2', $address?->address_line_2) }}"
                class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        </div>

        {{-- Latitude --}}
<div>

    <label
        for="latitude"
        class="block text-sm font-semibold text-gray-900">

        Latitude

    </label>

    <p class="mt-1 text-xs text-gray-500">
        Enter manually if automatic location detection fails.
    </p>

    <input
        id="latitude"
        type="text"
        name="latitude"
        value="{{ old('latitude') }}"
        placeholder="Example: 46.481259"
        class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm
               focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

</div>


{{-- Longitude --}}
<div>

    <label
        for="longitude"
        class="block text-sm font-semibold text-gray-900">

        Longitude

    </label>

    <p class="mt-1 text-xs text-gray-500">
        Enter manually if automatic location detection fails.
    </p>

    <input
        id="longitude"
        type="text"
        name="longitude"
        value="{{ old('longitude')}}"
        placeholder="Example: 30.745252"
        class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm
               focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

</div>

    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 bg-gray-50 px-6 py-5">

        <div class="flex items-center justify-between">

            <button
                type="button"
                onclick="closeDrawer()"
                class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-100">

                Cancel

            </button>

            <button
                type="submit"
                class="inline-flex items-center rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-black">

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