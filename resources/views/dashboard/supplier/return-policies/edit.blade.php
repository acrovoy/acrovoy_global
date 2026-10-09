
@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Edit Return Policy
            </h2>

            <p class="text-sm text-gray-500">
                Update your custom return policy and its return rules
            </p>
        </div>

        <a href="{{ route('supplier.return-policies.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>

            Back to Return Policies
        </a>
    </div>


    {{-- =========================================================
        ALERTS
    ========================================================== --}}

    <x-alerts />


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4">

            <div class="flex items-start gap-3">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-red-600 mt-0.5 shrink-0"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3.5m0 3h.01M10.29 3.86l-7.17 12.42A2 2 0 004.85 19h14.3a2 2 0 001.73-2.72L13.71 3.86a2 2 0 001.73-2.72L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>

                <div>
                    <p class="text-sm font-semibold text-red-800">
                        Please correct the following errors:
                    </p>

                    <ul class="mt-2 space-y-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    @endif


    {{-- =========================================================
        FORM
    ========================================================== --}}

    <form method="POST"
          action="{{ route('supplier.return-policies.update', $returnPolicy) }}">

        @csrf
        @method('PUT')


        {{-- =====================================================
            BASIC INFORMATION
        ====================================================== --}}

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Basic Information
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Update the main information for this return policy.
                </p>
            </div>


            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- =================================================
                    NAME
                ================================================== --}}

                <div>
                    <label for="name"
                           class="block text-sm font-semibold text-gray-700">
                        Policy Name
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $returnPolicy->name) }}"
                           required
                           placeholder="e.g. Standard Returns"
                           class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- =================================================
                    CODE
                ================================================== --}}

                <div>
                    <label for="code"
                           class="block text-sm font-semibold text-gray-700">
                        Policy Code
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code', $returnPolicy->code) }}"
                           required
                           placeholder="e.g. standard_returns"
                           class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                    <p class="mt-1.5 text-xs text-gray-400">
                        Use letters, numbers, dashes or underscores.
                    </p>

                    @error('code')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

        </div>


        {{-- =========================================================
            RETURN RULES
        ========================================================== --}}

        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Return Rules
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Configure when and how customers can return products.
                </p>
            </div>


            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- =================================================
                    RETURN WINDOW
                ================================================== --}}

                <div>
                    <label for="return_window_days"
                           class="block text-sm font-semibold text-gray-700">
                        Return Window
                    </label>

                    <div class="mt-2 flex">

                        <input type="number"
                               id="return_window_days"
                               name="return_window_days"
                               value="{{ old('return_window_days', $returnPolicy->return_window_days) }}"
                               min="0"
                               max="3650"
                               placeholder="30"
                               class="w-full px-4 py-2.5 rounded-l-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                        <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-sm text-gray-500">
                            days
                        </span>

                    </div>

                    <p class="mt-1.5 text-xs text-gray-400">
                        Leave empty if the policy does not have a fixed return window.
                    </p>

                    @error('return_window_days')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- =================================================
                    SHIPPING PAYER
                ================================================== --}}

                <div>
                    <label for="return_shipping_payer"
                           class="block text-sm font-semibold text-gray-700">
                        Return Shipping Paid By
                    </label>

                    <select id="return_shipping_payer"
                            name="return_shipping_payer"
                            class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                        <option value="">
                            Not specified
                        </option>

                        <option value="buyer"
                            @selected(old('return_shipping_payer', $returnPolicy->return_shipping_payer) === 'buyer')>
                            Buyer
                        </option>

                        <option value="supplier"
                            @selected(old('return_shipping_payer', $returnPolicy->return_shipping_payer) === 'supplier')>
                            Supplier
                        </option>

                        <option value="depends_on_reason"
                            @selected(old('return_shipping_payer', $returnPolicy->return_shipping_payer) === 'depends_on_reason')>
                            Depends on reason
                        </option>

                    </select>

                    @error('return_shipping_payer')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- =================================================
                    RESTOCKING FEE
                ================================================== --}}

                <div class="lg:col-span-2">

                    <div class="flex items-start gap-3">

                        <input type="checkbox"
                               id="restocking_fee_enabled"
                               name="restocking_fee_enabled"
                               value="1"
                               @checked(old('restocking_fee_enabled', $returnPolicy->restocking_fee_enabled))
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                        <div class="flex-1">

                            <label for="restocking_fee_enabled"
                                   class="text-sm font-semibold text-gray-700 cursor-pointer">
                                Restocking Fee
                            </label>

                            <p class="mt-1 text-xs text-gray-400">
                                Charge a percentage of the product value as a restocking fee.
                            </p>

                        </div>

                    </div>


                    <div class="mt-4 max-w-sm">

                        <label for="restocking_fee_percent"
                               class="block text-sm font-medium text-gray-700">
                            Restocking Fee Percentage
                        </label>

                        <div class="mt-2 flex">

                            <input type="number"
                                   id="restocking_fee_percent"
                                   name="restocking_fee_percent"
                                   value="{{ old('restocking_fee_percent', $returnPolicy->restocking_fee_percent) }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   placeholder="20"
                                   class="w-full px-4 py-2.5 rounded-l-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                            <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-sm text-gray-500">
                                %
                            </span>

                        </div>

                        @error('restocking_fee_percent')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- =================================================
                    CUSTOM PRODUCTS
                ================================================== --}}

                <div class="lg:col-span-2">

                    <label class="flex items-start gap-3 cursor-pointer">

                        <input type="checkbox"
                               name="custom_products_returnable"
                               value="1"
                               @checked(old('custom_products_returnable', $returnPolicy->custom_products_returnable))
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                        <span>

                            <span class="block text-sm font-semibold text-gray-700">
                                Custom Products Are Returnable
                            </span>

                            <span class="block mt-1 text-xs text-gray-400">
                                Allow products made or customized specifically for the buyer to be returned.
                            </span>

                        </span>

                    </label>

                </div>

            </div>

        </div>


        {{-- =========================================================
            RETURN REASONS
        ========================================================== --}}

        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Return Reasons
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Select the reasons that allow a customer to request a return.
                </p>
            </div>


            <div class="p-6">

                @php
                    $selectedReasons = old(
                        'reasons',
                        $selectedReasonIds ?? $returnPolicy->reasons->pluck('id')->toArray()
                    );
                @endphp

                @if($reasons->isNotEmpty())

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        @foreach($reasons as $reason)

                            @php
                                $translation =
                                    $reason->translation(app()->getLocale())
                                    ?? $reason->translation('en');

                                $reasonName =
                                    $translation?->name
                                    ?? $reason->code;
                            @endphp

                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">

                                <input type="checkbox"
                                       name="reasons[]"
                                       value="{{ $reason->id }}"
                                       @checked(in_array(
                                           $reason->id,
                                           $selectedReasons
                                       ))
                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                                <span class="min-w-0">

                                    <span class="block text-sm font-medium text-gray-700">
                                        {{ $reasonName }}
                                    </span>

                                    <span class="block mt-0.5 text-[11px] text-gray-400 font-mono">
                                        {{ $reason->code }}
                                    </span>

                                </span>

                            </label>

                        @endforeach

                    </div>

                @else

                    <p class="text-sm text-gray-400">
                        No active return reasons are available.
                    </p>

                @endif

                @error('reasons')
                    <p class="mt-3 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        {{-- =========================================================
            RETURN RESOLUTIONS
        ========================================================== --}}

        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Return Resolutions
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Select the resolutions available for approved returns.
                </p>
            </div>


            <div class="p-6">

                @php
                    $selectedResolutions = old(
                        'resolutions',
                        $selectedResolutionIds ?? $returnPolicy->resolutions->pluck('id')->toArray()
                    );
                @endphp

                @if($resolutions->isNotEmpty())

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        @foreach($resolutions as $resolution)

                            @php
                                $translation =
                                    $resolution->translation(app()->getLocale())
                                    ?? $resolution->translation('en');

                                $resolutionName =
                                    $translation?->name
                                    ?? $resolution->code;
                            @endphp

                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">

                                <input type="checkbox"
                                       name="resolutions[]"
                                       value="{{ $resolution->id }}"
                                       @checked(in_array(
                                           $resolution->id,
                                           $selectedResolutions
                                       ))
                                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                                <span class="min-w-0">

                                    <span class="block text-sm font-medium text-gray-700">
                                        {{ $resolutionName }}
                                    </span>

                                    <span class="block mt-0.5 text-[11px] text-gray-400 font-mono">
                                        {{ $resolution->code }}
                                    </span>

                                </span>

                            </label>

                        @endforeach

                    </div>

                @else

                    <p class="text-sm text-gray-400">
                        No active return resolutions are available.
                    </p>

                @endif

                @error('resolutions')
                    <p class="mt-3 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        {{-- =========================================================
            TRANSLATIONS
        ========================================================== --}}

        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Translations
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Update localized names and descriptions for this return policy.
                </p>
            </div>


            <div class="p-6">

                <div class="space-y-5">

                    @foreach($languages as $language)

                        @php
                            $locale = $language->code ?? $language->locale ?? null;

                            $existingTranslation = $returnPolicy->translations
                                ->firstWhere('locale', $locale);

                            $oldTranslation = old(
                                "translations.$locale",
                                [
                                    'name' => $existingTranslation?->name ?? '',
                                    'description' => $existingTranslation?->description ?? '',
                                ]
                            );
                        @endphp

                        @if($locale)

                            <div class="border border-gray-200 rounded-xl overflow-hidden">

                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">

                                    <div>
                                        <span class="text-sm font-semibold text-gray-800">
                                            {{ $language->name ?? strtoupper($locale) }}
                                        </span>

                                        <span class="ml-2 text-[11px] font-mono text-gray-400 uppercase">
                                            {{ $locale }}
                                        </span>
                                    </div>

                                </div>


                                <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-4">

                                    {{-- =================================================
                                        TRANSLATED NAME
                                    ================================================== --}}

                                    <div>

                                        <label class="block text-sm font-medium text-gray-700">
                                            Name
                                        </label>

                                        <input type="text"
                                               name="translations[{{ $locale }}][name]"
                                               value="{{ $oldTranslation['name'] ?? '' }}"
                                               placeholder="{{ $returnPolicy->name }}"
                                               class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition">

                                        @error("translations.$locale.name")
                                            <p class="mt-1.5 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>


                                    {{-- =================================================
                                        DESCRIPTION
                                    ================================================== --}}

                                    <div>

                                        <label class="block text-sm font-medium text-gray-700">
                                            Description
                                        </label>

                                        <textarea name="translations[{{ $locale }}][description]"
                                                  rows="3"
                                                  placeholder="Describe the return policy..."
                                                  class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition resize-y">{{ $oldTranslation['description'] ?? '' }}</textarea>

                                        @error("translations.$locale.description")
                                            <p class="mt-1.5 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                </div>

                            </div>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>


        {{-- =========================================================
            STATUS
        ========================================================== --}}

        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    Status
                </h3>
            </div>


            <div class="p-6">

                <label class="flex items-start gap-3 cursor-pointer">

                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           @checked(old('is_active', $returnPolicy->is_active))
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                    <span>

                        <span class="block text-sm font-semibold text-gray-700">
                            Active
                        </span>

                        <span class="block mt-1 text-xs text-gray-400">
                            Active policies can be assigned to products and selected as the supplier default.
                        </span>

                    </span>

                </label>

            </div>

        </div>


        {{-- =========================================================
            FORM ACTIONS
        ========================================================== --}}

        <div class="mt-6 flex items-center justify-end gap-3">

            <a href="{{ route('supplier.return-policies.index') }}"
               class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 13l4 4L19 7" />
                </svg>

                Save Changes
            </button>

        </div>

    </form>

</div>
@endsection

