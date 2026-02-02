 {{-- Price / Quantity Table --}}
                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <h3 class="font-semibold text-lg leading-none">
                        {{ __('product/product_show.price_per_quantity') }}
                    </h3>

                    <span class="text-xs text-gray-500 leading-none">
                        {{ __('product/product_show.shipping_cost_not_included') }}
                    </span>
                    <table class="w-full text-left text-gray-700">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">{{ __('product/product_show.quantity') }}</th>
                                <th class="py-2">{{ __('product/product_show.unit_price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product1->priceTiers as $tier)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">
                                    {{ $tier->min_qty }} - {{ $tier->max_qty ?? '∞' }} pcs
                                </td>
                                <td class="py-2 font-semibold text-blue-900 {{ $loop->first ? 'text-xl' : 'text-base' }}">
                                    {{ price($tier['price']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>