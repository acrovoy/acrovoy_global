<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('buyer.company.update', 'general') }}"
    method="POST">

    @csrf

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-10">

        {{-- ================= ABOUT COMPANY ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    About Company
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Tell buyers about your company and business.
                </p>

            </div>

            <div>

                <label
                    class="block text-sm font-semibold text-gray-900">

                    About Us

                </label>

                <p class="mt-1 text-xs text-gray-500">
                    This information appears on your company profile.
                </p>

                <textarea
                    name="about_us_description"
                    rows="7"
                    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm resize-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('about_us_description', $company->profile?->about_us_description) }}</textarea>

            </div>

        </section>

        {{-- ================= BUSINESS METRICS ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Business Metrics
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Basic company information used in your supplier profile.
                </p>

            </div>

            <div class="grid grid-cols-2 gap-5">

                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Founded
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Year company was established.
                    </p>

                    <input
                        type="number"
                        name="founded_year"
                        value="{{ old('founded_year', $company->profile?->founded_year) }}"
                        placeholder="2015"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Employees
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Total number of employees.
                    </p>

                    <input
                        type="number"
                        name="total_employees"
                        value="{{ old('total_employees', $company->profile?->total_employees) }}"
                        placeholder="150"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                

               

            </div>

        </section>

        

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

