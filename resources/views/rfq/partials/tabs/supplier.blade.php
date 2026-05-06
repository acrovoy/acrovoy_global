{{-- SUPPLIER TABS --}}

<div
    x-data="{ openReviews: false }"
    class="text-[13px] font-medium tracking-wide text-gray-700"
>

@php
    function tabClass($active) {
        return $active ? 'bg-black/5' : 'bg-white';
    }
@endphp

{{-- OVERVIEW --}}
<a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
   class="group relative flex justify-between items-center py-3 px-2 rounded-md
          hover:bg-black/5 transition-all duration-200 {{ tabClass($activeTab === 'overview') }}">

    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                 group-hover:h-5 transition-all duration-200"></span>

    <span class="uppercase group-hover:text-black transition">
        RFQ SUMMARY
    </span>

    <span class="group-hover:translate-x-1 transition">
        →
    </span>
</a>

{{-- REQUIREMENTS --}}
<div class="border-t border-gray-100 py-3 px-2 flex justify-between items-center text-gray-700">
    <span class="uppercase">Requirements</span>
    <span>↓</span>
</div>

@include('rfq.partials.aside-products.supplier', [
    'rfq' => $rfq,
    'activeTab' => $activeTab
])

{{-- ORDERER --}}
<div class="border-t border-gray-100 py-3 px-2 flex justify-between items-center text-gray-700">
    <span class="uppercase">Orderer</span>
    
</div>




{{-- ORDERER CARD --}}
@include('rfq.partials.company-card.buyer', [
    'supplier' => $rfq->buyer ?? null
])

</div>