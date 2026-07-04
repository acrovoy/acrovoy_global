{{-- resources/views/rfq/workspace/offer-comparison.blade.php --}}

@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('rfq.partials.aside-panel', [
'rfq' => $rfq,
'activeTab' => 'offers'
])

@endsection

@section('dashboard-content')


{{-- BACK --}}
<a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
    class="text-sm text-gray-500 hover:text-gray-900 transition">

    ← Back to RFQ Overview
</a>


<x-alerts />




<div class="mb-6">



    <div class="flex items-start justify-between gap-6">

        {{-- LEFT --}}
        <div class="flex-1 min-w-0">

            <!-- <div class="text-xs text-gray-500 mb-1">
                    RFQ Comparison
                </div> -->

            <h1 class="text-xl font-semibold text-gray-900">
                Supplier Offers Comparison
            </h1>

            <div class="text-sm text-gray-500 mt-1">
                Compare prices, terms and supplier responses
            </div>

        </div>

    </div>



</div>

{{-- ========================= --}}
{{-- TABLE --}}
{{-- ========================= --}}

<div class="border border-gray-200 rounded-lg overflow-hidden">

    <div class="overflow-x-auto">

        <table class="min-w-full">

            {{-- HEADER --}}
            <thead>
                <tr class="bg-gray-50 border-b">

                    <th class="sticky left-0 bg-gray-50 z-20 px-6 py-4 text-left min-w-[200px] border-r">
                        <div class="text-xs text-gray-500 uppercase">
                            Requirements
                        </div>
                    </th>

                    @foreach($offers as $offer)

                    @php
                    $supplier = $offer->participant;
                    $isUser = $supplier instanceof \App\Models\User;
                    $isSupplierCompany = $supplier instanceof \App\Models\Supplier;
                    $version = $offer->versions
                    ->where('status', '!=', 'draft')
                    ->sortByDesc('id')
                    ->first();

                    $version = $version ?? null;

                    $level = $product->supplier->level ?? 'Basic';
                    @endphp

                    <th class="min-w-[220px] px-5 py-5 border-r align-top bg-white">

                        <div class="flex flex-col h-full gap-3">

                            {{-- SUPPLIER HEADER --}}
                            <div class="flex items-start gap-3">

                                {{-- LOGO --}}
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border">

    @if($isUser)

        <img src="{{ $supplier->avatar()?->cdn_url ?? asset('images/default-avatar.png') }}"
             class="w-full h-full object-cover">

    @elseif($isSupplierCompany)

        @if($supplier->logo?->cdn_url)
            <img src="{{ $supplier->logo->cdn_url }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                <span class="text-xs font-semibold text-gray-500">
                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                </span>
            </div>
        @endif

    @else

        <div class="w-full h-full flex items-center justify-center bg-gray-200">
            <span class="text-xs text-gray-500">?</span>
        </div>

    @endif

</div>



                                {{-- NAME + META --}}
                                <div class="min-w-0">

                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                        {{ trim($supplier->name . ' ' . $supplier->last_name) }}
                                    </div>

                                    {{-- TRUST BADGES --}}
                                    <div class="flex flex-wrap items-center gap-1">

                                        @if($supplier?->is_verified)
                                        <span class="px-1.5 py-[2px] text-[9px] font-medium bg-blue-50 text-blue-700 border border-blue-100 rounded">
                                            VERIFIED
                                        </span>
                                        @endif

                                        @if($supplier?->is_trusted)
                                        <span class="px-1.5 py-[2px] text-[9px] font-medium bg-green-50 text-green-700 border border-green-100 rounded">
                                            TRUSTED
                                        </span>
                                        @endif

                                        @if($supplier?->is_premium)
                                        <span class="px-1.5 py-[2px] text-[9px] font-medium bg-purple-50 text-purple-700 border border-purple-100 rounded">
                                            PREMIUM
                                        </span>
                                        @endif

                                        @if($supplier?->level)

                                        @php
                                        $level = strtoupper($supplier->level);
                                        @endphp

                                        <span class="px-1.5 py-[2px] text-[9px] font-semibold uppercase rounded
            @if($level === 'PLATINUM')
                bg-gray-900 text-white
            @elseif($level === 'GOLD')
                bg-amber-100 text-amber-700 border border-amber-200
            @elseif($level === 'SILVER')
                bg-gray-100 text-gray-700 border border-gray-200
            @else
                bg-white text-gray-500 border border-gray-300
            @endif
        ">
                                            {{ $supplier->level }}
                                        </span>

                                        @endif

                                    </div>

                                    <div class="text-[11px] text-gray-400 mt-3">
                                        Version {{ $version?->version_number }}
                                    </div>

                                    <div class="text-[11px] text-gray-400">
                                        {{ $version?->created_at?->format('M d, H:i') }}
                                    </div>

                                </div>

                            </div>





                            {{-- ACTIONS --}}
<div class="mt-auto pt-2 flex gap-2">

    @if(optional($version)->status === 'submitted')

        <form method="POST"
              action="{{ route('buyer.rfqs.offers.versions.accept', [
                    'rfq' => $rfq->id,
                    'offer' => $offer->id,
                    'version' => $version->id,
              ]) }}">
            @csrf

            <button type="submit"
                class="px-3 py-1 text-[11px] font-medium rounded-md
                       border border-green-200 text-green-700
                       bg-green-50 hover:bg-green-100 transition">
                Accept
            </button>
        </form>

    @elseif(optional($version)->status === 'accepted')

        <span class="px-3 py-1 text-[11px] font-medium rounded-md
                     bg-green-100 text-green-700 border border-green-200">
            Accepted
        </span>

    @elseif(optional($version)->status === 'rejected')

        <span class="px-3 py-1 text-[11px] font-medium rounded-md
                     bg-gray-100 text-gray-500 border border-gray-200">
            Rejected
        </span>

    @else

        <span class="px-3 py-1 text-[11px] font-medium rounded-md
                     bg-gray-50 text-gray-500 border border-gray-200">
            {{ ucfirst(optional($version)->status) }}
        </span>

    @endif

</div>

                        </div>

                    </th>

                    @endforeach

                </tr>
            </thead>

            {{-- BODY --}}
            <tbody>

                @foreach($rfq->attributeValues as $value)

                @php
                $attribute = $value->attribute;
                @endphp

                <tr class="border-b">

                    {{-- REQUIREMENT --}}
                    <td class="sticky left-0 z-10 px-4 py-4 border-r bg-gray-50/60 backdrop-blur-sm w-[100px] min-w-[100px]">

                        {{-- ATTRIBUTE NAME --}}
                        <div class="text-sm font-semibold text-gray-900 leading-snug">
                            {{ $attribute->name }}
                        </div>

                        {{-- VALUE + OPTIONS --}}
                        <div class="mt-2 text-xs text-gray-600 space-y-2">

                            {{-- VALUE --}}
                            <div class="text-gray-700 font-medium">
                                {{ $value->value_text
                ?? $value->value_number
                ?? $value->value_date
                ?? '—' }}
                            </div>

                            {{-- SELECT --}}
                            @if($attribute->type === 'select')

                            <div class="flex flex-wrap gap-1">

                                @foreach($attribute->options as $option)

                                <span class="px-2 py-0.5 text-[11px] rounded-md
                        {{ (int)$value->attribute_option_id === (int)$option->id
                            ? 'bg-blue-100 text-blue-700 font-medium'
                            : 'bg-gray-100 text-gray-500'
                        }}">
                                    {{ $option->translatedValue() }}
                                </span>

                                @endforeach

                            </div>

                            @endif

                            {{-- MULTISELECT --}}
                            @if($attribute->type === 'multiselect')

                            @php
                            $selectedIds = $value->options?->pluck('id')->toArray() ?? [];
                            @endphp

                            <div class="flex flex-wrap gap-1">

                                @foreach($attribute->options as $option)

                                @php
                                $isSelected = in_array($option->id, $selectedIds);
                                @endphp

                                <span class="px-2 py-0.5 text-[11px] rounded-md
                        {{ $isSelected
                            ? 'bg-blue-100 text-blue-700 font-medium'
                            : 'bg-gray-100 text-gray-400'
                        }}">
                                    {{ $option->translatedValue() }}
                                </span>

                                @endforeach

                            </div>

                            @endif

                        </div>
                    </td>

                    {{-- OFFERS --}}
                    @foreach($offers as $offer)

                    @php
                    $version = $offer->versions
                    ->where('status', '!=', 'draft')
                    ->sortByDesc('id')
                    ->first();

                    $item = $version?->items
                    ?->firstWhere('attribute_id', $attribute->id);
                    @endphp

                    <td class="px-5 py-4 border-r align-top">

                        <div class="space-y-2">

                            {{-- PRICE --}}
                            <div>
                                <div class="text-[11px] text-gray-400 uppercase">
                                    Price
                                </div>

                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $item?->unit_price
                                                ? number_format((float)$item->unit_price, 2)
                                                : '-' }}
                                </div>
                            </div>

                            {{-- NOTES --}}
                            <div>
                                <div class="text-[11px] text-gray-400 uppercase">
                                    Notes
                                </div>

                                <div class="text-sm text-gray-700">
                                    {{ $item?->notes ?: '—' }}
                                </div>
                            </div>

                            {{-- OPTIONS --}}


                            @if($item?->options?->count())
                            <div class="flex flex-wrap gap-1 mt-1">

                                @foreach($item->options as $option)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[11px] rounded-md">
                                    {{ $option->translatedValue() }}
                                </span>
                                @endforeach

                            </div>
                            @endif

                        </div>

                    </td>

                    @endforeach

                </tr>

                
                @endforeach




                <tr class="border-b bg-gray-50">

    <td class="sticky left-0 z-10 px-4 py-4 border-r bg-gray-50 text-sm font-semibold text-gray-900 leading-snug">
        Grand Total
    </td>

    @foreach($offers as $offer)

        @php
            $version = $offer->versions
                ->where('status', '!=', 'draft')
                ->sortByDesc('id')
                ->first();
        @endphp

        <td class="px-5 py-4 border-r align-middle">

            @if($version?->total_price)

                <div class="text-lg font-semibold text-gray-900">
                    ${{ number_format((float)$version->total_price, 2) }}
                </div>

            @else

                <span class="text-gray-400">—</span>

            @endif

        </td>

    @endforeach

</tr>



                <tr class="border-b bg-gray-50">

                    <td class="sticky left-0 z-10 px-4 py-4 border-r bg-gray-50 text-sm font-semibold text-gray-900 leading-snug">
                        Delivery
                    </td>

                    @foreach($offers as $offer)

                    <td class="px-5 py-4 border-r align-top">

                        @php
                        $addressa = App\Models\UserAddress::find($rfq->delivery_address_id);

    if (!$addressa) {
        $cityId = null;
    } else {
        $eexistingLocation = \App\Models\Location::where('name', $addressa->city)
            ->where('parent_id', $addressa->region)
            ->first();

        $cityId = $eexistingLocation?->id;
    }

    $participant = $offer->participant;

    $shippingTemplates = collect();

    if ($participant && $cityId && method_exists($participant, 'shippingTemplates')) {

        $shippingTemplates = collect($participant->shippingTemplates ?? [])
            ->filter(fn ($template) =>
                $template->locations?->contains('id', $cityId)
            );
    }


                       
                        @endphp

                        @forelse($shippingTemplates as $shippingTemplate)

                        <div class="px-2 border-b last:border-0">

                            <div class="text-sm font-medium text-gray-900 mt-3">
                                {{ $shippingTemplate->title }}
                            </div>
                            <div class="text-[12px] text-gray-600 ">

                                <span class="text-[12px] text-gray-400 font-medium">
                                    Delivery Time
                                </span>

                                <span class="font-semibold text-gray-400">
                                    {{ $shippingTemplate->delivery_time }} days
                                </span>

                            </div>

                            <div class="text-sm text-gray-500 mb-3">
                                ${{ number_format($shippingTemplate->price, 2) }}
                            </div>

                        </div>

                        @empty

                        <span class="text-gray-400 text-sm">
                            No delivery options
                        </span>

                        @endforelse

                    </td>

                    @endforeach

                </tr>
            </tbody>

        </table>

    </div>

</div>

@endsection