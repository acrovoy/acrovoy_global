<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('supplier.company.update', 'contacts') }}"
    method="POST">

    @csrf

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-10">

        {{-- ================= CONTACT DETAILS ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Contact Details
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    These details allow buyers to contact your company directly.
                </p>

            </div>

            {{-- Email --}}
            <div>

                <label
                    for="email"
                    class="block text-sm font-semibold text-gray-900">

                    Company Email

                </label>

                <p class="mt-1 text-xs text-gray-500">
                    Primary email address for customer inquiries.
                </p>

                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    value="{{ old('email', $company->email) }}"
                    placeholder="company@example.com"
                    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                           shadow-sm transition
                           focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            </div>

            {{-- Phone --}}
            <div>

                <label
                    for="phone"
                    class="block text-sm font-semibold text-gray-900">

                    Company Phone

                </label>

                <p class="mt-1 text-xs text-gray-500">
                    Include country code whenever possible.
                </p>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone', $company->phone) }}"
                    placeholder="+1 234 567 890"
                    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                           shadow-sm transition
                           focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            </div>

            {{-- Address --}}
            <div>

                <label
                    for="address"
                    class="block text-sm font-semibold text-gray-900">

                    Company Address

                </label>

                <p class="mt-1 text-xs text-gray-500">
                    Enter your registered business address.
                </p>

                <textarea
                    id="address"
                    name="address"
                    rows="5"
                    placeholder="Street, City, State, ZIP, Country"
                    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm
                           shadow-sm resize-none transition
                           focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('address', $company->address) }}</textarea>

            </div>

        </section>

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