{{-- Supplier Info --}}
<div x-data="{ openReviews: false }" class="@if($supplier->is_trusted)paper-notch @endif relative flex flex-col lg:flex-row items-start gap-6 rounded-xl border  p-6 mb-4
            @if(($supplier->reputation ?? 0) > 120)bg-gradient-to-br from-white via-[#f7f3ec] to-[#e1d8cb] shadow-lg border-gray-100
            @else
            bg-gradient-to-br from-white via-[#f9f7f3] to-[#f9f7f3] shadow-sm border-gray-200
            @endif ">

    {{-- TOP BADGES --}}
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-20 flex overflow-visible">
        @if($supplier->is_trusted)
        <div class="bg-emerald-700 text-white text-[10px] font-semibold rounded-t-full px-3 py-1 uppercase tracking-wide">
            TRUSTED COMPANY
        </div>
        @endif
    </div>

    {{-- Logo --}}
    @if($supplier->logo)
    <div class="flex-shrink-0">
        <img src="{{ asset('storage/' . $supplier->logo) }}"
            alt="{{ $supplier->name }}"
            class="w-28 h-28 object-cover rounded-lg">
    </div>
    @endif

    {{-- Info --}}
    <div class="flex-1 flex flex-col gap-2">

        <div class="flex items-start justify-between">
            <div>
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                    <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                        {{ $supplier->name }}
                    </h1>

                    @if($supplier->is_verified)
                    <img src="{{ asset('images/icons/verified_icon.png') }}"
                        alt="Verified"
                        class="w-5 h-5 flex-shrink-0">
                    @endif

                    @if($types->isNotEmpty())
                    <div class="">
                        <span class="text-sm text-gray-400">
                            {{ $types->implode('  -  ') }}
                        </span>
                    </div>
                    @endif

                    <span class="text-gray-600 font-medium whitespace-nowrap">
                        |&nbsp;&nbsp; {{ $supplier->country ? $supplier->country->name : 'N/A' }}
                    </span>

                </div>

                <p class="text-gray-700">{{ $supplier->short_description ?? '' }}</p>
                <p class="text-gray-700 mb-3">{{ $supplier->description ?? '' }}</p>

            </div>

            {{-- Reputation --}}
            <div class="ml-auto w-full lg:w-auto self-start">

                <div class="px-5 pt-3">

                    <div class="bg-gray-50 h-[20]  rounded-xl p-4 shadow-inner">

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] whitespace-nowrap font-semibold tracking-wide shadow-xl
                                    {{ $level === 'Basic' ? 'bg-gray-50 text-gray-400 border border-gray-200' : '' }}
                                    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
                                    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                    {{ $level === 'Platinum' ? 'bg-slate-800 text-white border border-slate-700' : '' }}
                                ">

                            @if($level === 'Basic')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" />
                            </svg>
                            @elseif($level === 'Silver')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 3l7 18H5l7-18z" />
                            </svg>
                            @elseif($level === 'Gold')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M5 12l5 5L20 7" />
                            </svg>
                            @elseif($level === 'Platinum')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 2l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-7.8L2 9h7z" />
                            </svg>
                            @endif

                            {{ strtoupper($level) }} SUPPLIER
                        </span>

                        <div class="mt-2 items-center flex justify-center">
                            <div class="flex flex-col md:flex-row items-center gap-1 inline">

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <=floor($supplierRating))
                                    <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" /></svg>
                                    @elseif ($i - $supplierRating < 1)
                                        <svg class="w-4 h-4 fill-current text-yellow-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" /></svg>
                                        @else
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                        </svg></svg>

                                        @endif

                                        @endfor

                                        <span class="text-xs text-gray-500">{{$supplierRating = number_format($supplierRating, 1);}}</span>

                            </div>
                        </div>

                        <div @click="openReviews = true" class="items-center flex justify-center text-xs  mt-1 text-emerald-700 hover:text-emerald-900 hover:underline hover:cursor-pointer">
                            {{-- Количество отзывов --}}
                            <span>{{ $supplier->supplierReviews->count() }} review(s)</span>
                        </div>

                    </div>

                    <div class="mt-4 flex justify-center">
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5
                                            text-[11px] font-medium text-gray-500
                                            bg-[#f4f1eb] border border-gray-200
                                            rounded-full shadow-sm tracking-wide">

                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none"
                                stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            {{ $supplier->years_on_platform ?? 0 }}+ years on Acrovoy
                        </div>
                    </div>





                </div>
            </div>
            @include('supplier.modals.supplier_reviews')

        </div>

    </div>
</div>