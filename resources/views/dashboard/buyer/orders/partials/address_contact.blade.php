{{-- Контакты и адрес --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-base font-semibold text-gray-900">
            Contact & Shipping Information
        </h3>

        {{-- Кнопка открыть модалку --}}
        @if($canEditAddress)
            <div x-data="{ editAddressModalOpen: false }">
                <a href="#"
                   @click.prevent="editAddressModalOpen = true"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    Edit Address
                </a>

                @include('dashboard.buyer.orders.modals.edit_address_modal', ['order' => $order])
            </div>
        @endif
    </div>

    @if($order->country && $order->city && $order->street)

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- Recipient Contact --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">
                    Recipient Contact
                </h4>

                <div class="space-y-4 text-sm">

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Full Name</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ $order->first_name }} {{ $order->last_name ?? '' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Phone</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ $order->phone }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- Shipping Address --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">
                    Shipping Address
                </h4>

                <div class="space-y-4 text-sm">

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Country</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ optional($order->countryRelation)->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">City</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ $order->city }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Region</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ optional($order->regionRelation)->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Postal Code</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ $order->postal_code }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Street</div>
                        <div class="mt-1 font-medium text-gray-900">
                            {{ $order->street }}
                        </div>
                    </div>

                </div>
            </div>

        </div>

    @else
        <div class="text-sm text-gray-500">
            Address not provided.
        </div>
    @endif

</div>
