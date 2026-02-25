<div class="bg-gradient-to-br from-white via-[#fbf8f2] to-white 
            border border-gray-200/70 shadow-xl shadow-gray-100 
            rounded-3xl p-10 space-y-16">

{{-- ================= ABOUT ================= --}}
<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-700 pl-4">
        About Us
    </h2>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 space-y-8">

        {{-- Description --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $supplier->about ?? 'We are a professional manufacturer specializing in high-quality custom furniture solutions for commercial and residential projects. With strong global experience, we focus on precision engineering, material traceability, and consistent production standards.' }}
        </p>

        {{-- Core Metrics --}}
        <div class="grid md:grid-cols-3 gap-8 text-sm">

            {{-- Founded --}}
            <div>
                <div class="text-gray-400 text-xs">Founded</div>
                <div class="font-semibold text-gray-900">
                    {{ $supplier->founded_year ?? '2015' }}
                </div>
            </div>

            {{-- Years in Industry (auto calculated) --}}
            <div>
                <div class="text-gray-400 text-xs">Years in Industry</div>
                <div class="font-semibold text-gray-900">
                    @if($supplier->founded_year)
                        {{ now()->year - $supplier->founded_year }}+
                    @else
                        35+
                    @endif
                </div>
            </div>

            {{-- Markets / Nationality --}}
            <div>
                <div class="text-gray-400 text-xs">Markets</div>
                <div class="font-semibold text-gray-900 leading-relaxed">
                    @if($supplier->exportMarkets && $supplier->exportMarkets->isNotEmpty())
                        {{ $supplier->exportMarkets
                            ->map(fn($m) => $m->translation?->name ?? $m->slug)
                            ->implode(', ') }}
                    @else
                        {{ $supplier->country?->name ?? 'National' }}
                    @endif
                </div>
            </div>

            {{-- Annual Export Revenue --}}
            <div>
                <div class="text-gray-400 text-xs">Annual Export Revenue</div>
                <div class="font-semibold text-gray-900">
                    {{ number_format($supplier->annual_export_revenue ?? 651170) }} USD
                </div>
            </div>

            {{-- Total Employees --}}
            <div>
                <div class="text-gray-400 text-xs">Total Employees</div>
                <div class="font-semibold text-gray-900">
                    {{ $supplier->total_employees ?? 29 }}
                </div>
            </div>

            {{-- Registration Capital --}}
            <div>
                <div class="text-gray-400 text-xs">Company Registration Capital</div>
                <div class="font-semibold text-gray-900">
                    {{ number_format($supplier->registration_capital ?? 1000000) }} USD
                </div>
            </div>

        </div>

    </div>
</div>



{{-- ================= MANUFACTURING PROFILE ================= --}}
<div class="space-y-12">

    <h2 class="text-2xl font-semibold text-gray-900 border-l-4 border-yellow-700 pl-4">
        Manufacturing Profile
    </h2>

    <div class="grid lg:grid-cols-2 gap-12 items-start">

        {{-- Manufacturing Overview --}}
        <div class="space-y-5">

            <h3 class="text-lg font-semibold text-gray-900 border-l-2 border-yellow-500 pl-3">
                Manufacturing Overview
            </h3>

            <div class="text-sm text-gray-600 leading-relaxed bg-white/80 backdrop-blur-sm border rounded-2xl p-6 shadow-sm">
                {!! $supplier->description ?? 'Our manufacturing facilities integrate modern CNC systems, skilled craftsmanship, and strict quality control procedures to ensure consistent output across global projects.' !!}
            </div>

        </div>


        {{-- Production Capability --}}
        <div class="space-y-5 max-w-sm">

            <h3 class="text-lg font-semibold text-gray-900 border-l-2 border-yellow-500 pl-3">
                Production Capability
            </h3>

            <div class="flex flex-col gap-3">

                @php
                    $capabilities = [
                        [
                            'label' => 'Registration Date',
                            'value' => optional($supplier->created_at)->format('d-m-Y'),
                            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14'
                        ],
                        [
                            'label' => 'Factory Area',
                            'value' => ($supplier->factory_area ?? 2580) . ' m²',
                            'icon' => 'M4 5h16v14H4z'
                        ],
                        [
                            'label' => 'Production Lines',
                            'value' => '4',
                            'icon' => 'M3 6h18M3 12h18M3 18h18'
                        ],
                    ];
                @endphp

                @foreach($capabilities as $item)

                    <div class="group flex items-center gap-3 rounded-xl border border-gray-200/70
                                bg-white/60 backdrop-blur-sm hover:border-yellow-200
                                hover:shadow-md transition p-3">

                        <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center
                                    text-yellow-600 group-hover:bg-yellow-100 transition">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="{{ $item['icon'] }}"/>
                            </svg>

                        </div>

                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-wider text-gray-400">
                                {{ $item['label'] }}
                            </span>

                            <span class="text-sm font-semibold text-gray-900">
                                {{ $item['value'] }}
                            </span>
                        </div>

                    </div>

                @endforeach

            </div>
        </div>


        {{-- Factory Base --}}
        <div class="lg:col-span-2 space-y-6">

            <h3 class="text-lg font-semibold text-gray-900 border-l-2 border-yellow-500 pl-3">
                Factory & Production Base
            </h3>

            <div class="grid md:grid-cols-4 gap-6">
                @for($i = 0; $i < 4; $i++)
                    <div class="aspect-square rounded-xl border border-gray-200/70
                                bg-gray-50 hover:shadow-lg transition duration-300"></div>
                @endfor
            </div>

        </div>

    </div>
</div>



{{-- ================= CERTIFICATIONS ================= --}}
<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-700 pl-4">
        Certifications & Compliance
    </h2>

    <div class="flex flex-wrap gap-4">

        @forelse($supplier->certificates ?? [] as $cert)
            <x-supplier.certificate-card :certificate="$cert"/>
        @empty
            <div class="text-sm text-gray-400">
                No certifications uploaded
            </div>
        @endforelse

    </div>

</div>



{{-- ================= COMPANY JOURNEY ================= --}}
<div class="space-y-8">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-700 pl-4">
        Company Development Journey
    </h2>

    <div class="relative ml-4 pl-10 space-y-10">

        <div class="absolute left-0 top-2 bottom-2 w-px 
                    bg-gradient-to-b from-transparent via-gray-300 to-transparent"></div>

        {{-- Founded --}}
        <div class="relative">

            <div class="absolute -left-12 top-1">
                <span class="text-[11px] font-semibold text-gray-500
                             bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                    {{ $supplier->founded_year ?? '2015' }}
                </span>
            </div>

            <div class="bg-white border border-gray-200/70 shadow-sm
                        rounded-2xl p-6 max-w-lg">

                <div class="font-semibold text-gray-900">
                    Company Established
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Initial market entry and operational setup.
                </p>

            </div>
        </div>

        {{-- Expansion --}}
        <div class="relative">

            <div class="absolute -left-12 top-1">
                <span class="text-[11px] font-semibold text-emerald-700
                             bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                    {{ now()->year - 2 }}
                </span>
            </div>

            <div class="bg-white border border-gray-200/70 shadow-sm
                        rounded-2xl p-6 max-w-lg">

                <div class="font-semibold text-gray-900">
                    Production Expansion Phase
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Capacity scaling and strengthened global operations.
                </p>

            </div>
        </div>

    </div>
</div>


{{-- ================= SUPPLIER REVIEWS ================= --}}
<div class="space-y-8">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-700 pl-4">
        Supplier Reviews
    </h2>

    <div class="bg-white border border-gray-200/70 rounded-3xl shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-10 py-8 bg-gradient-to-r from-slate-50 via-[#f4f1eb] to-[#ebe5dc] border-b border-gray-200">

            

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                {{-- Rating Summary --}}
                <div class="flex items-center gap-6">

                    <div class="text-4xl font-semibold text-gray-900">
                        {{ $supplierRating ?: '—' }}
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($supplierRating))
                                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                    </svg>
                                @elseif ($i - $supplierRating < 1)
                                    <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>

                        <div class="text-sm text-gray-500 mt-1">
                            Based on {{ $count }} {{ Str::plural('review', $count) }}
                        </div>
                    </div>

                </div>

                {{-- Optional CTA --}}
                <div class="text-sm text-gray-500">
                    Verified client feedback from completed orders
                </div>

            </div>
        </div>


        {{-- BODY --}}
        <div class="px-10 py-10 bg-white">

            @forelse($supplier->supplierReviews->take(5) as $review)

                @php
                    $user = $review->order->user ?? null;
                @endphp

                <div class="py-8 border-b border-gray-100 last:border-none">

                    <div class="flex gap-6">

                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            @if($user && $user->photo)
                                <img 
                                    src="{{ asset('storage/' . $user->photo) }}" 
                                    class="w-14 h-14 rounded-full object-cover"
                                >
                            @else
                                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-lg">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $user->name ?? 'Anonymous' }} {{ $user->last_name ?? '' }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $review->created_at->format('d M Y') }}
                                    </div>
                                </div>

                                {{-- Rating --}}
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>

                            </div>

                            {{-- Comment --}}
                            <div class="mt-4 text-sm text-gray-700 leading-relaxed max-w-3xl">
                                {{ $review->comment }}
                            </div>

                        </div>

                    </div>

                </div>

            @empty
                <div class="text-center py-16 text-gray-400 text-sm">
                    No reviews yet.
                </div>
            @endforelse

        </div>

    </div>

</div>




</div>