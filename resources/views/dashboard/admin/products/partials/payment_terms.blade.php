{{-- PAYMENT TERMS --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">Payment terms</h2>
    </div>

    @if ($product1->paymentTerms->isNotEmpty())
        <div class="p-5">
            <div class="space-y-4">
                @foreach ($product1->paymentTerms as $paymentTerm)
                    @php
                        $translation = $paymentTerm->translations
                            ->firstWhere('locale', app()->getLocale())
                            ?? $paymentTerm->translations->firstWhere('locale', 'en')
                            ?? $paymentTerm->translations->first();
                    @endphp

                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-stone-600">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.7"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 aria-hidden="true">
                                <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold leading-snug text-stone-900">
                                {{ $translation?->name ?? $paymentTerm->code }}
                            </div>

                            @if ($translation?->description)
                                <p class="mt-1 text-xs leading-snug text-stone-500">
                                    {{ $translation->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">No payment terms selected.</p>
    @endif
</section>