<div class="w-56 bg-white rounded-2xl shadow-xl overflow-hidden">
<div
   class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm transition-all duration-300 overflow-hidden supplier-card"
   data-country="{{ $company->country->name ?? '' }}">

@php
    $score = $company->reputation ?? 0;

    if ($score <= 50) {
        $level = 'Basic';
        $color = 'bg-gray-200';
        $textColor = 'text-gray-600';
        $nextLevelScore = 51;
    } elseif ($score <= 120) {
        $level = 'Silver';
        $color = 'bg-gray-400';
        $textColor = 'text-white';
        $nextLevelScore = 121;
    } elseif ($score <= 200) {
        $level = 'Gold';
        $color = 'bg-yellow-500';
        $textColor = 'text-white';
        $nextLevelScore = 201;
    } else {
        $level = 'Platinum';
        $color = 'bg-blue-600';
        $textColor = 'text-white';
        $nextLevelScore = max($score, 1);
    }

    $progress = ($score / $nextLevelScore) * 100;
    $progress = min($progress, 100);

    $rating = round($company->reviews()->avg('rating') ?? 0, 1);
@endphp



{{-- Image --}}
<div class="overflow-hidden">
    <img src="{{ $company->catalog_image ? asset('storage/' . $company->catalog_image) : asset('images/no-logo.png') }}"
         class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105"
         alt="{{ $company->name }}">
</div>



{{-- Badge --}}
<div class="px-5 pt-3">

    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold tracking-wide shadow-xl
        {{ $level === 'Basic' ? 'bg-gray-50 text-gray-400 border border-gray-200' : '' }}
        {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
        {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
        {{ $level === 'Platinum' ? 'bg-slate-800 text-white border border-slate-700' : '' }}">

        @if($level === 'Basic')
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="8"/>
            </svg>
        @elseif($level === 'Silver')
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M12 3l7 18H5l7-18z"/>
            </svg>
        @elseif($level === 'Gold')
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M5 12l5 5L20 7"/>
            </svg>
        @elseif($level === 'Platinum')
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M12 2l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-7.8L2 9h7z"/>
            </svg>
        @endif

        {{ strtoupper($level) }}
    </span>

</div>



{{-- Content --}}
<div class="p-5 pt-2 text-center flex flex-col items-center space-y-4">

    {{-- Title --}}
    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-1 justify-center group-hover:text-black transition">
        {{ $company->name }}

        @if($company->is_verified)
            <img src="{{ asset('images/icons/verified_icon.png') }}"
                 alt="Verified"
                 class="w-4 h-4">
        @endif
    </h3>



    {{-- Country --}}
    <p class="text-xs text-gray-500">
        {{ $company->country->name ?? '' }}
    </p>



    {{-- Info Table --}}
    <div class="text-xs text-gray-600 space-y-2 border-t pt-4 w-full pb-4">

        {{-- Supplier Types --}}
        @php
$types = optional($company->supplierTypes)
    ->map(fn($type) => $type->translation?->name ?? $type->slug)
    ->filter()
    ->values();
@endphp

        @if($types->isNotEmpty())
        <div class="flex justify-between items-center gap-2">
            <span class="flex items-center gap-1.5">Type</span>

            <span class="font-medium text-gray-800 text-right">
                {{ $types->implode(', ') }}
            </span>
        </div>
        @endif



        {{-- Years --}}
        <div class="flex justify-between items-center gap-2">
            <span class="flex items-center gap-1.5">Years</span>

            <span class="font-medium text-gray-800">
                {{ $company->years_on_platform }}+
            </span>
        </div>



        {{-- Export Markets --}}
        @php
            $markets = $company->exportMarkets
                ->map(fn($m) => $m->translation?->name ?? $m->slug)
                ->filter();
        @endphp

        @if($markets->isNotEmpty())
        <div class="flex justify-between items-start gap-2">
            <span>Export</span>

            <div class="flex flex-wrap gap-1 justify-end">
                @foreach($markets as $market)
                    <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 shadow-sm">
                        {{ $market }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif



        {{-- MOQ --}}
        @if($company->moq_range)
        <div class="flex justify-between items-center">
            <span>MOQ</span>

            <span class="font-medium text-gray-800">
                {{ $company->moq_range['min'] }} - {{ $company->moq_range['max'] }}
            </span>
        </div>
        @endif



        {{-- Rating --}}
        <div class="flex justify-between items-center">
            <span>Rating</span>

            <span class="font-medium text-gray-800">
                {{ $rating }} / 5
            </span>
        </div>



        {{-- Orders --}}
        <div class="flex justify-between items-center">
            <span>Completed Orders</span>

            <span class="font-medium text-gray-800">28+</span>
        </div>



        {{-- Logistics --}}
        @if($company->has_logistics)
        <div class="flex justify-between items-center text-green-700">
            <span>Logistics</span>

            <span class="font-medium">Available</span>
        </div>
        @endif

    </div>

</div>



{{-- Footer Badge --}}
@if($company->is_trusted == 1)
<div class="absolute bottom-0 left-0 w-full bg-emerald-900 text-white text-xs font-semibold tracking-wide text-center py-2">
    TRUSTED SUPPLIER
</div>
@else
<div class="absolute bottom-0 left-0 w-full bg-gray-200 text-white text-xs font-semibold tracking-wide text-center py-2">
    STANDARD
</div>
@endif

</div>
</div>