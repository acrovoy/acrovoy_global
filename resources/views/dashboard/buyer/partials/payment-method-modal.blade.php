{{-- =========================================================
    PAYMENT METHOD MODAL
========================================================= --}}

<div
    id="payment-method-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-labelledby="payment-method-modal-title"
    aria-modal="true"
    role="dialog"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-gray-900/50 transition-opacity"
        onclick="closePaymentMethodModal()"
    ></div>

    {{-- Modal container --}}
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 sm:px-6">
                <div>
                    <h2
                        id="payment-method-modal-title"
                        class="text-lg font-semibold text-gray-900"
                    >
                        Select Payment Method
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Choose your preferred payment method.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closePaymentMethodModal()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                    aria-label="Close payment methods"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>

            {{-- Payment methods --}}
            <div class="max-h-[65vh] overflow-y-auto px-5 py-5 sm:px-6">
                @if($availablePaymentMethods->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($availablePaymentMethods as $paymentMethod)
    @php
        $translation = $paymentMethod->translations
            ->firstWhere('locale', app()->getLocale())
            ?? $paymentMethod->translations->firstWhere('locale', 'en');

        $methodName = $translation?->name
            ?? $paymentMethod->code
            ?? 'Payment method';

        $methodDescription = $translation?->description ?? '';
        $methodIcon = $paymentMethod->icon_svg;
        $radioId = 'payment-method-' . $paymentMethod->id;
    @endphp

    <label
        for="{{ $radioId }}"
        class="flex cursor-pointer items-center gap-4 rounded-xl border border-gray-200 p-4 transition hover:border-blue-300 hover:bg-gray-50 has-[:checked]:border-blue-900 has-[:checked]:bg-blue-50/50"
    >
        <input
            type="radio"
            id="{{ $radioId }}"
            name="payment_method_option_id"
            value="{{ $paymentMethod->id }}"
            data-method-id="{{ $paymentMethod->id }}"
            data-method-name="{{ $methodName }}"
            data-method-description="{{ $methodDescription }}"
            data-method-logo="{{ $methodIcon ? base64_encode($methodIcon) : '' }}"
            class="h-4 w-4 shrink-0 border-gray-300 text-blue-900 focus:ring-blue-900"
        >

        @if($methodIcon)
            <div class="flex h-14 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-white p-2">
                <div class="flex h-full w-full items-center justify-center [&>svg]:h-full [&>svg]:w-full [&>svg]:max-h-10 [&>svg]:max-w-full">
                    {!! $methodIcon !!}
                </div>
            </div>
        @endif

        <div class="min-w-0 flex-1">
            <span class="text-sm font-semibold text-gray-900">
                {{ $methodName }}
            </span>

            @if($methodDescription)
                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                    {{ $methodDescription }}
                </p>
            @endif
        </div>
    </label>
@endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                            <svg
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h3"/>
                            </svg>
                        </div>

                        <p class="mt-3 text-sm font-medium text-gray-900">
                            No payment methods available
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Please contact the supplier to arrange payment.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <button
                    type="button"
                    onclick="closePaymentMethodModal()"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    onclick="confirmPaymentMethod()"
                    @disabled($availablePaymentMethods->isEmpty())
                    class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900/20 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Confirm Payment Method
                </button>
            </div>
        </div>
    </div>
</div>