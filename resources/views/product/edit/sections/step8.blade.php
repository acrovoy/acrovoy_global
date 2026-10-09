

@include('product.edit.partials.progress-bar', [$mode = 'edit'])

<form method="POST"
    action="{{ route('supplier.products.update', [
          'product' => $product->id,
          'step' => 8
      ]) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    
    
    {{-- Country of origin --}}
<div>
    <h3 class="text-xl font-semibold mb-4">Country of Origin</h3>
    <select name="country_id" class="input w-full">
        <option value="">Select a country</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}" {{ $product->country_id == $country->id ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
    <p class="text-sm text-gray-500 mt-1">Выберите страну, из которой поставляется товар.</p>
</div>



{{-- Shipping Dimensions --}}
<div class="mt-6 bg-white border rounded-xl p-6">
    <h3 class="text-xl font-semibold mb-4">Shipping Dimensions

    <x-help-tooltip width="w-80">
    <div class="space-y-2 leading-relaxed">
        <div class="font-semibold text-white">Shipping Dimensions</div>
        <div class="text-gray-200 text-sm">
            Укажите габариты и вес упаковки для расчёта доставки и логистики.
            Размеры упаковки могут отличаться от реальных размеров самого товара.
        </div>
        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
            <li>Length — длина упаковки в сантиметрах</li>
            <li>Width — ширина упаковки в сантиметрах</li>
            <li>Height — высота упаковки в сантиметрах</li>
            <li>Weight — вес упаковки в килограммах</li>
            <li>Package Type — тип упаковки: коробка, паллет, комплект и т.д.</li>
        </ul>
        <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
            Note: <span class="text-white/80">транспортировочные габариты могут включать упаковку и защитные материалы,
            поэтому могут быть больше реальных размеров товара.</span>
        </div>
    </div>
</x-help-tooltip>


    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Length --}}
        <div>
            <label class="block mb-1 font-medium">Length (cm)</label>
            <input 
                type="number" 
                step="0.001" 
                name="shipping[length]" 
                value="{{ old('shipping.length', isset($product->shippingDimensions->length) ? number_format($product->shippingDimensions->length, 1, '.', '') : '') }}"
                class="input w-full"
            >
        </div>

        {{-- Width --}}
        <div>
            <label class="block mb-1 font-medium">Width (cm)</label>
            <input 
                type="number" 
                step="0.001" 
                name="shipping[width]" 
                value="{{ old('shipping.width', $product->shippingDimensions->width ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Height --}}
        <div>
            <label class="block mb-1 font-medium">Height (cm)</label>
            <input 
                type="number" 
                step="0.001" 
                name="shipping[height]" 
                value="{{ old('shipping.height', $product->shippingDimensions->height ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Weight --}}
        <div>
            <label class="block mb-1 font-medium">Weight (kg)</label>
            <input 
                type="number" 
                step="0.001" 
                name="shipping[weight]" 
                value="{{ old('shipping.weight', $product->shippingDimensions->weight ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Package Type --}}
        <div>
            <label class="block mb-1 font-medium">Package Type</label>
            <select name="shipping[package_type]" class="input w-full">
                <option value="box" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'box') ? 'selected' : '' }}>Box</option>
                <option value="pallet" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'pallet') ? 'selected' : '' }}>Pallet</option>
                <option value="set" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'set') ? 'selected' : '' }}>Set</option>
            </select>
        </div>

    </div>

    <p class="text-sm text-gray-500 mt-2">
        Укажите габариты и вес упаковки для расчёта доставки и логистики.
    </p>
</div>




{{-- Shipping Templates --}}
<div class="mt-6">
    <h3 class="text-xl font-semibold mb-4">Shipping Templates</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Platform / Default Shipping --}}
        @if($defaultShippingTemplate)
            <label
                class="border-2 border-dashed border-gray-400 rounded-xl p-4 cursor-pointer transition
                    hover:border-gray-700 hover:bg-gray-100
                    flex gap-3 items-start bg-gray-50 shadow-sm">

                {{-- Скрытый input, чтобы значение отправлялось --}}
                <input type="hidden" name="shipping_templates[]" value="{{ $defaultShippingTemplate->id }}">

                {{-- Видимый чекбокс только для UI --}}
                <input
                    type="checkbox"
                    value="{{ $defaultShippingTemplate->id }}"
                    class="mt-1"
                    checked
                    disabled
                >

                <div>
                    <div class="font-semibold text-gray-900 flex items-center gap-2">
                        {{ $defaultShippingTemplate->title }}
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">
                            Platform delivery
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $defaultShippingTemplate->description }}
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Price and delivery time will be calculated after order placement
                    </div>
                </div>
            </label>
        @endif

        {{-- Seller Shipping Templates --}}
        @foreach($shippingTemplates as $template)
            <label
                    class="border rounded-xl p-4 cursor-pointer transition
                        hover:border-blue-600 hover:bg-blue-50
                        flex gap-3 items-start bg-white shadow-sm">

                    <input
                        type="checkbox"
                        name="shipping_templates[]"
                        value="{{ $template->id }}"
                        class="mt-1"
                        {{ in_array($template->id, old('shipping_templates', $productShippingIds ?? [])) ? 'checked' : '' }}
                    >

                    <div>
                        <div class="font-semibold text-gray-900">
                            {{ $template->title }}
                        </div>

                        <div class="text-sm text-gray-600 mt-1">
                            {{ $template->description }}
                        </div>

                        <div class="text-xs text-gray-500 mt-2">
                            Seller-defined delivery
                        </div>
                    </div>
            </label>
        @endforeach

    </div>

    <p class="text-sm text-gray-500 mt-2">
            Выберите один или несколько шаблонов доставки.
            Если выбран только платформенный вариант — заказ будет ожидать расчёта доставки.
    </p>
</div>






{{-- Payment Methods --}}
<div class="mt-6 bg-white border rounded-xl p-6">

    <div class="flex items-center gap-2 mb-4">
        <h3 class="text-xl font-semibold">Payment Methods</h3>

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">
                    Payment Methods
                </div>

                <div class="text-gray-200 text-sm">
                    Выберите способы оплаты, которые доступны покупателю для этого товара.
                </div>

                <div class="text-gray-300 text-xs">
                    Можно выбрать несколько способов оплаты.
                </div>
            </div>
        </x-help-tooltip>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @foreach($paymentMethods as $paymentMethod)

            @php
                $translation = $paymentMethod->translations
                    ->firstWhere('locale', app()->getLocale());

                if (!$translation) {
                    $translation = $paymentMethod->translations
                        ->firstWhere('locale', 'en');
                }
            @endphp

            <label
                class="border rounded-xl p-4 cursor-pointer transition
                    hover:border-blue-600 hover:bg-blue-50
                    flex gap-3 items-start bg-white shadow-sm">

                <input
                    type="checkbox"
                    name="payment_methods[]"
                    value="{{ $paymentMethod->id }}"
                    class="mt-1"
                    {{ in_array(
                        $paymentMethod->id,
                        old('payment_methods', $productPaymentMethodIds ?? [])
                    ) ? 'checked' : '' }}
                >

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $translation?->name ?? $paymentMethod->code }}
                    </div>

                    @if($translation?->description)
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $translation->description }}
                        </div>
                    @endif
                </div>

            </label>

        @endforeach

    </div>

    <p class="text-sm text-gray-500 mt-3">
        Выберите один или несколько способов оплаты, доступных для этого товара.
    </p>

</div>




{{-- Payment Terms --}}
<div class="mt-6 bg-white border rounded-xl p-6">

    <div class="flex items-center gap-2 mb-4">
        <h3 class="text-xl font-semibold">Payment Terms</h3>

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">
                    Payment Terms
                </div>

                <div class="text-gray-200 text-sm">
                    Укажите условия оплаты, доступные для этого товара.
                </div>

                <div class="text-gray-300 text-xs">
                    Например: 100% предоплата, 50/50, Net 30 или условия по договорённости.
                </div>
            </div>
        </x-help-tooltip>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @foreach($paymentTerms as $paymentTerm)

            @php
                $translation = $paymentTerm->translations
                    ->firstWhere('locale', app()->getLocale());

                if (!$translation) {
                    $translation = $paymentTerm->translations
                        ->firstWhere('locale', 'en');
                }
            @endphp

            <label
                class="border rounded-xl p-4 cursor-pointer transition
                    hover:border-blue-600 hover:bg-blue-50
                    flex gap-3 items-start bg-white shadow-sm">

                <input
                    type="checkbox"
                    name="payment_terms[]"
                    value="{{ $paymentTerm->id }}"
                    class="mt-1"
                    {{ in_array(
                        $paymentTerm->id,
                        old('payment_terms', $productPaymentTermIds ?? [])
                    ) ? 'checked' : '' }}
                >

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $translation?->name ?? $paymentTerm->code }}
                    </div>

                    @if($translation?->description)
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $translation->description }}
                        </div>
                    @endif
                </div>

            </label>

        @endforeach

    </div>

    <p class="text-sm text-gray-500 mt-3">
        Выберите один или несколько вариантов условий оплаты.
    </p>

</div>




{{-- =========================================================
    RETURNS & REFUNDS
========================================================= --}}


@php
    /*
     * ---------------------------------------------------------
     * CURRENT PRODUCT RETURN POLICY
     * ---------------------------------------------------------
     */

    $currentReturnPolicy = $productReturnPolicy ?? null;

    $useSupplierDefault = old(
        'use_supplier_default',
        $currentReturnPolicy?->use_supplier_default ?? true
    );

    $selectedReturnPolicyId = old(
        'return_policy_id',
        $currentReturnPolicy?->return_policy_id
    );


    /*
     * ---------------------------------------------------------
     * TRANSLATION HELPER
     * ---------------------------------------------------------
     */

    $returnPolicyTranslation = function ($policy) {
        return $policy->translation(app()->getLocale())
            ?? $policy->translation('en');
    };


    /*
     * ---------------------------------------------------------
     * SUPPLIER DEFAULT RETURN POLICY
     * ---------------------------------------------------------
     *
     * The supplier default is stored separately from the product
     * return policy.
     *
     * ProductReturnPolicy:
     * - use_supplier_default = true
     * - return_policy_id = NULL
     *
     * The actual default policy is taken from:
     * supplier_return_policy_settings
     */

    $supplierDefaultPolicy = null;

    if (!empty($supplierDefaultReturnPolicyId)) {
        $supplierDefaultPolicy = collect($returnPolicies ?? [])
            ->firstWhere('id', $supplierDefaultReturnPolicyId);
    }


    /*
     * ---------------------------------------------------------
     * SUPPLIER DEFAULT TRANSLATION
     * ---------------------------------------------------------
     */

    $supplierDefaultPolicyTranslation = null;

    if ($supplierDefaultPolicy) {
        $supplierDefaultPolicyTranslation =
            $returnPolicyTranslation($supplierDefaultPolicy);
    }


    /*
     * ---------------------------------------------------------
     * SUPPLIER DEFAULT DISPLAY DATA
     * ---------------------------------------------------------
     */

    $supplierDefaultPolicyName =
        $supplierDefaultPolicyTranslation?->name
        ?: $supplierDefaultPolicy?->name;

    $supplierDefaultPolicyDescription =
        $supplierDefaultPolicyTranslation?->description
        ?: null;


    /*
     * ---------------------------------------------------------
     * SHIPPING PAYER LABEL
     * ---------------------------------------------------------
     */

    $supplierDefaultShippingPayer = null;

    if ($supplierDefaultPolicy?->return_shipping_payer) {

        $supplierDefaultShippingPayer = match (
            $supplierDefaultPolicy->return_shipping_payer
        ) {
            'buyer' => 'Buyer pays shipping',
            'supplier' => 'Supplier pays shipping',
            'depends_on_reason' => 'Shipping depends on reason',
            default => $supplierDefaultPolicy->return_shipping_payer,
        };
    }
@endphp




<div
    class="mt-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden"
    x-data="{
        useSupplierDefault: {{ $useSupplierDefault ? 'true' : 'false' }},
        selectedPolicyId: '{{ $selectedReturnPolicyId ?? '' }}'
    }"
>

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="px-6 py-5 border-b border-gray-100">

        <div class="flex items-start justify-between gap-5">

            <div>

                <div class="flex items-center gap-2">

                    <h3 class="font-bold text-[17px] leading-tight text-gray-900">
                        Returns & Refunds
                    </h3>

                    <div
                        class="w-5 h-5 rounded-full border border-gray-300 text-gray-500 flex items-center justify-center text-[11px] font-semibold"
                        title="Choose how returns are handled for this product."
                    >
                        ?
                    </div>

                </div>

                <p class="mt-1.5 text-sm leading-relaxed text-gray-500">
                    Choose the return policy that applies to this product.
                </p>

            </div>

        </div>

    </div>


    {{-- =====================================================
        CONTENT
    ====================================================== --}}

    <div class="p-6">

        {{-- =================================================
            POLICY MODE
        ================================================== --}}

        <div class="space-y-3">

            
{{-- ---------------------------------------------
    USE SUPPLIER DEFAULT
---------------------------------------------- --}}

<label
    class="block cursor-pointer"
    @click="
        useSupplierDefault = true;
        selectedPolicyId = '';
    "
>

    <input
        type="radio"
        name="use_supplier_default"
        value="1"
        class="sr-only"
        x-model="useSupplierDefault"
        @checked($useSupplierDefault)
    >


    <div
        class="rounded-xl border p-4 transition"
        :class="useSupplierDefault
            ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
            : 'border-gray-200 bg-white hover:border-gray-300'"
    >

        <div class="flex items-start gap-3">

            {{-- Radio indicator --}}

            <div
                class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                :class="useSupplierDefault
                    ? 'border-gray-900'
                    : 'border-gray-300'"
            >

                <div
                    x-show="useSupplierDefault"
                    class="w-2.5 h-2.5 rounded-full bg-gray-900"
                ></div>

            </div>


            {{-- Content --}}

            <div class="min-w-0 flex-1">

                <div class="text-sm font-semibold text-gray-900">
                    Use supplier default
                </div>

                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                    Use the default return policy configured for the supplier.
                </p>


                {{-- =============================================
                    SUPPLIER DEFAULT POLICY
                ============================================== --}}

                @if($supplierDefaultPolicy)

                    <div
                        class="mt-4 rounded-xl border border-gray-200 bg-white p-3"
                    >

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">

                                <div
                                    class="text-[11px] font-semibold uppercase tracking-wide text-gray-400"
                                >
                                    Supplier default
                                </div>

                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $supplierDefaultPolicyName }}
                                </div>

                                @if($supplierDefaultPolicyDescription)

                                    <p class="mt-1 text-xs leading-relaxed text-gray-500">
                                        {{ $supplierDefaultPolicyDescription }}
                                    </p>

                                @endif

                            </div>


                            {{-- Policy code --}}

                            <span
                                class="shrink-0 rounded-lg bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500"
                            >
                                {{ $supplierDefaultPolicy->code }}
                            </span>

                        </div>


                        {{-- =====================================
                            POLICY SUMMARY
                        ====================================== --}}

                        <div class="mt-3 flex flex-wrap items-center gap-2">

                            {{-- Return window --}}

                            @if($supplierDefaultPolicy->return_window_days !== null)

                                <span
                                    class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200"
                                >
                                    {{ $supplierDefaultPolicy->return_window_days }} days
                                </span>

                            @else

                                <span
                                    class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200"
                                >
                                    No time limit
                                </span>

                            @endif


                            {{-- Shipping payer --}}

                            @if($supplierDefaultShippingPayer)

                                <span
                                    class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200"
                                >
                                    {{ $supplierDefaultShippingPayer }}
                                </span>

                            @endif


                            {{-- Restocking fee --}}

                            @if($supplierDefaultPolicy->restocking_fee_enabled)

                                <span
                                    class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200"
                                >

                                    Restocking

                                    @if($supplierDefaultPolicy->restocking_fee_percent !== null)
                                        {{ $supplierDefaultPolicy->restocking_fee_percent }}%
                                    @endif

                                </span>

                            @endif


                            {{-- Custom products --}}

                            @if($supplierDefaultPolicy->custom_products_returnable)

                                <span
                                    class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200"
                                >
                                    Custom products allowed
                                </span>

                            @endif

                        </div>

                    </div>

                @else

                    {{-- =========================================
                        NO DEFAULT CONFIGURED
                    ========================================== --}}

                    <div
                        class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5"
                    >

                        <div class="text-xs font-medium text-amber-700">
                            No supplier default return policy has been configured.
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</label>




            {{-- ---------------------------------------------
                SELECT POLICY FOR THIS PRODUCT
            ---------------------------------------------- --}}

            <label
                class="block cursor-pointer"
                @click="useSupplierDefault = false"
            >

                <input
                    type="radio"
                    name="use_supplier_default"
                    value="0"
                    class="sr-only"
                    x-model="useSupplierDefault"
                    @checked(!$useSupplierDefault)
                >

                <div
                    class="rounded-xl border p-4 transition"
                    :class="!useSupplierDefault
                        ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                        : 'border-gray-200 bg-white hover:border-gray-300'"
                >

                    <div class="flex items-start gap-3">

                        {{-- Radio indicator --}}

                        <div
                            class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                            :class="!useSupplierDefault
                                ? 'border-gray-900'
                                : 'border-gray-300'"
                        >

                            <div
                                x-show="!useSupplierDefault"
                                class="w-2.5 h-2.5 rounded-full bg-gray-900"
                            ></div>

                        </div>


                        {{-- Content --}}

                        <div class="min-w-0">

                            <div class="text-sm font-semibold text-gray-900">
                                Select policy for this product
                            </div>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Choose a specific platform return policy for this product.
                            </p>

                        </div>

                    </div>

                </div>

            </label>

        </div>


        {{-- =================================================
            PLATFORM POLICIES
        ================================================== --}}

        <div
            x-show="!useSupplierDefault"
            x-transition
            class="mt-5"
        >

            <div class="mb-3">

                <div class="text-sm font-semibold text-gray-900">
                    Platform return policies
                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Select one of the available platform policies.
                </p>

            </div>


            @if(isset($returnPolicies) && $returnPolicies->count())

                <div class="space-y-3">

                    @foreach($returnPolicies as $policy)

                        @php
                            $translation = $returnPolicyTranslation($policy);

                            $policyName = $translation?->name
                                ?? $policy->name;

                            $policyDescription = $translation?->description
                                ?? null;
                        @endphp


                        <label class="block cursor-pointer">

                            <input
                                type="radio"
                                name="return_policy_id"
                                value="{{ $policy->id }}"
                                class="sr-only"
                                x-model="selectedPolicyId"
                            >

                            <div
                                class="rounded-xl border p-4 transition"
                                :class="selectedPolicyId == '{{ $policy->id }}'
                                    ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                                    : 'border-gray-200 bg-white hover:border-gray-300'"
                            >

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex items-start gap-3 min-w-0">

                                        {{-- Radio indicator --}}

                                        <div
                                            class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                                            :class="selectedPolicyId == '{{ $policy->id }}'
                                                ? 'border-gray-900'
                                                : 'border-gray-300'"
                                        >

                                            <div
                                                x-show="selectedPolicyId == '{{ $policy->id }}'"
                                                class="w-2.5 h-2.5 rounded-full bg-gray-900"
                                            ></div>

                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex items-center gap-2 flex-wrap">

                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ $policyName }}
                                                </span>

                                                @if($policy->is_default)

                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600">
                                                        Default
                                                    </span>

                                                @endif

                                            </div>


                                            @if($policyDescription)

                                                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                                    {{ $policyDescription }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>


                                    @if($policy->code)

                                        <span class="shrink-0 text-[11px] font-medium text-gray-400">
                                            {{ $policy->code }}
                                        </span>

                                    @endif

                                </div>


                                {{-- =========================================
                                    POLICY SUMMARY
                                ========================================== --}}

                                <div class="mt-4 pt-4 border-t border-gray-100">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                                        {{-- Return Window --}}

                                        <div>

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Return Window
                                            </div>

                                            <div class="mt-1 text-sm font-medium text-gray-800">

                                                @if($policy->return_window_days !== null)

                                                    {{ $policy->return_window_days }} days

                                                @else

                                                    No return window

                                                @endif

                                            </div>

                                        </div>


                                        {{-- Shipping --}}

                                        <div>

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Return Shipping
                                            </div>

                                            <div class="mt-1 text-sm font-medium text-gray-800">

                                                @switch($policy->return_shipping_payer)

                                                    @case('buyer')
                                                        Buyer
                                                        @break

                                                    @case('supplier')
                                                        Supplier
                                                        @break

                                                    @case('depends_on_reason')
                                                        Depends on reason
                                                        @break

                                                    @default
                                                        Not specified

                                                @endswitch

                                            </div>

                                        </div>


                                        {{-- Restocking Fee --}}

                                        <div>

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Restocking Fee
                                            </div>

                                            <div class="mt-1 text-sm font-medium text-gray-800">

                                                @if($policy->restocking_fee_enabled)

                                                    {{ rtrim(rtrim(number_format((float) $policy->restocking_fee_percent, 2), '0'), '.') }}%

                                                @else

                                                    None

                                                @endif

                                            </div>

                                        </div>


                                        {{-- Custom Products --}}

                                        <div>

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Custom Products
                                            </div>

                                            <div class="mt-1 text-sm font-medium text-gray-800">

                                                {{ $policy->custom_products_returnable ? 'Allowed' : 'Not allowed' }}

                                            </div>

                                        </div>

                                    </div>


                                    {{-- =====================================
                                        REASONS
                                    ====================================== --}}

                                    @if($policy->reasons->count())

                                        <div class="mt-4">

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Return Reasons
                                            </div>

                                            <div class="mt-2 flex flex-wrap gap-1.5">

                                                @foreach($policy->reasons as $reason)

                                                    @php
                                                        $reasonTranslation = $reason->translation(app()->getLocale())
                                                            ?? $reason->translation('en');
                                                    @endphp

                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                                        {{ $reasonTranslation?->name ?? $reason->code }}
                                                    </span>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    {{-- =====================================
                                        RESOLUTIONS
                                    ====================================== --}}

                                    @if($policy->resolutions->count())

                                        <div class="mt-4">

                                            <div class="text-[11px] uppercase tracking-wide font-semibold text-gray-400">
                                                Resolutions
                                            </div>

                                            <div class="mt-2 flex flex-wrap gap-1.5">

                                                @foreach($policy->resolutions as $resolution)

                                                    @php
                                                        $resolutionTranslation = $resolution->translation(app()->getLocale())
                                                            ?? $resolution->translation('en');
                                                    @endphp

                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                                        {{ $resolutionTranslation?->name ?? $resolution->code }}
                                                    </span>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>

            @else

                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">

                    <div class="text-sm font-medium text-gray-800">
                        No return policies are currently available.
                    </div>

                    <p class="mt-1 text-sm text-gray-500">
                        Please contact the platform administrator to configure return policies.
                    </p>

                </div>

            @endif


            @error('return_policy_id')

                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>

            @enderror

        </div>

    </div>

</div>







<div class="flex justify-between mt-6">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 7]) }}"
            class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-100 text-gray-400 px-6 py-2 rounded">
            Previous
        </a>



        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500">
                Publish
            </button>

    </div>



</form>