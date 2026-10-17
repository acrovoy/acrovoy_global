
@extends('dashboard.admin.layout')

@section('dashboard-content')

@php
    $statusClasses = match ($product1->status) {
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'rejected' => 'border-red-200 bg-red-50 text-red-800',
        'draft' => 'border-stone-200 bg-stone-100 text-stone-700',
        'archived' => 'border-stone-300 bg-stone-200 text-stone-600',
        default => 'border-stone-200 bg-stone-50 text-stone-700',
    };

    $variantItems = $product1->variantGroup?->items ?? collect();
    $returnPolicy = $productReturnPolicy?->returnPolicy;
    $dimensions = $product1->shippingDimensions;

    $returnFields = $productReturnPolicy ? [
        'Returnable' => $productReturnPolicy->returnable,
        'Return window (days)' => $productReturnPolicy->return_window_days,
        'Return shipping payer' => $productReturnPolicy->return_shipping_payer,
        'Restocking fee enabled' => $productReturnPolicy->restocking_fee_enabled,
        'Restocking fee (%)' => $productReturnPolicy->restocking_fee_percent,
        'Custom products returnable' => $productReturnPolicy->custom_products_returnable,
        'Additional information' => $productReturnPolicy->additional_information,
    ] : [];
@endphp

<div class="mx-auto max-w-screen-2xl space-y-6">

    {{-- =========================================================
         HEADER AND MODERATION ACTIONS
    ========================================================== --}}
    <div class="flex flex-col gap-4">
        <a
            href="{{ route('admin.products.index') }}"
            class="inline-flex w-fit items-center gap-2 text-sm text-stone-500 transition hover:text-stone-900"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m15 18-6-6 6-6M9 12h12" />
            </svg>
            Back to products
        </a>

        <div class="flex flex-col gap-4 rounded-xl border border-stone-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">

            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-stone-500">Current status</span>

                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                    {{ ucfirst($product1->status ?? 'Unknown') }}
                </span>

                <span class="text-xs text-stone-400">
                    Product #{{ $product1->id }}
                </span>
            </div>

            {{-- Keep existing approval and rejection routes --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                {{-- Approve --}}
                <form
                    method="POST"
                    action="{{ route('admin.products.approve', $product1->id) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-stone-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2 sm:w-auto"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m5 12 4 4L19 6" />
                        </svg>
                        Approve
                    </button>
                </form>

                {{-- Reject --}}
                <form
                    method="POST"
                    action="{{ route('admin.products.reject', $product1->id) }}"
                    class="flex flex-col gap-2 sm:flex-row"
                >
                    @csrf

                    <input
                        type="text"
                        name="reject_reason"
                        value="{{ old('reject_reason') }}"
                        placeholder="Reason for rejection"
                        maxlength="1000"
                        required
                        class="min-w-0 rounded-lg border border-stone-300 px-3 py-2.5 text-sm text-stone-800 placeholder:text-stone-400 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100 sm:w-56"
                    >

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 6l12 12M18 6 6 18" />
                        </svg>
                        Reject
                    </button>
                </form>
            </div>

            <div class="border-t border-stone-100 pt-3 text-xs text-stone-500 lg:border-l lg:border-t-0 lg:pl-4 lg:pt-0">
                <p>Supplier language</p>
                <p class="mt-1 font-medium text-stone-800">
                    {{ $supplierLanguage?->name ?? $product1->supplier?->language ?? 'Not specified' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Please review the following errors:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Editing-copy notice --}}
    @if ($originalProduct)
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="text-sm font-semibold text-blue-900">
                Submitted changes to an existing product
            </p>
            <p class="mt-1 text-sm text-blue-800">
                This submission is an editing copy of product
                #{{ $originalProduct->id }}.
                The original product remains unchanged until these changes are approved.
            </p>
            <p class="mt-2 text-sm font-medium text-blue-900">
                Original title: {{ $originalProduct->name ?? 'Untitled product' }}
            </p>
        </div>
    @endif

    {{-- =========================================================
         PRODUCT TITLE AND QUICK DETAILS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">
                        Product submission
                    </p>

                    <h1 class="mt-2 break-words text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl">
                        {{ $product1->name ?: 'Untitled product' }}
                    </h1>

                    @if ($product1->undername)
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-stone-600">
                            {{ $product1->undername }}
                        </p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                            SKU: {{ $product1->sku ?: 'Not provided' }}
                        </span>

                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                            Category: {{ $product1->category?->name ?? 'Not selected' }}
                        </span>

                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                            Supplier: {{ $product1->supplier?->name ?? 'Unknown' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:min-w-96">
                    <div class="rounded-lg bg-stone-50 p-3">
                        <p class="text-xs text-stone-500">Product ID</p>
                        <p class="mt-1 text-lg font-semibold text-stone-900">
                            #{{ $product1->id }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-stone-50 p-3">
                        <p class="text-xs text-stone-500">Price tiers</p>
                        <p class="mt-1 text-lg font-semibold text-stone-900">
                            {{ $product1->priceTiers->count() }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-stone-50 p-3">
                        <p class="text-xs text-stone-500">Specifications</p>
                        <p class="mt-1 text-lg font-semibold text-stone-900">
                            {{ $product1->attributeValues->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if ($product1->status === 'rejected' && $product1->reject_reason)
            <div class="border-t border-red-100 bg-red-50 px-5 py-4 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                    Rejection reason
                </p>
                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-red-900">
                    {{ $product1->reject_reason }}
                </p>
            </div>
        @endif
    </section>

    {{-- =========================================================
         GALLERY AND PRODUCT RECORD
    ========================================================== --}}
    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-3">

        {{-- Gallery --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Product images
                </h2>
                <span class="text-xs text-stone-400">
                    {{ $images->count() }} files
                </span>
            </div>

            <div class="p-4">
                @if ($images->isNotEmpty())
                    <div class="flex h-72 items-center justify-center overflow-hidden rounded-lg bg-stone-50">
                        <img
                            id="adminMainImage"
                            src="{{ $mainImage?->cdn_url ?? asset('images/no-image.png') }}"
                            alt="{{ $product1->name ?? 'Product image' }}"
                            class="h-full w-full object-contain"
                        >
                    </div>

                    <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-5 xl:grid-cols-4">
                        @foreach ($images as $image)
                            <button
                                type="button"
                                class="admin-image-thumbnail flex h-16 items-center justify-center overflow-hidden rounded-lg border bg-white p-1 transition hover:border-stone-500 {{ $image->id === $mainImage?->id ? 'border-stone-700' : 'border-stone-200' }}"
                                data-src="{{ $image->cdn_url }}"
                                aria-label="View product image {{ $loop->iteration }}"
                            >
                                <img
                                    src="{{ $image->cdn_url }}"
                                    alt=""
                                    class="h-full w-full object-contain"
                                >
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="flex h-64 flex-col items-center justify-center rounded-lg border border-dashed border-stone-200 bg-stone-50 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-stone-300"
                            viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.4">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 15-5-5L5 21" />
                        </svg>
                        <p class="mt-3 text-sm font-medium text-stone-600">
                            No images uploaded
                        </p>
                        <p class="mt-1 text-xs text-stone-400">
                            The supplier did not add a product gallery.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Product record --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Product record
                </h2>
                <p class="mt-1 text-xs text-stone-500">
                    Stored product data and supplier information
                </p>
            </div>

            <dl class="grid grid-cols-1 divide-y divide-stone-100 px-5 sm:grid-cols-2 sm:gap-x-8 sm:divide-y-0">

                @php
                    $recordFields = [
                        'SKU' => $product1->sku,
                        'Slug' => $product1->slug,
                        'Category ID' => $product1->category_id,
                        'Supplier ID' => $product1->supplier_id,
                        'Created by' => $creator?->name
                            ? $creator->name . ' (#' . $creator->id . ')'
                            : ($product1->created_by ?? 'Not recorded'),
                        'Variant group ID' => $product1->variant_group_id,
                        'Minimum order quantity' => $product1->moq,
                        'Lead time (days)' => $product1->lead_time,
                        'Customization' => $product1->customization
                            ? 'Available'
                            : 'Not available',
                        'Created at' => $product1->created_at?->format('d M Y, H:i'),
                        'Last updated' => $product1->updated_at?->format('d M Y, H:i'),
                    ];
                @endphp

                @foreach ($recordFields as $label => $value)
                    <div class="flex min-w-0 items-start justify-between gap-4 border-b border-stone-100 py-3">
                        <dt class="shrink-0 text-xs text-stone-500">
                            {{ $label }}
                        </dt>
                        <dd class="min-w-0 break-words text-right text-sm font-medium text-stone-800">
                            {{ $value !== null && $value !== '' ? $value : '—' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            <div class="border-t border-stone-100 px-5 py-4">
                <p class="text-xs text-stone-500">Product URL slug</p>
                <p class="mt-1 break-all font-mono text-xs text-stone-700">
                    {{ $product1->slug }}
                </p>
            </div>

            @can('update', $product1)
                <div class="border-t border-stone-100 px-5 py-4">
                    <a
                        href="{{ route('products.edit', $product1->id) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                    >
                        Edit product
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z" />
                        </svg>
                    </a>
                </div>
            @endcan
        </section>
    </div>

    {{-- =========================================================
         ALL PRODUCT TRANSLATIONS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-stone-900">
                Product content and translations
            </h2>
            <p class="mt-1 text-xs text-stone-500">
                All saved languages, names, subtitles and descriptions
            </p>
        </div>

        @if ($product1->translations->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th class="w-20 px-5 py-3 font-medium">Locale</th>
                            <th class="w-1/4 px-5 py-3 font-medium">Name</th>
                            <th class="w-1/4 px-5 py-3 font-medium">Subtitle</th>
                            <th class="px-5 py-3 font-medium">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($product1->translations->sortBy('locale') as $translation)
                            <tr class="align-top hover:bg-stone-50/70">
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-md border border-stone-200 bg-stone-50 px-2 py-1 text-xs font-semibold uppercase text-stone-700">
                                        {{ $translation->locale }}
                                    </span>
                                </td>
                                <td class="whitespace-pre-line break-words px-5 py-4 font-medium text-stone-900">
                                    {{ $translation->name ?: '—' }}
                                </td>
                                <td class="whitespace-pre-line break-words px-5 py-4 text-stone-700">
                                    {{ $translation->undername ?: '—' }}
                                </td>
                                <td class="max-w-2xl whitespace-pre-line break-words px-5 py-4 leading-6 text-stone-600">
                                    {{ $translation->description ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No translations have been saved.
            </p>
        @endif
    </section>

    {{-- =========================================================
         PRICE TIERS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-stone-900">
                    Pricing and quantity tiers
                </h2>
                <p class="mt-1 text-xs text-stone-500">
                    Unit prices by order quantity; shipping excluded
                </p>
            </div>
            <span class="text-xs text-stone-400">
                {{ $product1->priceTiers->count() }} tiers
            </span>
        </div>

        @if ($product1->priceTiers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px] text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Min quantity</th>
                            <th class="px-5 py-3 font-medium">Max quantity</th>
                            <th class="px-5 py-3 text-right font-medium">Unit price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($product1->priceTiers->sortBy('min_qty') as $tier)
                            <tr class="hover:bg-stone-50/70">
                                <td class="px-5 py-3 text-stone-700">
                                    {{ $tier->min_qty }} pcs
                                </td>
                                <td class="px-5 py-3 text-stone-700">
                                    {{ $tier->max_qty ?? 'No maximum' }}
                                    {{ $tier->max_qty ? 'pcs' : '' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-stone-900">
                                    ${{ number_format((float) $tier->price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No price tiers have been saved.
            </p>
        @endif
    </section>

    {{-- =========================================================
         PRODUCT SPECIFICATIONS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-stone-900">
                Product specifications
            </h2>
            <p class="mt-1 text-xs text-stone-500">
                Saved category attributes and their values
            </p>
        </div>

        @if ($product1->attributeValues->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Attribute group</th>
                            <th class="px-5 py-3 font-medium">Attribute</th>
                            <th class="px-5 py-3 font-medium">Type</th>
                            <th class="px-5 py-3 font-medium">Saved value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($product1->attributeValues->sortBy(fn ($value) => strtolower($value->attribute?->group?->name ?? 'General')) as $attributeValue)
                            @php
                                $attribute = $attributeValue->attribute;
                                $displayValue = $attributeValue->display_value;

                                if ($displayValue === null || $displayValue === '') {
                                    $valueTranslation = $attributeValue->translations->firstWhere('locale', 'en')
                                        ?? $attributeValue->translations->first();

                                    $displayValue = $valueTranslation?->value;
                                }

                                $attributeUnit = $attribute?->unit;
                            @endphp

                            <tr class="align-top hover:bg-stone-50/70">
                                <td class="px-5 py-3 text-xs text-stone-500">
                                    {{ $attribute?->group?->name ?? 'General' }}
                                </td>
                                <td class="px-5 py-3 font-medium text-stone-800">
                                    {{ $attribute?->name ?? $attribute?->code ?? ('Attribute #' . $attributeValue->attribute_id) }}
                                </td>
                                <td class="px-5 py-3 text-xs text-stone-500">
                                    {{ ucfirst($attribute?->type ?? '—') }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="whitespace-pre-line break-words font-medium text-stone-900">
                                        {{ $displayValue !== null && $displayValue !== '' ? $displayValue : 'Not provided' }}
                                    </span>

                                    @if ($attributeUnit)
                                        <span class="ml-1 text-xs text-stone-500">
                                            {{ $attributeUnit }}
                                        </span>
                                    @endif

                                    @if ($attributeValue->translations->count() > 1)
                                        <div class="mt-2 space-y-1">
                                            @foreach ($attributeValue->translations as $valueTranslation)
                                                <p class="text-xs text-stone-500">
                                                    <span class="font-semibold uppercase">
                                                        {{ $valueTranslation->locale }}:
                                                    </span>
                                                    {{ $valueTranslation->value }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No product specifications have been saved.
            </p>
        @endif
    </section>

    {{-- =========================================================
         MATERIALS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-stone-900">
                Materials
            </h2>
        </div>

        <div class="p-5">
            @if ($product1->materials->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($product1->materials as $material)
                        <div class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2">
                            <p class="text-sm font-medium text-stone-800">
                                {{ $material->name ?? ('Material #' . $material->id) }}
                            </p>
                            @if ($material->translations->isNotEmpty())
                                <div class="mt-1 space-y-1">
                                    @foreach ($material->translations as $translation)
                                        <p class="text-xs text-stone-500">
                                            {{ strtoupper($translation->locale) }}:
                                            {{ $translation->name ?? '—' }}
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-stone-500">
                    No materials selected.
                </p>
            @endif
        </div>
    </section>

    {{-- =========================================================
         VARIANTS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-stone-900">
                Variations and color options
            </h2>
            <p class="mt-1 text-xs text-stone-500">
                Variant group ID: {{ $product1->variant_group_id ?? 'Not assigned' }}
            </p>
        </div>

        @if ($variantItems->isNotEmpty())
            <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($variantItems->sortBy('sort_order') as $variantItem)
                    @php
                        $variantProduct = $variantItem->product;
                        $variantTitle = $variantItem->title
                            ?: ($variantProduct?->name ?? 'Variant');
                        $variantImage = $variantItem->media?->cdn_url
                            ?? $variantProduct?->mainImage?->cdn_url;
                    @endphp

                    <div class="flex items-start gap-3 rounded-lg border border-stone-200 p-3">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-md bg-stone-50">
                            @if ($variantImage)
                                <img
                                    src="{{ $variantImage }}"
                                    alt="{{ $variantTitle }}"
                                    class="h-full w-full object-contain"
                                >
                            @else
                                <span class="px-2 text-center text-xs text-stone-400">
                                    No image
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <p class="break-words text-sm font-medium text-stone-900">
                                {{ $variantTitle }}
                            </p>
                            <p class="mt-1 text-xs text-stone-500">
                                Variant item #{{ $variantItem->id }}
                            </p>
                            @if ($variantProduct)
                                <p class="mt-1 text-xs text-stone-500">
                                    Product #{{ $variantProduct->id }}
                                </p>
                                <p class="mt-1 text-xs text-stone-500">
                                    SKU: {{ $variantProduct->sku ?: '—' }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No variant items are associated with this product.
            </p>
        @endif
    </section>

    {{-- =========================================================
         ORIGIN AND SHIPPING DIMENSIONS
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- Origin --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Product origin and contact
                </h2>
            </div>

            <dl class="divide-y divide-stone-100 px-5">
                @foreach ([
                    'Country' => $originCountry?->name,
                    'Region' => $originRegion?->name,
                    'City' => $originCity?->name,
                    'Origin address' => $product1->origin_address,
                    'Contact person' => $product1->origin_contact_name,
                    'Contact phone' => $product1->origin_contact_phone,
                ] as $label => $value)
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-stone-500">{{ $label }}</dt>
                        <dd class="max-w-[65%] break-words text-right text-sm font-medium text-stone-800">
                            {{ $value !== null && $value !== '' ? $value : '—' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            <div class="border-t border-stone-100 px-5 py-3">
                <p class="text-xs text-stone-400">
                    Country ID: {{ $product1->country_id ?? '—' }}
                    <span class="mx-1">·</span>
                    Region ID: {{ $product1->origin_region_id ?? '—' }}
                    <span class="mx-1">·</span>
                    City ID: {{ $product1->origin_city_id ?? '—' }}
                </p>
            </div>
        </section>

        {{-- Shipping dimensions --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Shipping dimensions and package
                </h2>
            </div>

            @if ($dimensions)
                <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-4">
                    @foreach ([
                        'Length' => $dimensions->length,
                        'Width' => $dimensions->width,
                        'Height' => $dimensions->height,
                        'Weight' => $dimensions->weight,
                    ] as $label => $value)
                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-xs text-stone-500">{{ $label }}</p>
                            <p class="mt-1 break-words text-lg font-semibold text-stone-900">
                                {{ $value !== null && $value !== '' ? $value : '—' }}
                            </p>
                        </div>
                    @endforeach

                    <div class="col-span-2 border-t border-stone-100 pt-3 sm:col-span-4">
                        <p class="text-xs text-stone-500">Package type</p>
                        <p class="mt-1 text-sm font-medium text-stone-800">
                            {{ $dimensions->package_type ?? '—' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="p-5 text-sm text-stone-500">
                    No shipping dimensions have been saved.
                </p>
            @endif
        </section>
    </div>

    {{-- =========================================================
         SHIPPING OPTIONS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-stone-900">
                    Shipping options
                </h2>
                <p class="mt-1 text-xs text-stone-500">
                    Shipping templates selected for this product
                </p>
            </div>
            <span class="text-xs text-stone-400">
                {{ $product1->shippingTemplates->count() }} selected
            </span>
        </div>

        @if ($product1->shippingTemplates->isNotEmpty())
            <div class="divide-y divide-stone-100">
                @foreach ($product1->shippingTemplates as $template)
                    @php
                        $templateTranslation = $template->translations->firstWhere('locale', 'en')
                            ?? $template->translations->first();

                        $destinationNames = $template->locations
                            ->map(fn ($location) => $location->name)
                            ->filter()
                            ->values();

                        $templateTitle = $template->title
                            ?? $templateTranslation?->title
                            ?? ('Shipping template #' . $template->id);
                    @endphp

                    <div class="p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-semibold text-stone-900">
                                    {{ $templateTitle }}
                                </h3>

                                @if ($template->description)
                                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-stone-600">
                                        {{ $template->description }}
                                    </p>
                                @endif

                                <p class="mt-2 text-xs text-stone-500">
                                    Warehouse:
                                    <span class="font-medium text-stone-700">
                                        {{ $template->warehouse?->name ?? 'Not specified' }}
                                    </span>
                                </p>

                                <p class="mt-1 text-xs text-stone-500">
                                    Delivery type:
                                    <span class="font-medium text-stone-700">
                                        {{ $template->shipping_type ?? '—' }}
                                    </span>
                                </p>

                                <p class="mt-1 text-xs text-stone-500">
                                    Status:
                                    <span class="font-medium {{ $template->is_active ? 'text-emerald-700' : 'text-stone-500' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>

                            <div class="shrink-0 rounded-lg bg-stone-50 p-3 lg:min-w-40">
                                <p class="text-xs text-stone-500">Shipping price</p>
                                <p class="mt-1 text-lg font-semibold text-stone-900">
                                    {{ $template->price !== null ? number_format((float) $template->price, 2) : '—' }}
                                </p>
                                <p class="mt-1 text-xs text-stone-500">
                                    {{ $template->price_unit ?? 'Unit not specified' }}
                                </p>
                                <p class="mt-2 text-xs text-stone-500">
                                    Delivery time:
                                    <span class="font-medium text-stone-700">
                                        {{ $template->delivery_time ?? '—' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-stone-100 pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                                Delivery destinations
                            </p>

                            @if ($destinationNames->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($destinationNames as $destinationName)
                                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-700">
                                            {{ $destinationName }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-2 text-sm text-stone-500">
                                    No destinations listed on this template.
                                </p>
                            @endif
                        </div>

                        @if ($template->translations->isNotEmpty())
                            <details class="mt-3">
                                <summary class="cursor-pointer text-xs font-medium text-stone-500 hover:text-stone-800">
                                    View all shipping template translations
                                </summary>

                                <div class="mt-3 space-y-2">
                                    @foreach ($template->translations as $translation)
                                        <div class="rounded-lg bg-stone-50 p-3">
                                            <p class="text-xs font-semibold uppercase text-stone-500">
                                                {{ $translation->locale }}
                                            </p>
                                            <p class="mt-1 text-sm text-stone-800">
                                                {{ $translation->title ?? '—' }}
                                            </p>
                                            @if ($translation->description)
                                                <p class="mt-1 whitespace-pre-line text-xs leading-5 text-stone-600">
                                                    {{ $translation->description }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No shipping templates are assigned to this product.
            </p>
        @endif
    </section>

    {{-- =========================================================
         PAYMENT METHODS AND TERMS
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- Payment methods --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Payment methods
                </h2>
            </div>

            @if ($product1->paymentMethods->isNotEmpty())
                <div class="divide-y divide-stone-100">
                    @foreach ($product1->paymentMethods as $method)
                        @php
                            $methodTranslation = $method->translations->firstWhere('locale', 'en')
                                ?? $method->translations->first();

                            $methodName = $method->name
                                ?? $methodTranslation?->name
                                ?? ('Payment method #' . $method->id);
                        @endphp

                        <div class="px-5 py-3">
                            <p class="text-sm font-medium text-stone-900">
                                {{ $methodName }}
                            </p>

                            <p class="mt-1 text-xs text-stone-500">
                                ID: {{ $method->id }}
                            </p>

                            @if ($method->translations->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($method->translations as $translation)
                                        <span class="rounded-md bg-stone-50 px-2 py-1 text-xs text-stone-600">
                                            {{ strtoupper($translation->locale) }}:
                                            {{ $translation->name ?? '—' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="p-5 text-sm text-stone-500">
                    No payment methods selected.
                </p>
            @endif
        </section>

        {{-- Payment terms --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Payment terms
                </h2>
            </div>

            @if ($product1->paymentTerms->isNotEmpty())
                <div class="divide-y divide-stone-100">
                    @foreach ($product1->paymentTerms as $term)
                        @php
                            $termTranslation = $term->translations->firstWhere('locale', 'en')
                                ?? $term->translations->first();

                            $termName = $term->name
                                ?? $termTranslation?->name
                                ?? ('Payment term #' . $term->id);
                        @endphp

                        <div class="px-5 py-3">
                            <p class="text-sm font-medium text-stone-900">
                                {{ $termName }}
                            </p>

                            <p class="mt-1 text-xs text-stone-500">
                                ID: {{ $term->id }}
                            </p>

                            @if ($term->translations->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($term->translations as $translation)
                                        <span class="rounded-md bg-stone-50 px-2 py-1 text-xs text-stone-600">
                                            {{ strtoupper($translation->locale) }}:
                                            {{ $translation->name ?? '—' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="p-5 text-sm text-stone-500">
                    No payment terms selected.
                </p>
            @endif
        </section>
    </div>

    {{-- =========================================================
         RETURNS AND REFUNDS
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-stone-900">
                Returns and refunds
            </h2>
            <p class="mt-1 text-xs text-stone-500">
                Product-specific return settings and selected policy
            </p>
        </div>

        @if ($productReturnPolicy)
            <div class="grid grid-cols-1 gap-5 p-5 xl:grid-cols-2">

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                        Product return settings
                    </h3>

                    <dl class="mt-3 divide-y divide-stone-100">
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-xs text-stone-500">
                                Use supplier default policy
                            </dt>
                            <dd class="text-right text-sm font-medium text-stone-800">
                                {{ $productReturnPolicy->use_supplier_default ? 'Yes' : 'No' }}
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-xs text-stone-500">
                                Return policy ID
                            </dt>
                            <dd class="text-sm font-medium text-stone-800">
                                {{ $productReturnPolicy->return_policy_id ?? '—' }}
                            </dd>
                        </div>

                        @foreach ($returnFields as $label => $value)
                            <div class="flex items-start justify-between gap-4 py-3">
                                <dt class="text-xs text-stone-500">{{ $label }}</dt>
                                <dd class="max-w-[65%] whitespace-pre-line break-words text-right text-sm font-medium text-stone-800">
                                    @if ($value === null || $value === '')
                                        Not specified
                                    @elseif (is_bool($value) || in_array($value, [0, 1, '0', '1'], true) && in_array($label, ['Returnable', 'Restocking fee enabled', 'Custom products returnable'], true))
                                        {{ filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No' }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                        Selected return policy
                    </h3>

                    @if ($returnPolicy)
                        <div class="mt-3 rounded-lg border border-stone-200 p-4">
                            <p class="text-sm font-semibold text-stone-900">
                                {{ $returnPolicy->name ?? ('Return policy #' . $returnPolicy->id) }}
                            </p>

                            <p class="mt-1 text-xs text-stone-500">
                                Policy ID: {{ $returnPolicy->id }}
                            </p>

                            @if ($returnPolicy->translations->isNotEmpty())
                                <div class="mt-3 space-y-3">
                                    @foreach ($returnPolicy->translations as $translation)
                                        <div class="rounded-md bg-stone-50 p-3">
                                            <p class="text-xs font-semibold uppercase text-stone-500">
                                                {{ $translation->locale }}
                                            </p>
                                            <p class="mt-1 text-sm font-medium text-stone-800">
                                                {{ $translation->name ?? $translation->title ?? '—' }}
                                            </p>
                                            @if ($translation->description ?? false)
                                                <p class="mt-1 whitespace-pre-line text-xs leading-5 text-stone-600">
                                                    {{ $translation->description }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($returnPolicy->reasons->isNotEmpty())
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-stone-600">
                                    Return reasons
                                </p>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($returnPolicy->reasons as $reason)
                                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700">
                                            {{ $reason->name ?? $reason->translations->firstWhere('locale', 'en')?->name ?? ('Reason #' . $reason->id) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($returnPolicy->resolutions->isNotEmpty())
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-stone-600">
                                    Available resolutions
                                </p>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($returnPolicy->resolutions as $resolution)
                                        <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700">
                                            {{ $resolution->name ?? $resolution->translations->firstWhere('locale', 'en')?->name ?? ('Resolution #' . $resolution->id) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="mt-3 rounded-lg bg-stone-50 p-4 text-sm text-stone-500">
                            No specific return policy selected.
                        </p>
                    @endif
                </div>
            </div>
        @else
            <p class="p-5 text-sm text-stone-500">
                No product-specific return settings have been saved.
            </p>
        @endif
    </section>

</div>

{{-- Gallery interaction only; moderation actions remain unchanged --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mainImage = document.getElementById('adminMainImage');

    document.querySelectorAll('.admin-image-thumbnail').forEach((button) => {
        button.addEventListener('click', () => {
            if (!mainImage || !button.dataset.src) {
                return;
            }

            mainImage.src = button.dataset.src;

            document.querySelectorAll('.admin-image-thumbnail').forEach((item) => {
                item.classList.remove('border-stone-700');
                item.classList.add('border-stone-200');
            });

            button.classList.remove('border-stone-200');
            button.classList.add('border-stone-700');
        });
    });
});
</script>

@endsection
