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
        @if(!$rfq->customization)
        <span class="uppercase">Supplier’s offers</span>
        @else
        <span class="uppercase">Supplier’s offer</span>
        @endif
        <span>↓</span>
    </div>




    @php
    $offers = ($rfq->offers ?? collect())->filter(function ($offer) {
    $version = $offer->latestVersion;

    return $version && in_array($version->status, [
        'submitted',
        'accepted',
        'rejected',
    ]);
});
    @endphp

    @if($offers->isEmpty())
@if(!$rfq->customization)
    <div class="mt-3 mx-2 p-3 rounded-lg bg-gray-50 border border-gray-100">
        <div class="text-xs text-gray-500 leading-relaxed">
            No offers yet
        </div>

        <div class="text-[11px] text-gray-400 mt-1">
            Suppliers will appear here once they submit proposals
        </div>
    </div>
@else
<div class="mt-3 mx-2 p-3 rounded-lg bg-gray-50 border border-gray-100">
        <div class="text-xs text-gray-500 leading-relaxed">
            No offer yet
        </div>

        <div class="text-[11px] text-gray-400 mt-1">
            Supplier will appear here once they submit proposal
        </div>
    </div>
@endif
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

        <a href="{{ route('rfqs.workspace', [
        'rfq' => $rfq->id,
        'tab' => 'offers',
        'offer' => $offer->id
    ]) }}"
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
                        Version {{ $version?->version_number ?? '-' }}
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
                href="{{ route('buyer.rfqs.offer-comparison', $rfq->id) }}"
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
        <span class="uppercase">Requirements</span>
        <span>↓</span>
    </div>


    @include('rfq.partials.aside-products.buyer', [
    'rfq' => $rfq,
    'activeTab' => $activeTab
    ])

@if(!$rfq->customization)

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
@endif

</div>