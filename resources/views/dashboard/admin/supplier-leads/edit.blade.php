@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col max-w-4xl">

    <x-alerts />

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">

        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Edit Factory / Supplier
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Update supplier information and acquisition progress.
            </p>

            @if($supplierLead->supplier_id)
                <p class="mt-2 text-xs text-emerald-700">
                    Linked to an existing Acrovoy supplier profile
                    (#{{ $supplierLead->supplier_id }}).
                </p>
            @endif
        </div>

        <a
            href="{{ route('admin.supplier-leads.index') }}"
            class="text-sm text-gray-600 hover:text-gray-900 transition"
        >
            Back to list
        </a>

    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form
            action="{{ route('admin.supplier-leads.update', $supplierLead) }}"
            method="POST"
            class="p-6 flex flex-col gap-6"
        >
            @csrf
            @method('PUT')

            {{-- ============================================================
                 COMPANY INFORMATION
            ============================================================ --}}

            <div class="border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Company Information
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Basic information about the factory, brand or supplier.
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Company Name --}}
                    <div class="md:col-span-2">

                        <label for="company_name" class="block text-[13px] font-semibold text-gray-800">
                            Company Name
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="company_name"
                            id="company_name"
                            value="{{ old('company_name', $supplierLead->company_name) }}"
                            maxlength="255"
                            required
                            autofocus
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('company_name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Legal Name --}}
                    <div>

                        <label for="legal_name" class="block text-[13px] font-semibold text-gray-800">
                            Legal Company Name
                        </label>

                        <input
                            type="text"
                            name="legal_name"
                            id="legal_name"
                            value="{{ old('legal_name', $supplierLead->legal_name) }}"
                            maxlength="255"
                            placeholder="Registered legal name"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('legal_name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Company Type --}}
                    <div>

                        <label for="company_type" class="block text-[13px] font-semibold text-gray-800">
                            Company Type
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="company_type"
                            id="company_type"
                            required
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                            <option value="">Select company type</option>

                            <option value="manufacturer" @selected(old('company_type', $supplierLead->company_type) === 'manufacturer')>
                                Manufacturer / Factory
                            </option>

                            <option value="wholesaler" @selected(old('company_type', $supplierLead->company_type) === 'wholesaler')>
                                Wholesaler
                            </option>

                            <option value="distributor" @selected(old('company_type', $supplierLead->company_type) === 'distributor')>
                                Distributor
                            </option>

                            <option value="trading_company" @selected(old('company_type', $supplierLead->company_type) === 'trading_company')>
                                Trading Company
                            </option>

                            <option value="brand" @selected(old('company_type', $supplierLead->company_type) === 'brand')>
                                Brand
                            </option>

                            <option value="other" @selected(old('company_type', $supplierLead->company_type) === 'other')>
                                Other
                            </option>
                        </select>

                        @error('company_type')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>


                    <div>
    <label for="linkedin_url" class="mb-1.5 block text-sm font-medium text-gray-700">
        Company LinkedIn Page
    </label>

    <input
        type="url"
        id="linkedin_url"
        name="linkedin_url"
        value="{{ old('linkedin_url', $supplierLead->linkedin_url ?? '') }}"
        placeholder="https://www.linkedin.com/company/example"
        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
    >

    @error('linkedin_url')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>


                    {{-- Website --}}
                    <div>

                        <label for="website" class="block text-[13px] font-semibold text-gray-800">
                            Website
                        </label>

                        <input
                            type="url"
                            name="website"
                            id="website"
                            value="{{ old('website', $supplierLead->website) }}"
                            maxlength="255"
                            placeholder="https://example.com"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('website')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Email --}}
                    <div>

                        <label for="email" class="block text-[13px] font-semibold text-gray-800">
                            General Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $supplierLead->email) }}"
                            maxlength="255"
                            placeholder="info@example.com"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Phone --}}
                    <div>

                        <label for="phone" class="block text-[13px] font-semibold text-gray-800">
                            General Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            value="{{ old('phone', $supplierLead->phone) }}"
                            maxlength="50"
                            placeholder="+33 ..."
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('phone')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>

            {{-- ============================================================
                 LOCATION
            ============================================================ --}}

            <div class="border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Company Location
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Location of the company or manufacturing facility.
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Country --}}
                    <div>

                        <label for="country_id" class="block text-[13px] font-semibold text-gray-800">
                            Country
                        </label>

                        <select
                            name="country_id"
                            id="country_id"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                            <option value="">Select country</option>

                            @foreach($countries as $country)
                                <option
                                    value="{{ $country->id }}"
                                    @selected((string) old('country_id', $supplierLead->country_id) === (string) $country->id)
                                >
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('country_id')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- City --}}
                    <div>

                        <label for="city" class="block text-[13px] font-semibold text-gray-800">
                            City
                        </label>

                        <input
                            type="text"
                            name="city"
                            id="city"
                            value="{{ old('city', $supplierLead->city) }}"
                            maxlength="255"
                            placeholder="City"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('city')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">

                        <label for="address" class="block text-[13px] font-semibold text-gray-800">
                            Address
                        </label>

                        <input
                            type="text"
                            name="address"
                            id="address"
                            value="{{ old('address', $supplierLead->address) }}"
                            maxlength="255"
                            placeholder="Street, building, industrial park..."
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('address')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>

            {{-- ============================================================
                 ACQUISITION MANAGEMENT
            ============================================================ --}}

            <div class="border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Acquisition Management
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Track outreach progress and assign responsibility.
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Status --}}
                    <div>

                        <label for="acquisition_status" class="block text-[13px] font-semibold text-gray-800">
                            Acquisition Status
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="acquisition_status"
                            id="acquisition_status"
                            required
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                            @foreach($statuses as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('acquisition_status', $supplierLead->acquisition_status) === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('acquisition_status')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Priority --}}
                    <div>

                        <label for="priority" class="block text-[13px] font-semibold text-gray-800">
                            Priority
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            required
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                            @foreach($priorities as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('priority', $supplierLead->priority) === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('priority')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Source --}}
                    <div>

                        <label for="source" class="block text-[13px] font-semibold text-gray-800">
                            Lead Source
                        </label>

                        <input
                            type="text"
                            name="source"
                            id="source"
                            value="{{ old('source', $supplierLead->source) }}"
                            maxlength="255"
                            placeholder="e.g. Google, trade fair, referral"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >

                        @error('source')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Assigned User --}}
                    <div>

                        <label for="assigned_to" class="block text-[13px] font-semibold text-gray-800">
                            Assigned To
                        </label>

                        <select
                            name="assigned_to"
                            id="assigned_to"
                            class="mt-1.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                            <option value="">Unassigned</option>

                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    @selected((string) old('assigned_to', $supplierLead->assigned_to) === (string) $user->id)
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('assigned_to')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>

            {{-- ============================================================
                 PRODUCT CATEGORIES
            ============================================================ --}}

            @php
                $selectedCategories = collect(
                    old('categories', $supplierLead->categories->pluck('id')->all())
                )->map(fn ($id) => (string) $id)->all();
            @endphp

            <div class="border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Product Categories
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Select the product categories relevant to this supplier.
                    </p>
                </div>

                <div class="p-5">

                    @if($categories->isNotEmpty())

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            @foreach($categories as $category)

                                @php
                                    $categoryName = $category->translations
                                        ->firstWhere('language_code', app()->getLocale())
                                        ?->name
                                        ?? $category->name
                                        ?? ('Category #' . $category->id);
                                @endphp

                                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 bg-white cursor-pointer transition hover:bg-gray-50">

                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ $category->id }}"
                                        class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                                        @checked(in_array((string) $category->id, $selectedCategories, true))
                                    >

                                    <span class="text-sm text-gray-800">
                                        {{ $categoryName }}
                                    </span>

                                </label>

                            @endforeach

                        </div>

                    @else

                        <p class="text-sm text-gray-500">
                            No selectable product categories are available.
                        </p>

                    @endif

                    @error('categories')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    @error('categories.*')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            </div>

            {{-- ============================================================
                 INTERNAL NOTES
            ============================================================ --}}

            <div>

                <label for="notes" class="block text-[13px] font-semibold text-gray-800">
                    Internal Notes
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Internal acquisition information. Not visible to suppliers.
                </p>

                <textarea
                    name="notes"
                    id="notes"
                    rows="5"
                    class="mt-2 w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder:text-gray-400 outline-none transition focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                    placeholder="Add notes about the company, outreach history or next steps..."
                >{{ old('notes', $supplierLead->notes) }}</textarea>

                @error('notes')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- ============================================================
                 LINKED SUPPLIER PROFILE
            ============================================================ --}}

            @if($supplierLead->supplier)
                <div class="border border-emerald-200 rounded-xl p-5 bg-emerald-50">

                    <h3 class="font-semibold text-emerald-900 text-sm">
                        Linked Acrovoy Supplier
                    </h3>

                    <p class="text-sm text-emerald-800 mt-1">
                        This prospect is linked to an existing supplier record:
                        <span class="font-semibold">
                            {{ $supplierLead->supplier->name ?? 'Supplier #' . $supplierLead->supplier_id }}
                        </span>
                    </p>

                    <p class="text-xs text-emerald-700 mt-2">
                        Supplier linking is managed separately from the prospect details.
                    </p>

                </div>
            @endif

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">

                <a
                    href="{{ route('admin.supplier-leads.index') }}"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection