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
                    $version = $offer->versions
                    ->where('status', 'submitted')
                    ->sortByDesc('id')
                    ->first();
                    @endphp

                    <th class="min-w-[150px] px-5 py-4 border-r align-top">

                        <div class="flex flex-col h-full">

                            {{-- TOP CONTENT --}}
                            <div>

                                <div class="text-sm font-semibold text-gray-900 truncate whitespace-nowrap overflow-hidden max-w-[180px]"
                                    title="{{ $supplier?->name }}">
                                    {{ $supplier?->name }}
                                </div>

                                <div class="text-xs text-gray-500 mt-0.5">
                                    v{{ $version?->version_number }}
                                </div>

                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $version?->created_at?->format('M d, H:i') }}
                                </div>

                            </div>

                            {{-- ACTIONS (BOTTOM LEFT) --}}
                            <div class="mt-auto pt-3 flex items-center justify-start gap-2">

                                {{-- ACCEPT --}}
                                <form method="POST" action="">
                                    @csrf

                                    <button type="submit"
                                        class="px-2.5 py-1 text-[11px] font-medium rounded-md
                               border border-green-200 text-green-700
                               bg-green-50 hover:bg-green-100 transition">
                                        Accept
                                    </button>
                                </form>

                                <!-- {{-- REJECT --}}
                                <form method="POST" action="">
                                    @csrf

                                    <button type="submit"
                                        class="px-2.5 py-1 text-[11px] font-medium rounded-md
                               border border-gray-200 text-gray-700
                               bg-gray-50 hover:bg-gray-100 transition">
                                        Close negotiation
                                    </button>
                                </form> -->

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
                    ->where('status', 'submitted')
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

            </tbody>

        </table>

    </div>

</div>

@endsection