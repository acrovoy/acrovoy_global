<form
    class="drawer-form flex h-full flex-col"
    action="{{ route('supplier.company.update', 'manufacturing') }}"
    method="POST">

    @csrf

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-10">

        {{-- ================= MANUFACTURING OVERVIEW ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Manufacturing Overview
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Describe your production facilities, manufacturing experience and capabilities.
                </p>

            </div>

            <div>

                <label class="block text-sm font-semibold text-gray-900">
                    Manufacturing Description
                </label>

                <p class="mt-1 text-xs text-gray-500">
                    This information helps buyers understand your manufacturing strengths.
                </p>

                <textarea
                    name="manufacturing_description"
                    rows="8"
                    class="mt-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm resize-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('manufacturing_description', $company->profile?->manufacturing_description) }}</textarea>

            </div>

        </section>

        {{-- ================= PRODUCTION METRICS ================= --}}
        <section class="space-y-5">

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Production Metrics
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Key production information displayed in your supplier profile.
                </p>

            </div>

            <div class="grid grid-cols-2 gap-5">

                {{-- Factory Area --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Factory Area (m²)
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Total production facility area.
                    </p>

                    <input
                        type="number"
                        name="factory_area"
                        value="{{ old('factory_area', $company->profile?->factory_area) }}"
                        placeholder="5000"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                {{-- Production Lines --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Production Lines
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Number of active production lines.
                    </p>

                    <input
                        type="number"
                        name="production_lines"
                        value="{{ old('production_lines', $company->profile?->production_lines) }}"
                        placeholder="12"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                {{-- MOQ --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Minimum Order Quantity (MOQ)
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Minimum quantity accepted per order.
                    </p>

                    <input
                        type="number"
                        name="moq"
                        value="{{ old('moq', $company->profile?->moq) }}"
                        placeholder="100"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                {{-- Monthly Capacity --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-900">
                        Monthly Capacity
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Average monthly production capacity.
                    </p>

                    <input
                        type="number"
                        name="monthly_capacity"
                        value="{{ old('monthly_capacity', $company->profile?->monthly_capacity) }}"
                        placeholder="50000"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

                {{-- Lead Time --}}
                <div class="col-span-2">

                    <label class="block text-sm font-semibold text-gray-900">
                        Average Lead Time (days)
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Typical production lead time after order confirmation.
                    </p>

                    <input
                        type="number"
                        name="lead_time_days"
                        value="{{ old('lead_time_days', $company->profile?->lead_time_days) }}"
                        placeholder="30"
                        class="mt-3 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                </div>

            </div>

        </section>


        {{-- ================= MANUFACTURING CAPABILITIES ================= --}}
<section class="space-y-5">

    <div>

        <h3 class="text-base font-semibold text-gray-900">
            Manufacturing Capabilities
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Select production technologies and manufacturing capabilities your factory provides.
        </p>

    </div>

    {{-- Selected --}}
    <div>

        <label class="block text-sm font-semibold text-gray-900 mb-3">
            Selected Capabilities
        </label>

        <div
            id="selected-capabilities"
            class="flex flex-wrap gap-2 min-h-[44px] rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3">

        </div>

    </div>

    {{-- Search --}}
    <div>

        <input
            type="text"
            id="capabilitySearch"
            placeholder="Search capabilities..."
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

    </div>

    <input
        type="hidden"
        id="initial-capabilities"
        value='@json(
            $company->profile?->manufacturingCapabilities->map(function ($capability) {
                return [
                    "id" => (string) $capability->id,
                    "name" => $capability->name ?? $capability->slug,
                ];
            }) ?? []
        )'>

    <div
        id="capabilities-options"
        class="grid grid-cols-2 gap-3">

        @foreach($manufacturingCapabilities as $capability)

            @php
                $name = $capability->name ?? $capability->slug;
            @endphp

            <button
                type="button"
                class="capability-option flex items-center justify-between rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm transition hover:border-blue-400 hover:bg-blue-50"
                data-id="{{ $capability->id }}"
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
        name="manufacturing_capabilities_selected"
        id="capabilitiesSelectedInput">

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