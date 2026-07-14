{{-- BUYER TABS --}}


<div class="text-[13px] font-medium tracking-wide text-gray-700">

    @php

$activeTab ??= 'overview';

function tabClass($active)
{
    return $active ? 'bg-black/5' : 'bg-white';
}

/*
|--------------------------------------------------------------------------
| ALL SUBMITTED OFFERS FROM PROJECT RFQS
|--------------------------------------------------------------------------
*/

$offers = $project->rfqs
    ->flatMap(function ($rfq) {

        return $rfq->offers;

    })

    ->filter(function ($offer) {

        return $offer->latestVersion
            && $offer->latestVersion->status !== 'draft';

    })

    /*
    |--------------------------------------------------------------------------
    | one card per supplier
    |--------------------------------------------------------------------------
    */
    ->unique(function ($offer) {

        return $offer->participant_type.'_'.$offer->participant_id;

    })

    ->values();

@endphp

    {{-- OVERVIEW --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $project->id, 'tab' => 'overview']) }}"
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

        $status = $version?->status;

        $base = 'group flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg border shadow-sm transition-all duration-200';

        $styles = match ($status) {

        'accepted' => 'bg-green-50 border-green-200 hover:bg-green-100 hover:border-green-300',

        'rejected' => 'bg-gray-100 border-gray-200 opacity-60 hover:opacity-80',

        default => 'bg-gradient-to-b from-white via-gray-50 to-gray-100 border-gray-200 hover:bg-black/5 hover:border-gray-300'
        };

        @endphp

        <a href=""
            class="{{ $base }} {{ $styles }}">

            {{-- LEFT --}}
            <div class="flex items-center gap-3 min-w-0">

                {{-- DOT --}}
                <div class="w-1.5 h-1.5 rounded-full
            {{ $status === 'accepted'
                ? 'bg-green-500'
                : ($status === 'rejected'
                    ? 'bg-gray-400'
                    : 'bg-green-400') }}">
                </div>

                {{-- TEXT --}}
                <div class="min-w-0">

                    <div class="text-sm truncate
                {{ $status === 'accepted'
                    ? 'text-green-800'
                    : ($status === 'rejected'
                        ? 'text-gray-500'
                        : 'text-blue-800') }}">
                        <div class="text-sm font-semibold text-gray-900 truncate">
    {{ $supplier instanceof \App\Models\User
        ? trim($supplier->name . ' ' . $supplier->last_name)
        : ($supplier?->name ?? 'Unknown supplier')
    }}

    @if($supplier instanceof \App\Models\User && $supplier->email)
        <span class="text-xs text-gray-400 font-normal">
            ({{ $supplier->email }})
        </span>
    @endif
</div>
                    </div>

                    <div class="text-[11px] text-gray-400 mt-0.5">
                        Version 
                    </div>

                </div>

            </div>

            {{-- ARROW --}}
            <div class="transition
        {{ $status === 'rejected' ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-600' }}">
                →
            </div>

        </a>

        @endforeach


        <div class="flex justify-end mt-3">

            <a
                href=""
                class="px-4 py-1.5 text-sm rounded-md
               bg-white text-gray-700
               hover:bg-gray-900 hover:text-white hover:border-gray-900
               transition">
                Compare offers
            </a>

        </div>

    </div>

    @endif








    {{-- REQUIREMENTS --}}
    <div class="py-3 px-2 flex justify-between items-center text-gray-700 border-t border-gray-100 mt-3">
        <span class="uppercase">Procurement Items</span>
        <span>↓</span>
    </div>



    @include('project.partials.aside-products.buyer', [ 'project' => $project ])


<div
    x-data="{ open: false }"
    class="relative mt-3 px-2">

    <button
        @click="open = !open"
        class="w-full flex items-center justify-center gap-2
               rounded-lg border border-gray-200 bg-white
               px-4 py-2 text-sm font-medium text-gray-700
               hover:bg-gray-50 hover:border-gray-300
               transition">

        <span class="text-base leading-none">+</span>

        <span>Add Item</span>

    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute left-2 right-2 mt-2 z-20
               rounded-xl border border-gray-200 bg-white
               shadow-xl overflow-hidden">

        <a
            href="{{ route('catalog.index') }}"
            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">

            <div class="text-lg">📦</div>

            <div>
                <div class="text-sm font-medium text-gray-900">
                    Add from Catalog
                </div>

                <div class="text-xs text-gray-500">
                    Select an existing product from the marketplace catalog.
                </div>
            </div>

        </a>

        <div class="border-t border-gray-100"></div>

        <a
            href="#"
            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">

            <div class="text-lg">✨</div>

            <div>
                <div class="text-sm font-medium text-gray-900">
                    Create and Add New
                </div>

                <div class="text-xs text-gray-500">
                    Create a custom RFQ for a product not available in the catalog.
                </div>
            </div>

        </a>

    </div>

</div>
    



    {{-- PARTICIPANTS --}}
    <a href="{{ route('buyer.projects.participants', $project) }}"
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