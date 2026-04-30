{{-- BUYER TABS --}}


<div class="divide-y text-[13px] font-medium tracking-wide text-gray-700">

@php
        function tabClass($active) {
        return $active
        ? 'bg-gray-50'
        : 'bg-white';
        }
        @endphp

    {{-- OVERVIEW --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
        class="group relative flex justify-between items-center py-3 px-2 rounded-md
              hover:bg-gray-50 transition-all duration-200 {{ tabClass($activeTab === 'overview') }}">

        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                     group-hover:h-5 transition-all duration-200"></span>

        <span class="uppercase group-hover:text-black transition">
            Overview1
        </span>

        <span class="group-hover:translate-x-1 transition">
            →
        </span>
    </a>

    {{-- REQUIREMENTS --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'requirements']) }}"
        class="group relative flex justify-between items-center py-3 px-2 rounded-md
              hover:bg-black/5 transition-all duration-200 {{ tabClass($activeTab === 'requirements') }}">

        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                     group-hover:h-5 transition-all duration-200"></span>

        <span class="uppercase group-hover:text-black transition">
            Requirements
        </span>

        <span class="group-hover:translate-x-1 transition">
            →
        </span>
    </a>

    {{-- PARTICIPANTS --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'participants']) }}"
        class="group relative flex justify-between items-center py-3 px-2 rounded-md
              hover:bg-gray-50 transition-all duration-200 {{ tabClass($activeTab === 'participants') }}">

        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                     group-hover:h-5 transition-all duration-200"></span>

        <span class="uppercase group-hover:text-black transition">
            Participants
        </span>

        <span class="group-hover:translate-x-1 transition">
            →
        </span>
    </a>

    {{-- SUPPLIER OFFERS --}}
    <div class="py-3 px-2 flex justify-between items-center text-gray-700">
        <span class="uppercase">Supplier’s offers</span>
        <span>↓</span>
    </div>

    <div class="text-[11px] text-gray-400 pb-3 px-2 normal-case tracking-normal">
        No offers submitted at time
    </div>

</div>