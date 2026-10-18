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