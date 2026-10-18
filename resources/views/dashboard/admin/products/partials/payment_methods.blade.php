{{-- PAYMENT METHODS --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">Payment methods</h2>
    </div>

    
    @php
        $paymentMethods = $product1->paymentMethods->where('is_active', true);
    @endphp

    @if ($paymentMethods->isNotEmpty())
        <div class="p-5">
            <div class="space-y-4">
                @foreach ($paymentMethods as $paymentMethod)
                    @php
                        $paymentTranslation = $paymentMethod->translations
                            ->firstWhere('locale', app()->getLocale())
                            ?? $paymentMethod->translations->firstWhere('locale', 'en')
                            ?? $paymentMethod->translations->first();
                    @endphp

                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center">
                            @if ($paymentMethod->icon_svg)
                                {!! $paymentMethod->icon_svg !!}
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-6 w-6 text-emerald-600"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.6"
                                     stroke-linecap="round"
                                     stroke-linejoin="round"
                                     aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="M3 10h18"/>
                                    <path d="M7 15h3"/>
                                </svg>
                            @endif
                        </span>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold leading-snug text-stone-900">
                                {{ $paymentTranslation?->name ?? $paymentMethod->code }}
                            </div>

                            @if ($paymentTranslation?->description)
                                <p class="mt-1 text-xs leading-snug text-stone-500">
                                    {{ $paymentTranslation->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">No active payment methods selected.</p>
    @endif
</section>