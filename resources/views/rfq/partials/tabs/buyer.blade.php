{{-- BUYER TABS --}}


<div class="text-[13px] font-medium tracking-wide text-gray-700">

@php
        function tabClass($active) {
        return $active
        ? 'bg-black/5'
        : 'bg-white';
        }
        @endphp

    {{-- OVERVIEW --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
        class="group relative flex justify-between items-center py-3 px-2 rounded-md
              hover:bg-black/5 transition-all duration-200 {{ tabClass($activeTab === 'overview') }}">

        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                     group-hover:h-5 transition-all duration-200"></span>

        <span class="uppercase group-hover:text-black transition">
            Overview
        </span>

        <span class="group-hover:translate-x-1 transition">
            →
        </span>
    </a>

   

   

    {{-- SUPPLIER OFFERS --}}
    <div class="border-t border-gray-100 py-3 px-2 flex justify-between items-center text-gray-700">
        <span class="uppercase">Supplier’s offers</span>
        <span>↓</span>
    </div>

    @php
    $offers = $rfq->offers ?? collect();
@endphp

@if($offers->isEmpty())

    <div class="mt-3 mx-2 p-3 rounded-lg bg-gray-50 border border-gray-100">
        <div class="text-xs text-gray-500 leading-relaxed">
            No offers yet
        </div>

        <div class="text-[11px] text-gray-400 mt-1">
            Suppliers will appear here once they submit proposals
        </div>
    </div>

@else

  <div class="mt-3 space-y-2">

    @foreach($offers as $offer)

        @php
            $supplier = $offer->participant;
            $version = $offer->latestVersion;
        @endphp

        <a href="{{ route('rfqs.workspace', [
            'rfq' => $rfq->id,
            'tab' => 'offers',
            'offer' => $offer->id
        ]) }}"
           class="group flex items-center justify-between gap-3 px-3 py-2.5
                  rounded-lg border border-gray-200 bg-gradient-to-b from-white via-gray-50 to-gray-100 shadow-sm
                  transition-all duration-200
                  hover:bg-black/5 hover:border-gray-300 hover:shadow">

            {{-- LEFT --}}
            <div class="flex items-center gap-3 min-w-0">

                {{-- DOT --}}
                <div class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-gray-600"></div>

                {{-- TEXT --}}
                <div class="min-w-0">

                    <div class="text-sm text-gray-800 truncate">
                        {{ $supplier?->name ?? 'Unknown supplier' }}
                    </div>

                    <div class="text-[11px] text-gray-400 mt-0.5">
                        Version {{ $version?->version_number ?? '-' }}
                    </div>

                </div>

            </div>

            {{-- ARROW --}}
        <div class="text-gray-300 group-hover:text-gray-500 transition">
            →
        </div>

        </a>

    @endforeach

</div>

@endif








     {{-- REQUIREMENTS --}}
    <div class="py-3 px-2 flex justify-between items-center text-gray-700 border-t border-gray-100 mt-3">
        <span class="uppercase">Requirements</span>
        <span>↓</span>
    </div>


    @include('rfq.partials.aside-products.buyer', [
            'rfq' => $rfq,
            'activeTab' => $activeTab
            ])



     {{-- PARTICIPANTS --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'participants']) }}"
        class="border-t border-gray-100 group relative flex justify-between items-center py-3 px-2 mt-4 rounded-md
              hover:bg-black/5 transition-all duration-200 {{ tabClass($activeTab === 'participants') }}">

        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-black
                     group-hover:h-5 transition-all duration-200"></span>

        <span class="uppercase group-hover:text-black transition">
            Participants
        </span>

        <span class="group-hover:translate-x-1 transition">
            →
        </span>
    </a>


</div>