{{-- SUPPLIER TABS --}}

<div class="text-[13px] font-medium tracking-wide text-gray-700">

@php

    $activeTab ??= 'overview';

    function tabClass($active)
    {
        return $active
            ? 'bg-black/5'
            : 'bg-white';
    }

$buyer = $project->buyer;


@endphp

{{-- OVERVIEW --}}
<a
    href="{{ route('supplier.projects.show', $project) }}"
    class="group relative flex justify-between items-center py-3 px-2 rounded-md
           hover:bg-black/5 transition-all duration-200
           {{ tabClass($activeTab === 'overview') }}">

    <span
        class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
               group-hover:h-5 transition-all duration-200">
    </span>

    <span class="uppercase">
        Project Overview
    </span>

    <span class="group-hover:translate-x-1 transition">
        →
    </span>

</a>

{{-- PROCUREMENT ITEMS --}}
<div class="border-t border-gray-100 py-3 px-2 flex justify-between items-center text-gray-700">

    <span class="uppercase">
        Procurement Items
    </span>

    <span>↓</span>

</div>

@include('project.partials.aside-products.supplier', [
    'project' => $project,
    'activeTab' => $activeTab,
])



{{-- BUYER --}}
<div class="border-t border-gray-100 py-3 px-2 flex justify-between items-center text-gray-700">

    <span class="uppercase">
        Buyer
    </span>

</div>

@include('rfq.partials.company-card.buyer', [
    'buyer' => $buyer ?? null,
])

</div>