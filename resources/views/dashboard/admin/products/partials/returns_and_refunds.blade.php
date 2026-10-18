{{-- RETURNS AND REFUNDS --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">Returns and refunds</h2>
        <p class="mt-1 text-xs text-stone-500">Return policy applied to this product</p>
    </div>

    @php
        $returnSettings = $productReturnPolicy ?? null;
        $assignedPolicy = $effectiveReturnPolicy ?? null;
        $usesSupplierDefault = (bool) ($returnSettings?->use_supplier_default);

        $policyTranslation = $assignedPolicy
            ? ($assignedPolicy->translation(app()->getLocale())
                ?? $assignedPolicy->translation('en'))
            : null;

        $returnShippingPayer = match ($assignedPolicy?->return_shipping_payer) {
            'buyer' => 'Buyer pays return shipping',
            'supplier' => 'Supplier pays return shipping',
            'depends_on_reason' => 'Shipping depends on reason',
            default => null,
        };
    @endphp

    @if ($returnSettings)
        <div class="p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="text-sm text-stone-600">Policy mode</span>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $usesSupplierDefault ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                    {{ $usesSupplierDefault ? 'Supplier default' : 'Product-specific policy' }}
                </span>
            </div>

            @if ($assignedPolicy)
                <div class="mt-4 rounded-xl border border-stone-200 p-4">
                    {{-- POLICY HEADER --}}
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-stone-900">
                                {{ $policyTranslation?->name ?? $assignedPolicy->code ?? ('Return policy #' . $assignedPolicy->id) }}
                            </h3>

                            @if ($policyTranslation?->description)
                                <p class="mt-1 text-sm leading-5 text-stone-500">
                                    {{ $policyTranslation->description }}
                                </p>
                            @endif
                        </div>

                        @if ($assignedPolicy->code)
                            <span class="shrink-0 rounded-md bg-stone-100 px-2 py-1 text-xs font-medium text-stone-600">
                                {{ $assignedPolicy->code }}
                            </span>
                        @endif
                    </div>

                    {{-- POLICY DETAILS --}}
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-stone-400">Return window</p>
                            <p class="mt-1 text-sm font-medium text-stone-800">
                                {{ $assignedPolicy->return_window_days !== null ? $assignedPolicy->return_window_days . ' days' : 'No return window' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-stone-400">Return shipping</p>
                            <p class="mt-1 text-sm font-medium text-stone-800">
                                {{ $returnShippingPayer ?? 'Not specified' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-stone-400">Restocking fee</p>
                            <p class="mt-1 text-sm font-medium text-stone-800">
                                @if ($assignedPolicy->restocking_fee_enabled)
                                    {{ $assignedPolicy->restocking_fee_percent !== null
                                        ? rtrim(rtrim(number_format((float) $assignedPolicy->restocking_fee_percent, 2), '0'), '.') . '%'
                                        : 'Enabled' }}
                                @else
                                    None
                                @endif
                            </p>
                        </div>

                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-stone-400">Custom products</p>
                            <p class="mt-1 text-sm font-medium text-stone-800">
                                {{ $assignedPolicy->custom_products_returnable ? 'Allowed' : 'Not allowed' }}
                            </p>
                        </div>
                    </div>

                    {{-- RETURN REASONS --}}
                    @if ($assignedPolicy->reasons->isNotEmpty())
                        <div class="mt-4 border-t border-stone-100 pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Return reasons</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach ($assignedPolicy->reasons as $reason)
                                    @php
                                        $reasonTranslation = $reason->translation(app()->getLocale())
                                            ?? $reason->translation('en');
                                    @endphp

                                    <span class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        {{ $reasonTranslation?->name ?? $reason->code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- RESOLUTIONS --}}
                    @if ($assignedPolicy->resolutions->isNotEmpty())
                        <div class="mt-4 border-t border-stone-100 pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Available resolutions</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach ($assignedPolicy->resolutions as $resolution)
                                    @php
                                        $resolutionTranslation = $resolution->translation(app()->getLocale())
                                            ?? $resolution->translation('en');
                                    @endphp

                                    <span class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        {{ $resolutionTranslation?->name ?? $resolution->code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @elseif ($usesSupplierDefault)
                <p class="mt-3 text-sm text-stone-500">
                    The supplier default policy is selected, but no default policy is configured for this supplier.
                </p>
            @else
                <p class="mt-3 text-sm text-stone-500">
                    No specific return policy is assigned to this product.
                </p>
            @endif

            @if (filled($returnSettings->additional_information))
                <div class="mt-4 border-t border-stone-100 pt-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Additional information</p>
                    <p class="mt-1 whitespace-pre-line break-words text-sm leading-5 text-stone-600">
                        {{ $returnSettings->additional_information }}
                    </p>
                </div>
            @endif
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">
            No return settings have been saved for this product.
        </p>
    @endif
</section>