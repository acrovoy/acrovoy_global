{{-- =====================================================
RETURN POLICY MODAL
====================================================== --}}

<div
    x-cloak
    x-show="returnPolicyModalOpen"
    x-transition.opacity.duration.200ms
    x-trap.noscroll.inert="returnPolicyModalOpen"
    @keydown.escape.prevent.stop="returnPolicyModalOpen = false"
    @click.self="returnPolicyModalOpen = false"
    class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto p-3 sm:p-6"
    role="presentation"
    style="display: none;"
>
    {{-- =====================================================
        BACKDROP
    ====================================================== --}}


<div
    class="fixed inset-0 bg-gray-950/50 backdrop-blur-[2px]"
    aria-hidden="true"
></div>

{{-- =====================================================
    MODAL PANEL
====================================================== --}}

<section
    x-show="returnPolicyModalOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-2 scale-[0.99]"
    @click.stop
    class="relative my-auto flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl"
    role="dialog"
    aria-modal="true"
    aria-labelledby="return-policy-modal-title"
    aria-describedby="return-policy-modal-description"
    tabindex="-1"
>

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 sm:px-7 sm:py-5">

        <div class="min-w-0">

            

            <h2
                id="return-policy-modal-title"
                class="mt-1 text-xl font-bold leading-tight text-gray-900 sm:text-2xl"
            >
                Return Policy
            </h2>

            <p
                id="return-policy-modal-description"
                class="mt-2 text-sm leading-relaxed text-gray-500"
            >
                Review the return conditions for this product.
            </p>

        </div>

        <button
            type="button"
            @click="returnPolicyModalOpen = false"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200 text-gray-500 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
            aria-label="Close return policy"
            title="Close"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </button>

    </div>


    {{-- =====================================================
        SCROLLABLE CONTENT
    ====================================================== --}}

    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-5 sm:px-7 sm:py-6">

        @if(!empty($returnPolicyData))

            {{-- POLICY NAME AND DESCRIPTION --}}

            <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 sm:p-5">

                <div class="flex items-start gap-3">

                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-emerald-100 bg-white text-emerald-700">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M3 11a9 9 0 1 0 3-6.7L3 7"/>
                            <path d="M3 3v4h4"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>

                    </span>

                    <div class="min-w-0 flex-1">

                        <h3 class="text-base font-bold text-gray-900">
                            {{ $returnPolicyData['name'] ?? 'Return Policy' }}
                        </h3>

                        @if(!empty($returnPolicyData['description']))
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-600">
                                {{ $returnPolicyData['description'] }}
                            </p>
                        @endif

                    </div>

                </div>

            </div>


            {{-- =====================================================
                RETURN CONDITIONS
            ====================================================== --}}

            <div class="mt-6">

                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                    Return Conditions
                </h3>

                <div class="mt-3 divide-y divide-gray-100">

                    {{-- RETURN WINDOW --}}

                    @if(($returnPolicyData['return_window_days'] ?? null) !== null)

                        <div class="flex items-start gap-3 py-4">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v5l3 2"/>
                                </svg>

                            </span>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-gray-900">
                                    Return window
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                    @if((int) $returnPolicyData['return_window_days'] === 1)
                                        Returns accepted within 1 day.
                                    @else
                                        Returns accepted within {{ $returnPolicyData['return_window_days'] }} days.
                                    @endif
                                </p>

                            </div>

                        </div>

                    @endif


                    {{-- RETURN SHIPPING --}}

                    @if(!empty($returnPolicyData['return_shipping_payer']))

                        <div class="flex items-start gap-3 py-4">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M3 7h11v10H3z"/>
                                    <path d="M14 10h4l3 3v4h-7z"/>
                                    <circle cx="7" cy="19" r="1.5"/>
                                    <circle cx="18" cy="19" r="1.5"/>
                                </svg>

                            </span>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-gray-900">
                                    Return shipping
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                    {{ $returnPolicyData['return_shipping_payer'] }}
                                </p>

                            </div>

                        </div>

                    @endif


                    {{-- RESTOCKING FEE --}}

                    @if(!empty($returnPolicyData['restocking_fee_enabled'])
                        && ($returnPolicyData['restocking_fee_percent'] ?? null) !== null
                        && (float) $returnPolicyData['restocking_fee_percent'] > 0)

                        <div class="flex items-start gap-3 py-4">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M12 2v20"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>

                            </span>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-gray-900">
                                    Restocking fee
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                    {{ $returnPolicyData['restocking_fee_percent'] }}% of the applicable amount.
                                </p>

                            </div>

                        </div>

                    @endif


                    {{-- CUSTOM PRODUCTS --}}

                    @if(!empty($returnPolicyData['custom_products_returnable']))

                        <div class="flex items-start gap-3 py-4">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>

                            </span>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-gray-900">
                                    Custom products
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-gray-600">
                                    Custom products can be returned under this policy.
                                </p>

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- =====================================================
                REASONS FOR RETURN
            ====================================================== --}}

            @if(!empty($returnPolicyData['reasons'])
                && count($returnPolicyData['reasons']) > 0)

                <div class="mt-6 border-t border-gray-200 pt-5">

                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                        Accepted Return Reasons
                    </h3>

                    <ul class="mt-3 space-y-2">

                        @foreach($returnPolicyData['reasons'] as $reason)

                            <li class="flex items-start gap-2.5 text-sm leading-relaxed text-gray-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>

                                <span>
                                    {{ is_array($reason) ? ($reason['name'] ?? '') : $reason }}
                                </span>

                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =====================================================
                RESOLUTIONS
            ====================================================== --}}

            @if(!empty($returnPolicyData['resolutions'])
                && count($returnPolicyData['resolutions']) > 0)

                <div class="mt-6 border-t border-gray-200 pt-5">

                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                        Available Resolutions
                    </h3>

                    <ul class="mt-3 space-y-2">

                        @foreach($returnPolicyData['resolutions'] as $resolution)

                            <li class="flex items-start gap-2.5 text-sm leading-relaxed text-gray-700">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>

                                <span>
                                    {{ is_array($resolution) ? ($resolution['name'] ?? '') : $resolution }}
                                </span>

                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =====================================================
                ADDITIONAL INFORMATION
            ====================================================== --}}

            @if(!empty($returnPolicyData['additional_information']))

                <div class="mt-6 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Additional Information
                    </h3>

                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-600">
                        {{ $returnPolicyData['additional_information'] }}
                    </p>

                </div>

            @endif

        @else

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                <p class="text-sm leading-relaxed text-gray-600">
                    Return conditions are available from the supplier. Please contact the supplier for further information.
                </p>

            </div>

        @endif

    </div>


    {{-- =====================================================
        FOOTER
    ====================================================== --}}

    <div class="flex shrink-0 items-center justify-between gap-4 border-t border-gray-200 bg-white px-5 py-4 sm:px-7">

        <p class="hidden text-xs leading-relaxed text-gray-500 sm:block">
            Please review the conditions before placing an order.
        </p>

        <button
            type="button"
            @click="returnPolicyModalOpen = false"
            class="inline-flex min-h-10 items-center justify-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
        >
            Close
        </button>

    </div>

</section>


</div>
