<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('supplier.company.update', 'general') }}"
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

                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Annual Export Revenue (USD)
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Estimated annual export revenue.
                    </p>

                    <input
                        type="number"
                        name="annual_export_revenue"
                        value="{{ old('annual_export_revenue', $company->profile?->annual_export_revenue) }}"
                        placeholder="650000"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Registration Capital (USD)
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Registered company capital.
                    </p>

                    <input
                        type="number"
                        name="registration_capital"
                        value="{{ old('registration_capital', $company->profile?->registration_capital) }}"
                        placeholder="1000000"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

            </div>

        </section>

        {{-- ================= EXPORT MARKETS ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Export Markets
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Select countries and regions where your products are exported.
                </p>

            </div>

            {{-- Selected --}}
            <div>

                <label class="block text-sm font-semibold text-gray-900 mb-3">
                    Selected Markets
                </label>

                <div
                    id="selected-export-markets"
                    class="flex flex-wrap gap-2 min-h-[44px] rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3">

                </div>

            </div>

            {{-- Search --}}
            <div>

                <input
                    type="text"
                    id="exportMarketSearch"
                    placeholder="Search export markets..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            </div>

            <input
    type="hidden"
    id="initial-export-markets"
    value='@json(
        $company->exportMarkets->map(function ($market) {
            return [
                "id" => (string) $market->id,
                "name" => $market->translation?->name ?? $market->slug,
            ];
        })
    )'>

            {{-- Options --}}
            <div
                id="export-markets-options"
                class="grid grid-cols-2 gap-3">

                @foreach($exportMarkets as $market)

                    @php
                        $name = $market->translation?->name ?? $market->slug;
                    @endphp

                    <button
                        type="button"
                        class="export-market-option flex items-center justify-between rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm transition hover:border-blue-400 hover:bg-blue-50"
                        data-id="{{ $market->id }}"
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
                name="export_markets_selected"
                id="exportMarketsSelectedInput">

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

