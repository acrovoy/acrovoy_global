
@include('product.edit.partials.progress-bar', [$mode = 'edit'])




<form method="POST"
    action="{{ route('supplier.products.update', [
        'product' => $product->id,
        'step' => 8
    ]) }}"
    enctype="multipart/form-data"
    id="productForm">

    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    {{-- =====================================================
        SHIPPING & DELIVERY
    ====================================================== --}}

    <div class="mt-6 bg-white border border-gray-200 rounded shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">

                

                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Shipping & Delivery
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Configure delivery time, package details and shipping options.
                    </p>
                </div>

            </div>
        </div>

        <div class="p-6 space-y-8">

            {{-- LEAD TIME --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                    Lead Time
                </h4>

                <div class="max-w-md flex items-center gap-3">
                    <input
                        type="number"
                        name="lead_time"
                        placeholder="e.g., 25"
                        value="{{ old('lead_time', $product->lead_time) }}"
                        class="input w-full">

                    <span class="text-sm text-gray-500 whitespace-nowrap">
                        day(s)
                    </span>
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Estimated number of days required to prepare the product for dispatch.
                </p>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- SHIPPING DIMENSIONS --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <h4 class="text-sm font-semibold text-gray-900">
                        Shipping Dimensions
                    </h4>

                    <x-help-tooltip width="w-80">
                        <div class="space-y-2 leading-relaxed">
                            <div class="font-semibold text-white">
                                Shipping Dimensions
                            </div>

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
                                Note:
                                <span class="text-white/80">
                                    транспортировочные габариты могут включать упаковку и защитные материалы,
                                    поэтому могут быть больше реальных размеров товара.
                                </span>
                            </div>
                        </div>
                    </x-help-tooltip>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Length --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Length (cm)
                        </label>

                        <input
                            type="number"
                            step="0.001"
                            name="shipping[length]"
                            value="{{ old('shipping.length', isset($product->shippingDimensions->length) ? number_format($product->shippingDimensions->length, 1, '.', '') : '') }}"
                            class="input w-full">
                    </div>

                    {{-- Width --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Width (cm)
                        </label>

                        <input
                            type="number"
                            step="0.001"
                            name="shipping[width]"
                            value="{{ old('shipping.width', $product->shippingDimensions->width ?? '') }}"
                            class="input w-full">
                    </div>

                    {{-- Height --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Height (cm)
                        </label>

                        <input
                            type="number"
                            step="0.001"
                            name="shipping[height]"
                            value="{{ old('shipping.height', $product->shippingDimensions->height ?? '') }}"
                            class="input w-full">
                    </div>

                    {{-- Weight --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Weight (kg)
                        </label>

                        <input
                            type="number"
                            step="0.001"
                            name="shipping[weight]"
                            value="{{ old('shipping.weight', $product->shippingDimensions->weight ?? '') }}"
                            class="input w-full">
                    </div>

                    {{-- Package Type --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Package Type
                        </label>

                        <select name="shipping[package_type]" class="input w-full">
                            <option value="box"
                                {{ old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'box' ? 'selected' : '' }}>
                                Box
                            </option>

                            <option value="pallet"
                                {{ old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'pallet' ? 'selected' : '' }}>
                                Pallet
                            </option>

                            <option value="set"
                                {{ old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'set' ? 'selected' : '' }}>
                                Set
                            </option>
                        </select>
                    </div>

                </div>

                <p class="text-sm text-gray-500 mt-3">
                    Укажите габариты и вес упаковки для расчёта доставки и логистики.
                </p>
            </div>

            <div class="border-t border-gray-100"></div>

            
            


            {{-- SHIPPING TEMPLATES --}}

            <input type="hidden" name="_shipping_templates_submitted" value="1">

@php
    $defaultShippingTemplateIds = collect($defaultShippingTemplateIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->all();

    $productShippingIds = collect($productShippingIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->all();

    if (old('_shipping_templates_submitted')) {
        $initialShippingTemplateIds = old('shipping_templates', []);
    } elseif (!empty($productShippingIds)) {
        $initialShippingTemplateIds = $productShippingIds;
    } else {
        $initialShippingTemplateIds = $defaultShippingTemplateIds;
    }

    $selectedShippingTemplateIds = collect($initialShippingTemplateIds)
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values()
        ->all();
@endphp





<div>
    <div class="flex items-center justify-between gap-3 mb-4">
        <h4 class="text-sm font-semibold text-gray-900">
            Shipping Templates
        </h4>

        <button
            type="button"
            id="open-shipping-templates"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:border-gray-400"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Choose templates
            <span
                id="shipping-templates-count"
                class="inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-gray-100 px-1.5 text-xs font-semibold text-gray-700"
            >{{ count($selectedShippingTemplateIds) }}</span>
        </button>
    </div>

    {{-- Selected templates --}}
    <div id="selected-shipping-templates" class="space-y-4">

        {{-- Platform / Default Shipping is always selected --}}
        @if($defaultShippingTemplate)
            <div class="flex gap-3 items-start rounded-xl border-2 border-dashed border-gray-400 bg-gray-50 p-4 shadow-sm">
                <input
                    type="hidden"
                    name="shipping_templates[]"
                    value="{{ $defaultShippingTemplate->id }}"
                >

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 font-semibold text-gray-900">
                        {{ $defaultShippingTemplate->title }}

                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">
                            Platform delivery
                        </span>
                    </div>

                    @if($defaultShippingTemplate->description)
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $defaultShippingTemplate->description }}
                        </div>
                    @endif

                    <div class="mt-2 text-xs text-gray-500">
                        Price and delivery time will be confirmed after order placement.
                    </div>
                </div>
            </div>
        @endif

        {{-- Selected supplier templates are rendered by JavaScript --}}
        <div id="selected-seller-shipping-templates" class="space-y-3"></div>

        <div
    id="shipping-templates-empty"
    class="{{ empty($selectedShippingTemplateIds) ? '' : 'hidden' }} rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500"
>
    No supplier shipping templates selected.
</div>
    </div>

    <p class="mt-3 text-sm text-gray-500">
        Select one or more shipping templates. Each selected template can provide multiple delivery options.
    </p>
</div>

{{-- Drawer overlay --}}
<div
    id="shipping-templates-overlay"
    class="fixed inset-0 z-100 hidden bg-gray-900/40 opacity-0 transition-opacity duration-300"
    aria-hidden="true"
></div>

{{-- Drawer --}}
<div
    id="shipping-templates-drawer"
    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xl translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="shipping-templates-drawer-title"
>
    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 sm:px-6">
        <div>
            <h3 id="shipping-templates-drawer-title" class="text-lg font-semibold text-gray-900">
                Shipping Templates
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Select the shipping templates available for this product.
            </p>
        </div>

        <button
            type="button"
            data-close-shipping-templates
            class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
            aria-label="Close shipping templates"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m6 6 12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>

    <div class="border-b border-gray-200 p-5 sm:px-6">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path stroke-linecap="round" d="m16 16 4 4"/>
            </svg>

            <input
                type="search"
                id="shipping-templates-search"
                placeholder="Search shipping templates..."
                class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
        </div>
    </div>

    <div id="shipping-templates-options" class="flex-1 space-y-3 overflow-y-auto p-5 sm:px-6">
        @forelse($shippingTemplates as $template)
            @php
                $activeOptions = $template->deliveryOptions
                    ->where('is_active', true)
                    ->sortBy('sort_order');

                $isSelected = in_array(
                    (int) $template->id,
                    $selectedShippingTemplateIds,
                    true
                );
            @endphp

            <label
                data-shipping-template-option
                data-id="{{ $template->id }}"
                data-search="{{ \Illuminate\Support\Str::lower($template->title . ' ' . ($template->description ?? '')) }}"
                class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 shadow-sm transition
                    {{ $isSelected
                        ? 'border-emerald-200 bg-emerald-50'
                        : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <input
                    type="checkbox"
                    name="shipping_templates[]"
                    value="{{ $template->id }}"
                    data-shipping-template-checkbox
                    class="mt-1 h-4 w-4 shrink-0 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    {{ $isSelected ? 'checked' : '' }}
                >

                <div class="min-w-0 flex-1">
                    <div class="shipping-template-details">
                        <div class="font-semibold text-gray-900">
                            {{ $template->title }}
                        </div>

                        @if($template->description)
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $template->description }}
                            </div>
                        @endif

                        @if($activeOptions->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach($activeOptions as $option)
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="text-sm font-medium text-gray-800">
                                                {{ $option->deliveryType?->name
                                                    ?? $option->deliveryType?->code
                                                    ?? 'Delivery option' }}
                                            </span>

                                            @if($option->price !== null && $option->price !== '')
                                                <span class="shrink-0 text-sm font-semibold text-gray-900">
                                                    {{ number_format((float) $option->price, 2) }}
                                                </span>
                                            @else
                                                <span class="shrink-0 text-xs text-gray-500">
                                                    Carrier rates
                                                </span>
                                            @endif
                                        </div>

                                        @if($option->delivery_time)
                                            <div class="mt-1 text-xs text-gray-500">
                                                Delivery time: {{ $option->delivery_time }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-2 text-xs text-gray-500">
                                Delivery details will be confirmed with the supplier.
                            </div>
                        @endif

                        <div class="mt-3 text-xs text-gray-500">
                            Supplier shipping template
                        </div>
                    </div>
                </div>

                <span
                    data-shipping-template-indicator
                    class="mt-0.5 shrink-0 text-emerald-600 {{ $isSelected ? '' : 'invisible' }}"
                    aria-hidden="true"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m5 12 4 4L19 6"/>
                    </svg>
                </span>
            </label>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                No shipping templates available.
            </div>
        @endforelse

        <div id="shipping-templates-no-results" class="hidden py-8 text-center text-sm text-gray-500">
            No matching shipping templates found.
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 border-t border-gray-200 bg-white px-5 py-4 sm:px-6">
        <span class="text-sm text-gray-500">
            Selected: <strong id="shipping-templates-selected-count" class="text-gray-900">0</strong>
        </span>

        <button
            type="button"
            data-done-shipping-templates
            class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
        >
            Done
        </button>
    </div>
</div>






            <div class="border-t border-gray-100"></div>

            {{-- COUNTRY OF ORIGIN --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                    Country of Origin
                </h4>

                <div class="max-w-md">
                    <select name="country_id" class="input w-full">
                        <option value="">Select a country</option>

                        @foreach($countries as $country)
                            <option
                                value="{{ $country->id }}"
                                {{ old('country_id', $product->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Выберите страну, из которой поставляется товар.
                </p>
            </div>

        </div>
    </div>


    {{-- =====================================================
        PAYMENT
    ====================================================== --}}

    <div class="mt-6 bg-white border border-gray-200 rounded shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">

                

                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Payment
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Configure accepted payment methods and payment terms.
                    </p>
                </div>

            </div>
        </div>

        <div class="p-6 space-y-8">

           {{-- PAYMENT METHODS --}}
@php
    $defaultPaymentMethodIds = collect($defaultPaymentMethodIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    $productPaymentMethodIds = collect($productPaymentMethodIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    $selectedPaymentMethodIds = collect(
        old(
            'payment_methods',
            !empty($productPaymentMethodIds)
                ? $productPaymentMethodIds
                : $defaultPaymentMethodIds
        )
    )
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    $selectedPaymentMethods = $paymentMethods->filter(
        fn ($method) => in_array(
            (int) $method->id,
            $selectedPaymentMethodIds,
            true
        )
    );
@endphp

<div>
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <h4 class="text-sm font-semibold text-gray-900">
                Payment Methods
            </h4>

            <x-help-tooltip width="w-80">
                <div class="space-y-2 leading-relaxed">
                    <div class="font-semibold text-white">Payment Methods</div>
                    <div class="text-sm text-gray-200">
                        Выберите способы оплаты, доступные покупателю для этого товара.
                    </div>
                    <div class="text-xs text-gray-300">
                        Можно выбрать несколько способов оплаты. Изменения применяются только к этому товару.
                    </div>
                </div>
            </x-help-tooltip>
        </div>

        <button
            type="button"
            data-open-payment-methods
            class="shrink-0 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
        >
            Manage
        </button>
    </div>

    <div id="product-payment-method-summary" class="space-y-2">
        @forelse ($selectedPaymentMethods as $paymentMethod)
            @php
                $translation = $paymentMethod->translations
                    ->firstWhere('locale', app()->getLocale());

                if (!$translation) {
                    $translation = $paymentMethod->translations
                        ->firstWhere('locale', 'en');
                }
            @endphp

            <div
                data-summary-method="{{ $paymentMethod->id }}"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3"
            >
                <div class="flex h-10 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-white p-2">
                    @if (!empty($paymentMethod->icon_svg))
                        <div class="flex h-full w-full items-center justify-center [&>svg]:h-auto [&>svg]:w-auto [&>svg]:max-h-full [&>svg]:max-w-full">
                            {!! $paymentMethod->icon_svg !!}
                        </div>
                    @else
                        <svg class="h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-emerald-800">
                        {{ $translation?->name ?? $paymentMethod->code }}
                    </div>

                    @if ($translation?->description)
                        <div class="mt-0.5 line-clamp-2 text-xs text-gray-500">
                            {{ $translation->description }}
                        </div>
                    @endif
                </div>

                <svg class="h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @empty
            <p class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                No payment methods selected.
            </p>
        @endforelse
    </div>

    <p class="mt-3 text-sm text-gray-500">
        Manage payment methods available for this product.
    </p>
    
</div>

{{-- Product Payment Methods Drawer Overlay --}}
<div
    id="product-payment-methods-overlay"
    class="fixed inset-0 z-100 hidden bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"
></div>

{{-- Product Payment Methods Drawer --}}
<aside
    id="product-payment-methods-drawer"
    class="fixed right-0 top-0 z-50 flex h-full w-full max-w-[460px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="product-payment-methods-title"
    aria-hidden="true"
>
    {{-- Header --}}
    <div class="border-b bg-gray-50 px-6 py-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 id="product-payment-methods-title" class="text-lg font-semibold text-gray-900">
                    Payment Methods
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Choose payment methods for this product.
                </p>
            </div>

            <button
                type="button"
                data-close-product-payment-methods
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                aria-label="Close"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m18 6-12 12M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="border-b border-gray-200 px-6 py-4">
        <label for="product-payment-method-search" class="sr-only">
            Search payment methods
        </label>

        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path d="m16 16 4 4"/>
            </svg>

            <input
                id="product-payment-method-search"
                type="search"
                placeholder="Search payment methods..."
                class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
            >
        </div>

        <p class="mt-3 text-xs text-gray-400">
            <span id="product-payment-method-count">{{ count($selectedPaymentMethodIds) }}</span> selected
        </p>
    </div>

    {{-- List --}}
    <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5">
        <div id="product-payment-method-options" class="space-y-3">
            @foreach ($paymentMethods as $paymentMethod)
                @php
                    $translation = $paymentMethod->translations
                        ->firstWhere('locale', app()->getLocale());

                    if (!$translation) {
                        $translation = $paymentMethod->translations
                            ->firstWhere('locale', 'en');
                    }

                    $methodName = $translation?->name ?? $paymentMethod->code;
                    $methodDescription = $translation?->description ?? '';
                    $isSelected = in_array(
                        (int) $paymentMethod->id,
                        $selectedPaymentMethodIds,
                        true
                    );
                @endphp

                <label
                    data-product-payment-option
                    data-option-search="{{ \Illuminate\Support\Str::lower($methodName . ' ' . $methodDescription) }}"
                    class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition {{ $isSelected ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}"
                >
                    <input
                        type="checkbox"
                        name="payment_methods[]"
                        value="{{ $paymentMethod->id }}"
                        data-product-payment-checkbox
                        class="sr-only"
                        @checked($isSelected)
                    >

                    <div class="flex h-12 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-white p-2">
                        @if (!empty($paymentMethod->icon_svg))
                            <div class="flex h-full w-full items-center justify-center [&>svg]:h-auto [&>svg]:w-auto [&>svg]:max-h-full [&>svg]:max-w-full">
                                {!! $paymentMethod->icon_svg !!}
                            </div>
                        @else
                            <svg class="h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold {{ $isSelected ? 'text-emerald-800' : 'text-gray-900' }}">
                            {{ $methodName }}
                        </div>

                        @if ($methodDescription)
                            <div class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-gray-500">
                                {{ $methodDescription }}
                            </div>
                        @endif
                    </div>

                    <span
                        data-product-payment-check
                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition {{ $isSelected ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 bg-white text-transparent' }}"
                    >
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </label>
            @endforeach
        </div>

        <p id="product-payment-method-no-results" class="hidden py-8 text-center text-sm text-gray-400">
            No matching payment methods found.
        </p>
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between gap-4 border-t bg-white px-6 py-4">
        <button
            type="button"
            data-close-product-payment-methods
            class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50"
        >
            Cancel
        </button>

        <button
            type="button"
            id="product-payment-methods-done"
            class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white shadow-sm transition hover:bg-gray-800"
        >
            Done
        </button>
    </div>
</aside>


            <div class="border-t border-gray-100"></div>

            
{{-- PAYMENT TERMS --}}
@php
    $defaultPaymentTermIds = collect($defaultPaymentTermIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    $productPaymentTermIds = collect($productPaymentTermIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    $selectedPaymentTermId = (int) old(
    'payment_term_id',
    $productPaymentTermIds[0] ?? $defaultPaymentTermIds[0] ?? 0
);

$selectedPaymentTermIds = $selectedPaymentTermId
    ? [$selectedPaymentTermId]
    : [];

    $selectedPaymentTerms = $paymentTerms->filter(
        fn ($term) => in_array(
            (int) $term->id,
            $selectedPaymentTermIds,
            true
        )
    );

    $getPaymentTermTranslation = function ($paymentTerm) {
        return $paymentTerm->translations->firstWhere('locale', app()->getLocale())
            ?? $paymentTerm->translations->firstWhere('locale', 'en');
    };
@endphp

<div class="mt-8">
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <h4 class="text-sm font-semibold text-gray-900">
                Payment Terms
            </h4>

            <x-help-tooltip width="w-80">
                <div class="space-y-2 leading-relaxed">
                    <div class="font-semibold text-white">Payment Terms</div>
                    <div class="text-sm text-gray-200">
                        Укажите условия оплаты, доступные для этого товара.
                    </div>
                    <div class="text-xs text-gray-300">
                        Например: 100% предоплата, 50/50, Net 30 или условия по договорённости.
                    </div>
                </div>
            </x-help-tooltip>
        </div>

        <button
            type="button"
            data-open-payment-terms
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Manage
        </button>
    </div>

    <div
        id="selected-payment-terms"
        class="space-y-2"
        data-empty-text="No payment terms selected"
    >
        @forelse($selectedPaymentTerms as $paymentTerm)
            @php
                $translation = $getPaymentTermTranslation($paymentTerm);
            @endphp

            <div
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-4 py-3"
                data-selected-term="{{ $paymentTerm->id }}"
            >
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium text-gray-900" data-term-name>
                        {{ $translation?->name ?? $paymentTerm->code }}
                    </div>

                    @if($translation?->description)
                        <div class="mt-0.5 text-xs text-gray-500" data-term-description>
                            {{ $translation->description }}
                        </div>
                    @endif
                </div>

                <svg class="h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                </svg>
            </div>
        @empty
            <div
                class="rounded-xl border border-dashed border-gray-300 px-4 py-5 text-sm text-gray-500"
                data-empty-terms
            >
                No payment terms selected
            </div>
        @endforelse
    </div>

    <p class="mt-3 text-sm text-gray-500">
        Выберите условия оплаты, доступные для этого товара.
    </p>
</div>

{{-- PAYMENT TERMS DRAWER --}}
<div
    id="product-payment-terms-overlay"
    class="fixed inset-0 z-100 hidden bg-black/40 opacity-0 backdrop-blur-sm transition-opacity duration-300"
></div>

<aside
    id="product-payment-terms-drawer"
    class="fixed right-0 top-0 z-50 flex h-full w-full max-w-[460px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    aria-hidden="true"
>
    <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Payment Terms
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Choose payment terms for this product.
                </p>
            </div>

            <button
                type="button"
                data-close-payment-terms
                class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-200 hover:text-gray-900"
                aria-label="Close"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18 6-12 12M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mt-5">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path stroke-linecap="round" d="m16 16 4 4"/>
                </svg>

                <input
                    id="payment-terms-search"
                    type="search"
                    placeholder="Search payment terms..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                >
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-5">
        <div id="payment-terms-options" class="space-y-2">
            @foreach($paymentTerms as $paymentTerm)
                @php
                    $translation = $getPaymentTermTranslation($paymentTerm);
                    $isSelected = in_array(
                        (int) $paymentTerm->id,
                        $selectedPaymentTermIds,
                        true
                    );
                @endphp

                <label
                    data-term-option
                    data-id="{{ $paymentTerm->id }}"
                    data-name="{{ $translation?->name ?? $paymentTerm->code }}"
                    data-description="{{ $translation?->description ?? '' }}"
                    class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition {{ $isSelected ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}"
                >
                    <input
    type="radio"
    name="payment_term_id"
    value="{{ $paymentTerm->id }}"
    data-term-checkbox
    class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500"
    {{ $isSelected ? 'checked' : '' }}
>

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium text-gray-900">
                            {{ $translation?->name ?? $paymentTerm->code }}
                        </span>

                        @if($translation?->description)
                            <span class="mt-0.5 block text-xs text-gray-500">
                                {{ $translation->description }}
                            </span>
                        @endif
                    </span>

                    <svg
                        data-term-indicator
                        class="h-5 w-5 shrink-0 text-emerald-600 {{ $isSelected ? '' : 'invisible' }}"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                    </svg>
                </label>
            @endforeach
        </div>

        <p id="payment-terms-no-results" class="hidden py-8 text-center text-sm text-gray-500">
            No payment terms found.
        </p>
    </div>

    <div class="flex items-center justify-between gap-4 border-t border-gray-200 bg-white px-6 py-4">
        <span class="text-sm text-gray-500">
            <span id="payment-terms-selected-count">{{ count($selectedPaymentTermIds) }}</span>
            selected
        </span>

        <div class="flex items-center gap-3">
            <button
                type="button"
                data-close-payment-terms
                class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Cancel
            </button>

            <button
                type="button"
                data-done-payment-terms
                class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800"
            >
                Done
            </button>
        </div>
    </div>
</aside>







        </div>
    </div>


    {{-- =====================================================
        RETURNS & REFUNDS
    ====================================================== --}}

    @php
        $currentReturnPolicy = $productReturnPolicy ?? null;

        $useSupplierDefault = old(
            'use_supplier_default',
            $currentReturnPolicy?->use_supplier_default ?? true
        );

        $selectedReturnPolicyId = old(
            'return_policy_id',
            $currentReturnPolicy?->return_policy_id
        );

        $returnPolicyTranslation = function ($policy) {
            return $policy->translation(app()->getLocale())
                ?? $policy->translation('en');
        };

        $supplierDefaultPolicy = null;

        if (!empty($supplierDefaultReturnPolicyId)) {
            $supplierDefaultPolicy = collect($returnPolicies ?? [])
                ->firstWhere('id', $supplierDefaultReturnPolicyId);
        }

        $supplierDefaultPolicyTranslation = null;

        if ($supplierDefaultPolicy) {
            $supplierDefaultPolicyTranslation =
                $returnPolicyTranslation($supplierDefaultPolicy);
        }

        $supplierDefaultPolicyName =
            $supplierDefaultPolicyTranslation?->name
            ?: $supplierDefaultPolicy?->name;

        $supplierDefaultPolicyDescription =
            $supplierDefaultPolicyTranslation?->description
            ?: null;

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
        class="mt-6 bg-white border border-gray-200 rounded shadow-sm overflow-hidden"
        x-data="{
            useSupplierDefault: {{ $useSupplierDefault ? 'true' : 'false' }},
            selectedPolicyId: '{{ $selectedReturnPolicyId ?? '' }}'
        }">

        {{-- HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">

                

                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Returns & Refunds
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Choose the return policy that applies to this product.
                    </p>
                </div>

            </div>
        </div>

        <div class="p-6">

            {{-- POLICY MODE --}}
            <div class="space-y-3">

                {{-- USE SUPPLIER DEFAULT --}}
                <label
                    class="block cursor-pointer"
                    @click="
                        useSupplierDefault = true;
                        selectedPolicyId = '';
                    ">

                    <input
                        type="radio"
                        name="use_supplier_default"
                        value="1"
                        class="sr-only"
                        x-model="useSupplierDefault"
                        @checked($useSupplierDefault)>

                    <div
                        class="rounded-xl border p-4 transition"
                        :class="useSupplierDefault
                            ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                            : 'border-gray-200 bg-white hover:border-gray-300'">

                        <div class="flex items-start gap-3">

                            <div
                                class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                                :class="useSupplierDefault
                                    ? 'border-gray-900'
                                    : 'border-gray-300'">

                                <div
                                    x-show="useSupplierDefault"
                                    class="w-2.5 h-2.5 rounded-full bg-gray-900">
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="text-sm font-semibold text-gray-900">
                                    Use supplier default
                                </div>

                                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                    Use the default return policy configured for the supplier.
                                </p>

                                @if($supplierDefaultPolicy)

                                    <div class="mt-4 rounded-xl border border-gray-200 bg-white p-3">

                                        <div class="flex items-start justify-between gap-4">

                                            <div class="min-w-0">

                                                <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
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

                                            <span class="shrink-0 rounded-lg bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                {{ $supplierDefaultPolicy->code }}
                                            </span>

                                        </div>

                                        <div class="mt-3 flex flex-wrap items-center gap-2">

                                            {{-- Return Window --}}
                                            <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200">
                                                @if($supplierDefaultPolicy->return_window_days !== null)
                                                    {{ $supplierDefaultPolicy->return_window_days }} days
                                                @else
                                                    No time limit
                                                @endif
                                            </span>

                                            {{-- Shipping Payer --}}
                                            @if($supplierDefaultShippingPayer)
                                                <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200">
                                                    {{ $supplierDefaultShippingPayer }}
                                                </span>
                                            @endif

                                            {{-- Restocking Fee --}}
                                            @if($supplierDefaultPolicy->restocking_fee_enabled)
                                                <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200">
                                                    Restocking

                                                    @if($supplierDefaultPolicy->restocking_fee_percent !== null)
                                                        {{ $supplierDefaultPolicy->restocking_fee_percent }}%
                                                    @endif
                                                </span>
                                            @endif

                                            {{-- Custom Products --}}
                                            @if($supplierDefaultPolicy->custom_products_returnable)
                                                <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 border border-gray-200">
                                                    Custom products allowed
                                                </span>
                                            @endif

                                        </div>
                                    </div>

                                @else

                                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                                        <div class="text-xs font-medium text-amber-700">
                                            No supplier default return policy has been configured.
                                        </div>
                                    </div>

                                @endif

                            </div>
                        </div>
                    </div>
                </label>


                {{-- SELECT POLICY FOR THIS PRODUCT --}}
                <label
                    class="block cursor-pointer"
                    @click="useSupplierDefault = false">

                    <input
                        type="radio"
                        name="use_supplier_default"
                        value="0"
                        class="sr-only"
                        x-model="useSupplierDefault"
                        @checked(!$useSupplierDefault)>

                    <div
                        class="rounded-xl border p-4 transition"
                        :class="!useSupplierDefault
                            ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                            : 'border-gray-200 bg-white hover:border-gray-300'">

                        <div class="flex items-start gap-3">

                            <div
                                class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                                :class="!useSupplierDefault
                                    ? 'border-gray-900'
                                    : 'border-gray-300'">

                                <div
                                    x-show="!useSupplierDefault"
                                    class="w-2.5 h-2.5 rounded-full bg-gray-900">
                                </div>
                            </div>

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


            {{-- PLATFORM POLICIES --}}
            <div
                x-show="!useSupplierDefault"
                x-transition
                class="mt-5">

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
                                    x-model="selectedPolicyId">

                                <div
                                    class="rounded-xl border p-4 transition"
                                    :class="selectedPolicyId == '{{ $policy->id }}'
                                        ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                                        : 'border-gray-200 bg-white hover:border-gray-300'">

                                    <div class="flex items-start justify-between gap-4">

                                        <div class="flex items-start gap-3 min-w-0">

                                            <div
                                                class="mt-0.5 w-5 h-5 rounded-full border flex items-center justify-center shrink-0"
                                                :class="selectedPolicyId == '{{ $policy->id }}'
                                                    ? 'border-gray-900'
                                                    : 'border-gray-300'">

                                                <div
                                                    x-show="selectedPolicyId == '{{ $policy->id }}'"
                                                    class="w-2.5 h-2.5 rounded-full bg-gray-900">
                                                </div>
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

                                    {{-- POLICY SUMMARY --}}
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

                                            {{-- Return Shipping --}}
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

                                        {{-- RETURN REASONS --}}
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

                                        {{-- RESOLUTIONS --}}
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


    {{-- =====================================================
        NAVIGATION & ACTIONS
    ====================================================== --}}

    <div class="flex justify-between mt-6">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 7]) }}"
            class="mt-4 bg-gray-50 border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-2 rounded">
            Previous
        </a>

        <div class="flex gap-3">

            <button
                type="submit"
                form="productForm"
                formaction="{{ route('supplier.products.save-draft', $product->id) }}"
                formmethod="POST"
                class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500">
                Save
            </button>

        </div>
    </div>



   {{-- FLOATING SAVE BUTTON --}}
<div
    x-data="{ visible: false }"
    x-init="visible = window.scrollY > 200"
    @scroll.window="visible = window.scrollY > 200"
    x-show="visible"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed right-6 top-1/2 -translate-y-1/2 z-40"
    style="display: none;"
>
    <button
        type="submit"
        form="productForm"
        formaction="{{ route('supplier.products.save-draft', $product->id) }}"
        formmethod="POST"
        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg transition hover:bg-blue-500"
    >
        Save
    </button>
</div>


</form>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('product-payment-methods-overlay');
    const drawer = document.getElementById('product-payment-methods-drawer');
    const search = document.getElementById('product-payment-method-search');
    const options = document.getElementById('product-payment-method-options');
    const count = document.getElementById('product-payment-method-count');
    const noResults = document.getElementById('product-payment-method-no-results');
    const summary = document.getElementById('product-payment-method-summary');
    const openButton = document.querySelector('[data-open-payment-methods]');
    const doneButton = document.getElementById('product-payment-methods-done');

    if (!overlay || !drawer || !options || !summary) return;

    let previousFocus = null;

    function getCheckboxes() {
        return Array.from(options.querySelectorAll('[data-product-payment-checkbox]'));
    }

    function updateOption(checkbox) {
        const row = checkbox.closest('[data-product-payment-option]');
        const check = row.querySelector('[data-product-payment-check]');
        const name = row.querySelector('.text-sm.font-semibold');
        const selected = checkbox.checked;

        row.classList.toggle('border-emerald-200', selected);
        row.classList.toggle('bg-emerald-50', selected);
        row.classList.toggle('border-gray-200', !selected);
        row.classList.toggle('bg-white', !selected);
        row.classList.toggle('hover:border-gray-300', !selected);
        row.classList.toggle('hover:bg-gray-50', !selected);

        name.classList.toggle('text-emerald-800', selected);
        name.classList.toggle('text-gray-900', !selected);

        check.classList.toggle('border-emerald-600', selected);
        check.classList.toggle('bg-emerald-600', selected);
        check.classList.toggle('text-white', selected);
        check.classList.toggle('border-gray-300', !selected);
        check.classList.toggle('bg-white', !selected);
        check.classList.toggle('text-transparent', !selected);
    }

    function updateCount() {
        count.textContent = getCheckboxes().filter(input => input.checked).length;
    }

    function updateSummary() {
        summary.replaceChildren();

        const selected = getCheckboxes().filter(input => input.checked);

        if (!selected.length) {
            const empty = document.createElement('p');
            empty.className = 'rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500';
            empty.textContent = 'No payment methods selected.';
            summary.appendChild(empty);
            return;
        }

        selected.forEach(input => {
            const row = input.closest('[data-product-payment-option]');
            const name = row.querySelector('.text-sm.font-semibold').textContent.trim();
            const description = row.querySelector('.mt-0\\.5')?.textContent.trim() || '';
            const icon = row.querySelector('.flex.h-12.w-16')?.cloneNode(true);

            const item = document.createElement('div');
            item.dataset.summaryMethod = input.value;
            item.className = 'flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3';

            if (icon) item.appendChild(icon);

            const text = document.createElement('div');
            text.className = 'min-w-0 flex-1';

            const title = document.createElement('div');
            title.className = 'text-sm font-semibold text-emerald-800';
            title.textContent = name;
            text.appendChild(title);

            if (description) {
                const detail = document.createElement('div');
                detail.className = 'mt-0.5 line-clamp-2 text-xs text-gray-500';
                detail.textContent = description;
                text.appendChild(detail);
            }

            item.appendChild(text);

            const tick = document.createElement('span');
            tick.className = 'text-emerald-600';
            tick.innerHTML = '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z" clip-rule="evenodd"/></svg>';
            item.appendChild(tick);

            summary.appendChild(item);
        });
    }

    function openDrawer() {
        previousFocus = document.activeElement;
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            drawer.classList.remove('translate-x-full');
            drawer.setAttribute('aria-hidden', 'false');
            search?.focus();
        });
    }

    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.add('opacity-0');

        window.setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            previousFocus?.focus?.();
        }, 300);
    }

    getCheckboxes().forEach(input => {
    updateOption(input);

    input.addEventListener('change', () => {
        // Обновляем визуальное состояние всех методов,
        // включая метод, с которого сняли выбор.
        getCheckboxes().forEach(checkbox => {
            updateOption(checkbox);
        });

        updateCount();
        updateSummary();
    });
});


    openButton?.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);

    document.querySelectorAll('[data-close-product-payment-methods]').forEach(button => {
        button.addEventListener('click', closeDrawer);
    });

    doneButton?.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') {
            closeDrawer();
        }
    });

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        options.querySelectorAll('[data-product-payment-option]').forEach(row => {
            const visible = (row.dataset.optionSearch || '').includes(query);
            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        noResults?.classList.toggle('hidden', visibleCount > 0);
    });

    updateCount();
    updateSummary();

    
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('product-payment-terms-overlay');
    const drawer = document.getElementById('product-payment-terms-drawer');
    const search = document.getElementById('payment-terms-search');
    const options = document.getElementById('payment-terms-options');
    const count = document.getElementById('payment-terms-selected-count');
    const noResults = document.getElementById('payment-terms-no-results');
    const summary = document.getElementById('selected-payment-terms');
    const openButton = document.querySelector('[data-open-payment-terms]');
    const doneButton = document.querySelector('[data-done-payment-terms]');

    if (!overlay || !drawer || !options || !summary || !openButton) {
        console.warn('Payment Terms drawer: required elements not found.');
        return;
    }

    let previousFocus = null;
    let closeTimer = null;

    const getInputs = () =>
        Array.from(options.querySelectorAll('[data-term-checkbox]'));

    function updateOption(input) {
        const row = input.closest('[data-term-option]');
        if (!row) return;

        const indicator = row.querySelector('[data-term-indicator]');
        const selected = input.checked;

        row.classList.toggle('border-emerald-200', selected);
        row.classList.toggle('bg-emerald-50', selected);
        row.classList.toggle('border-gray-200', !selected);
        row.classList.toggle('bg-white', !selected);
        row.classList.toggle('hover:border-gray-300', !selected);
        row.classList.toggle('hover:bg-gray-50', !selected);

        indicator?.classList.toggle('invisible', !selected);
    }

    function updateAllOptions() {
        getInputs().forEach(updateOption);
    }

    function updateCount() {
        if (count) {
            count.textContent = getInputs().filter(input => input.checked).length;
        }
    }

    function updateSummary() {
        summary.replaceChildren();

        const selected = getInputs().filter(input => input.checked);

        if (!selected.length) {
            const empty = document.createElement('div');
            empty.className =
                'rounded-xl border border-dashed border-gray-300 px-4 py-5 text-sm text-gray-500';
            empty.textContent =
                summary.dataset.emptyText || 'No payment terms selected';

            summary.appendChild(empty);
            return;
        }

        selected.forEach(input => {
            const row = input.closest('[data-term-option]');
            if (!row) return;

            const name = row.dataset.name || input.value;
            const description = row.dataset.description || '';

            const item = document.createElement('div');
            item.dataset.selectedTerm = input.value;
            item.className =
                'flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-4 py-3';

            const icon = document.createElement('div');
            icon.className =
                'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600';
            icon.innerHTML = `
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                </svg>
            `;

            const text = document.createElement('div');
            text.className = 'min-w-0 flex-1';

            const title = document.createElement('div');
            title.className = 'text-sm font-medium text-gray-900';
            title.textContent = name;
            text.appendChild(title);

            if (description) {
                const detail = document.createElement('div');
                detail.className = 'mt-0.5 text-xs text-gray-500';
                detail.textContent = description;
                text.appendChild(detail);
            }

            const tick = document.createElement('span');
            tick.className = 'shrink-0 text-emerald-600';
            tick.innerHTML = `
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m5 12 4 4L19 6"/>
                </svg>
            `;

            item.append(icon, text, tick);
            summary.appendChild(item);
        });
    }

    function syncSelectionUI() {
        updateAllOptions();
        updateCount();
        updateSummary();
    }

    function openDrawer() {
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }

        previousFocus = document.activeElement;

        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            drawer.classList.remove('translate-x-full');
            drawer.setAttribute('aria-hidden', 'false');
            search?.focus();
        });
    }

    function closeDrawer() {
        if (drawer.getAttribute('aria-hidden') === 'true') return;

        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.add('opacity-0');

        closeTimer = window.setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            previousFocus?.focus?.();
            closeTimer = null;
        }, 300);
    }

    // Initialize selection styling.
    syncSelectionUI();

    // Radio selection: refresh every row when the selected term changes.
    getInputs().forEach(input => {
        input.addEventListener('change', () => {
            syncSelectionUI();
        });
    });

    openButton.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);

    drawer.querySelectorAll('[data-close-payment-terms]').forEach(button => {
        button.addEventListener('click', closeDrawer);
    });

    doneButton?.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            drawer.getAttribute('aria-hidden') === 'false'
        ) {
            closeDrawer();
        }
    });

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        options.querySelectorAll('[data-term-option]').forEach(row => {
            const name = row.dataset.name || '';
            const description = row.dataset.description || '';
            const text = `${name} ${description}`.toLocaleLowerCase();
            const visible = text.includes(query);

            row.classList.toggle('hidden', !visible);

            if (visible) visibleCount++;
        });

        noResults?.classList.toggle('hidden', visibleCount > 0);
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('shipping-templates-overlay');
    const drawer = document.getElementById('shipping-templates-drawer');
    const options = document.getElementById('shipping-templates-options');
    const search = document.getElementById('shipping-templates-search');
    const openButton = document.getElementById('open-shipping-templates');
    const doneButton = document.querySelector('[data-done-shipping-templates]');
    const summary = document.getElementById('selected-seller-shipping-templates');
    const empty = document.getElementById('shipping-templates-empty');
    const countBadge = document.getElementById('shipping-templates-count');
    const countLabel = document.getElementById('shipping-templates-selected-count');
    const noResults = document.getElementById('shipping-templates-no-results');

    if (!overlay || !drawer || !options || !summary) return;

    let previousFocus = null;
    let closeTimer = null;

    const getCheckboxes = () =>
        Array.from(options.querySelectorAll('[data-shipping-template-checkbox]'));

    function updateOption(checkbox) {
        const row = checkbox.closest('[data-shipping-template-option]');
        if (!row) return;

        const indicator = row.querySelector('[data-shipping-template-indicator]');
        const selected = checkbox.checked;

        row.classList.toggle('border-emerald-200', selected);
        row.classList.toggle('bg-emerald-50', selected);
        row.classList.toggle('border-gray-200', !selected);
        row.classList.toggle('bg-white', !selected);
        row.classList.toggle('hover:border-gray-300', !selected);
        row.classList.toggle('hover:bg-gray-50', !selected);

        indicator?.classList.toggle('invisible', !selected);
    }

    function updateSummary() {
    summary.replaceChildren();

    const selected = getCheckboxes().filter(checkbox => checkbox.checked);

    selected.forEach(checkbox => {
        const row = checkbox.closest('[data-shipping-template-option]');
        const details = row?.querySelector('.shipping-template-details');

        if (!details) return;

        const card = document.createElement('div');
        card.dataset.selectedShippingTemplate = checkbox.value;
        card.className =
            'relative overflow-hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm';

        const accent = document.createElement('div');
        accent.className = 'absolute inset-y-0 left-0 w-1 bg-emerald-500';

        const content = document.createElement('div');
        content.className = 'pl-2';
        content.appendChild(details.cloneNode(true));

        content.querySelectorAll('.font-semibold.text-gray-900').forEach(title => {
    title.classList.remove('text-gray-900');
    title.classList.add('text-emerald-900');
});


        card.append(accent, content);
        summary.appendChild(card);
    });

    empty?.classList.toggle('hidden', selected.length > 0);

    if (countBadge) countBadge.textContent = selected.length;
    if (countLabel) countLabel.textContent = selected.length;
}

    function syncSelectionUI() {
        getCheckboxes().forEach(updateOption);
        updateSummary();
    }

    function openDrawer() {
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }

        previousFocus = document.activeElement;

        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            drawer.classList.remove('translate-x-full');
            drawer.setAttribute('aria-hidden', 'false');
            overlay.setAttribute('aria-hidden', 'false');
            search?.focus();
        });
    }

    function closeDrawer() {
        if (drawer.getAttribute('aria-hidden') === 'true') return;

        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.add('opacity-0');
        overlay.setAttribute('aria-hidden', 'true');

        closeTimer = window.setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            previousFocus?.focus?.();
            closeTimer = null;
        }, 300);
    }

    // Initialize the selected state and visible summary.
    syncSelectionUI();

    // Refresh every row after selecting or deselecting a template.
    getCheckboxes().forEach(checkbox => {
        checkbox.addEventListener('change', syncSelectionUI);
    });

    openButton?.addEventListener('click', openDrawer);

    overlay.addEventListener('click', event => {
        if (event.target === overlay) closeDrawer();
    });

    drawer.querySelectorAll('[data-close-shipping-templates]').forEach(button => {
        button.addEventListener('click', closeDrawer);
    });

    doneButton?.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            drawer.getAttribute('aria-hidden') === 'false'
        ) {
            closeDrawer();
        }
    });

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        options.querySelectorAll('[data-shipping-template-option]').forEach(row => {
            const text = (row.dataset.search || '').toLocaleLowerCase();
            const visible = text.includes(query);

            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        noResults?.classList.toggle('hidden', visibleCount > 0);
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-done-payment-terms]').forEach(function (button) {
        button.addEventListener('click', function () {
            // Здесь вызывается существующее действие кнопки Done.
            // Например, закрытие панели Payment Terms.
        });
    });
});
</script>