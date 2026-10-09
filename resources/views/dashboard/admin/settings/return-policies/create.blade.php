@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-4xl">

    <x-alerts />

    {{-- ============================================================
        HEADER
    ============================================================ --}}

    <div>

        <h1 class="text-2xl font-semibold text-gray-900">
            Add Return Policy
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Create a return and refund policy that can be assigned to products and suppliers.
        </p>

    </div>


    {{-- ============================================================
        FORM
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form
            action="{{ route('admin.settings.return-policies.store') }}"
            method="POST"
            class="p-6 flex flex-col gap-6"
        >

            @csrf


            {{-- ====================================================
                BASIC INFORMATION
            ===================================================== --}}

            <div>

                <h2 class="text-sm font-semibold text-gray-900">
                    Basic Information
                </h2>

                <p class="text-[11px] text-gray-400 mt-1">
                    Define the policy name, internal code and default status.
                </p>

            </div>


            {{-- NAME --}}

            <div>

                <label
                    for="name"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Name
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Internal name used to identify this return policy.
                </p>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-50
                        text-sm
                        text-gray-900
                        outline-none
                        transition
                        focus:bg-white
                        focus:border-gray-400
                        focus:ring-2
                        focus:ring-gray-100
                    "
                    placeholder="e.g. Standard Returns"
                >

                @error('name')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- CODE --}}

            <div>

                <label
                    for="code"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Code
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Unique internal code used to identify the return policy.
                </p>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code') }}"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-50
                        text-sm
                        font-mono
                        text-gray-900
                        outline-none
                        transition
                        focus:bg-white
                        focus:border-gray-400
                        focus:ring-2
                        focus:ring-gray-100
                    "
                    placeholder="e.g. standard_returns"
                >

                @error('code')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- DEFAULT --}}

            <div>

                <label
                    for="is_default"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Default Policy
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    The default policy is automatically selected when no specific return policy is assigned.
                </p>

                <select
                    name="is_default"
                    id="is_default"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-50
                        text-sm
                        text-gray-900
                        outline-none
                        transition
                        focus:bg-white
                        focus:border-gray-400
                        focus:ring-2
                        focus:ring-gray-100
                    "
                >

                    <option
                        value="0"
                        @selected(old('is_default', '0') == '0')
                    >
                        No
                    </option>

                    <option
                        value="1"
                        @selected(old('is_default') == '1')
                    >
                        Yes
                    </option>

                </select>

                @error('is_default')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- STATUS --}}

            <div>

                <label
                    for="is_active"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Status
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Inactive policies will not be available for new assignments.
                </p>

                <select
                    name="is_active"
                    id="is_active"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-50
                        text-sm
                        text-gray-900
                        outline-none
                        transition
                        focus:bg-white
                        focus:border-gray-400
                        focus:ring-2
                        focus:ring-gray-100
                    "
                >

                    <option
                        value="1"
                        @selected(old('is_active', '1') == '1')
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        @selected(old('is_active') === '0')
                    >
                        Inactive
                    </option>

                </select>

                @error('is_active')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                RETURN TERMS
            ===================================================== --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-5">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Return Terms
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Define the main conditions under which a product can be returned.
                    </p>

                </div>


                {{-- RETURN WINDOW --}}

                <div>

                    <label
                        for="return_window_days"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Return Window
                    </label>

                    <p class="mt-1 text-[11px] text-gray-400">
                        Number of days after delivery during which a return can be requested. Leave empty if there is no fixed window.
                    </p>

                    <div class="flex items-center gap-3 mt-2">

                        <input
                            type="number"
                            name="return_window_days"
                            id="return_window_days"
                            value="{{ old('return_window_days') }}"
                            min="0"
                            class="
                                w-full
                                h-10
                                px-3
                                rounded-lg
                                border border-gray-200
                                bg-gray-50
                                text-sm
                                text-gray-900
                                outline-none
                                transition
                                focus:bg-white
                                focus:border-gray-400
                                focus:ring-2
                                focus:ring-gray-100
                            "
                            placeholder="e.g. 30"
                        >

                        <span
                            class="
                                inline-flex
                                items-center
                                justify-center
                                h-10
                                px-3
                                rounded-lg
                                bg-gray-100
                                border
                                border-gray-200
                                text-xs
                                font-medium
                                text-gray-500
                            "
                        >
                            Days
                        </span>

                    </div>

                    @error('return_window_days')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- SHIPPING PAYER --}}

                <div class="mt-5">

                    <label
                        for="return_shipping_payer"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Return Shipping Paid By
                    </label>

                    <p class="mt-1 text-[11px] text-gray-400">
                        Defines who is responsible for return shipping costs.
                    </p>

                    <select
                        name="return_shipping_payer"
                        id="return_shipping_payer"
                        class="
                            mt-2
                            w-full
                            h-10
                            px-3
                            rounded-lg
                            border border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                    >

                        <option value="">
                            Not specified
                        </option>

                        <option
                            value="buyer"
                            @selected(old('return_shipping_payer') === 'buyer')
                        >
                            Buyer
                        </option>

                        <option
                            value="supplier"
                            @selected(old('return_shipping_payer') === 'supplier')
                        >
                            Supplier
                        </option>

                        <option
                            value="depends_on_reason"
                            @selected(old('return_shipping_payer') === 'depends_on_reason')
                        >
                            Depends on reason
                        </option>

                    </select>

                    @error('return_shipping_payer')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- RESTOCKING FEE --}}

                <div class="mt-5">

                    <label
                        for="restocking_fee_enabled"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Restocking Fee
                    </label>

                    <p class="mt-1 text-[11px] text-gray-400">
                        Enable a fee that can be deducted from the refund when a product is returned.
                    </p>

                    <select
                        name="restocking_fee_enabled"
                        id="restocking_fee_enabled"
                        class="
                            mt-2
                            w-full
                            h-10
                            px-3
                            rounded-lg
                            border border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                    >

                        <option
                            value="0"
                            @selected(old('restocking_fee_enabled', '0') == '0')
                        >
                            Disabled
                        </option>

                        <option
                            value="1"
                            @selected(old('restocking_fee_enabled') == '1')
                        >
                            Enabled
                        </option>

                    </select>

                    @error('restocking_fee_enabled')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- RESTOCKING FEE PERCENT --}}

                <div class="mt-5">

                    <label
                        for="restocking_fee_percent"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Restocking Fee Percentage
                    </label>

                    <p class="mt-1 text-[11px] text-gray-400">
                        Percentage of the refund that may be charged as a restocking fee.
                    </p>

                    <div class="flex items-center gap-3 mt-2">

                        <input
                            type="number"
                            name="restocking_fee_percent"
                            id="restocking_fee_percent"
                            value="{{ old('restocking_fee_percent') }}"
                            min="0"
                            max="100"
                            step="0.01"
                            class="
                                w-full
                                h-10
                                px-3
                                rounded-lg
                                border border-gray-200
                                bg-gray-50
                                text-sm
                                text-gray-900
                                outline-none
                                transition
                                focus:bg-white
                                focus:border-gray-400
                                focus:ring-2
                                focus:ring-gray-100
                            "
                            placeholder="e.g. 20"
                        >

                        <span
                            class="
                                inline-flex
                                items-center
                                justify-center
                                h-10
                                w-10
                                rounded-lg
                                bg-gray-100
                                border
                                border-gray-200
                                text-sm
                                font-medium
                                text-gray-500
                            "
                        >
                            %
                        </span>

                    </div>

                    @error('restocking_fee_percent')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- CUSTOM PRODUCTS --}}

                <div class="mt-5">

                    <label
                        for="custom_products_returnable"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Custom Products
                    </label>

                    <p class="mt-1 text-[11px] text-gray-400">
                        Defines whether customized or made-to-order products can be returned under this policy.
                    </p>

                    <select
                        name="custom_products_returnable"
                        id="custom_products_returnable"
                        class="
                            mt-2
                            w-full
                            h-10
                            px-3
                            rounded-lg
                            border border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                    >

                        <option
                            value="0"
                            @selected(old('custom_products_returnable', '0') == '0')
                        >
                            Not allowed
                        </option>

                        <option
                            value="1"
                            @selected(old('custom_products_returnable') == '1')
                        >
                            Allowed
                        </option>

                    </select>

                    @error('custom_products_returnable')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>


            {{-- ====================================================
                RETURN REASONS
            ===================================================== --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-5">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Return Reasons
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Select the reasons that can be used when requesting a return under this policy.
                    </p>

                </div>


                @if($reasons->count())

                    <div class="flex flex-col gap-2">

                        @foreach($reasons as $reason)

                            @php
                                $translation = $reason->translation(app()->getLocale())
                                    ?? $reason->translation('en');
                            @endphp

                            <label
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-4
                                    p-4
                                    rounded-xl
                                    border
                                    border-gray-200
                                    bg-gray-50/50
                                    hover:bg-gray-50
                                    cursor-pointer
                                    transition
                                "
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <input
                                        type="checkbox"
                                        name="reasons[]"
                                        value="{{ $reason->id }}"
                                        @checked(in_array(
                                            $reason->id,
                                            old('reasons', [])
                                        ))
                                        class="
                                            w-4
                                            h-4
                                            rounded
                                            border-gray-300
                                            text-gray-900
                                            focus:ring-gray-200
                                        "
                                    >

                                    <div class="min-w-0">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $translation?->name ?? $reason->code }}
                                        </div>

                                        @if($translation?->description)

                                            <div class="text-xs text-gray-400 mt-0.5 truncate">
                                                {{ $translation->description }}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                                <code
                                    class="
                                        shrink-0
                                        text-[11px]
                                        text-gray-500
                                        bg-white
                                        border
                                        border-gray-200
                                        px-1.5
                                        py-0.5
                                        rounded
                                    "
                                >
                                    {{ $reason->code }}
                                </code>

                            </label>

                        @endforeach

                    </div>

                @else

                    <div
                        class="
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50
                            p-5
                            text-sm
                            text-gray-500
                        "
                    >
                        No active return reasons are available.
                    </div>

                @endif

                @error('reasons')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

                @error('reasons.*')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                RETURN RESOLUTIONS
            ===================================================== --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-5">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Return Resolutions
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Select the resolutions that can be offered for returns under this policy.
                    </p>

                </div>


                @if($resolutions->count())

                    <div class="flex flex-col gap-2">

                        @foreach($resolutions as $resolution)

                            @php
                                $translation = $resolution->translation(app()->getLocale())
                                    ?? $resolution->translation('en');
                            @endphp

                            <label
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-4
                                    p-4
                                    rounded-xl
                                    border
                                    border-gray-200
                                    bg-gray-50/50
                                    hover:bg-gray-50
                                    cursor-pointer
                                    transition
                                "
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <input
                                        type="checkbox"
                                        name="resolutions[]"
                                        value="{{ $resolution->id }}"
                                        @checked(in_array(
                                            $resolution->id,
                                            old('resolutions', [])
                                        ))
                                        class="
                                            w-4
                                            h-4
                                            rounded
                                            border-gray-300
                                            text-gray-900
                                            focus:ring-gray-200
                                        "
                                    >

                                    <div class="min-w-0">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $translation?->name ?? $resolution->code }}
                                        </div>

                                        @if($translation?->description)

                                            <div class="text-xs text-gray-400 mt-0.5 truncate">
                                                {{ $translation->description }}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                                <code
                                    class="
                                        shrink-0
                                        text-[11px]
                                        text-gray-500
                                        bg-white
                                        border
                                        border-gray-200
                                        px-1.5
                                        py-0.5
                                        rounded
                                    "
                                >
                                    {{ $resolution->code }}
                                </code>

                            </label>

                        @endforeach

                    </div>

                @else

                    <div
                        class="
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50
                            p-5
                            text-sm
                            text-gray-500
                        "
                    >
                        No active return resolutions are available.
                    </div>

                @endif

                @error('resolutions')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

                @error('resolutions.*')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                TRANSLATIONS
            ===================================================== --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-5">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Translations
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Add the name and description for each active language.
                    </p>

                </div>


                <div class="flex flex-col gap-5">

                    @foreach($languages as $language)

                        <div
                            class="
                                rounded-xl
                                border
                                border-gray-200
                                bg-gray-50/50
                                p-5
                            "
                        >

                            {{-- LANGUAGE HEADER --}}

                            <div class="flex items-center justify-between gap-4 mb-4">

                                <div>

                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $language->name }}
                                    </div>

                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        {{ $language->native_name }}
                                    </div>

                                </div>

                                <span
                                    class="
                                        inline-flex
                                        items-center
                                        px-2
                                        py-1
                                        rounded-md
                                        bg-white
                                        border
                                        border-gray-200
                                        text-[11px]
                                        font-mono
                                        text-gray-500
                                    "
                                >
                                    {{ strtoupper($language->code) }}
                                </span>

                            </div>


                            {{-- NAME --}}

                            <div>

                                <label
                                    for="translation_name_{{ $language->code }}"
                                    class="block text-[13px] font-semibold text-gray-800"
                                >
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="translations[{{ $language->code }}][name]"
                                    id="translation_name_{{ $language->code }}"
                                    value="{{ old("translations.{$language->code}.name") }}"
                                    class="
                                        mt-2
                                        w-full
                                        h-10
                                        px-3
                                        rounded-lg
                                        border border-gray-200
                                        bg-white
                                        text-sm
                                        text-gray-900
                                        outline-none
                                        transition
                                        focus:border-gray-400
                                        focus:ring-2
                                        focus:ring-gray-100
                                    "
                                    placeholder="Return policy name"
                                >

                                @error("translations.{$language->code}.name")
                                    <span class="block mt-1.5 text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- DESCRIPTION --}}

                            <div class="mt-4">

                                <label
                                    for="translation_description_{{ $language->code }}"
                                    class="block text-[13px] font-semibold text-gray-800"
                                >
                                    Description
                                </label>

                                <textarea
                                    name="translations[{{ $language->code }}][description]"
                                    id="translation_description_{{ $language->code }}"
                                    rows="3"
                                    class="
                                        mt-2
                                        w-full
                                        px-3
                                        py-2.5
                                        rounded-lg
                                        border border-gray-200
                                        bg-white
                                        text-sm
                                        text-gray-900
                                        outline-none
                                        transition
                                        resize-y
                                        focus:border-gray-400
                                        focus:ring-2
                                        focus:ring-gray-100
                                    "
                                    placeholder="Short description of this return policy"
                                >{{ old("translations.{$language->code}.description") }}</textarea>

                                @error("translations.{$language->code}.description")
                                    <span class="block mt-1.5 text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- ====================================================
                ACTIONS
            ===================================================== --}}

            <div
                class="
                    flex
                    items-center
                    justify-end
                    gap-3
                    pt-6
                    border-t
                    border-gray-100
                "
            >

                <a
                    href="{{ route('admin.settings.return-policies.index') }}"
                    class="
                        px-4
                        py-2
                        text-sm
                        border
                        border-gray-300
                        rounded-lg
                        text-gray-700
                        hover:bg-gray-100
                        transition
                    "
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="
                        px-4
                        py-2
                        text-sm
                        font-medium
                        rounded-lg
                        bg-gray-900
                        text-white
                        hover:bg-gray-800
                        transition
                    "
                >
                    Create Return Policy
                </button>

            </div>

        </form>

    </div>

</div>

@endsection