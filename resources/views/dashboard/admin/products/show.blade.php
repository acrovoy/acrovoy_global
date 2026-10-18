
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


    @include('dashboard.admin.products.partials.product_title')

    
    {{-- =========================================================
    GALLERY AND PRODUCT RECORD
========================================================== --}}
<div class="flex flex-col gap-5">
    @include('dashboard.admin.products.partials.product_images')

    @include('dashboard.admin.products.partials.product_record')
</div>

    @include('dashboard.admin.products.partials.all_product_translations')

    @include('dashboard.admin.products.partials.price_tiers')

    @include('dashboard.admin.products.partials.product_specifications')

    @include('dashboard.admin.products.partials.materials')

    @include('dashboard.admin.products.partials.variants')

    {{-- =========================================================
         ORIGIN AND SHIPPING DIMENSIONS
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

        @include('dashboard.admin.products.partials.origin')

        @include('dashboard.admin.products.partials.shipping_dimensions')

    </div>

    @include('dashboard.admin.products.partials.shipping_options')

    {{-- =========================================================
         PAYMENT METHODS AND TERMS
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

        @include('dashboard.admin.products.partials.payment_methods')

        @include('dashboard.admin.products.partials.payment_terms')

    </div>

     @include('dashboard.admin.products.partials.returns_and_refunds')

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
