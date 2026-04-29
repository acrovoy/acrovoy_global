@extends('dashboard.layout')

@section('dashboard-content')

@include('rfq.partials.header', ['rfq' => $rfq])



<div class="space-y-6">

    

    {{-- ========================= --}}
    {{-- OFFER SUMMARY --}}
    {{-- ========================= --}}
    <div class="bg-white border rounded-xl p-6">

        @if(!$offer)

            {{-- EMPTY STATE --}}
            <div class="text-center py-6">

                <h2 class="font-semibold text-gray-900 mb-2">
                    No Offer Yet
                </h2>

                <p class="text-sm text-gray-500 mb-4">
                    You haven’t submitted an offer for this RFQ yet.
                </p>

                <a href="#"
                   class="inline-block px-4 py-2 bg-black text-white text-sm rounded-lg">
                    Create Offer
                </a>

            </div>

        @else

            <div class="flex justify-between items-center mb-4">

                <h2 class="font-semibold text-gray-900">
                    Your Offer
                </h2>

                <div class="text-right">

                    <div class="text-lg font-semibold">
                        ${{ $offer->latestVersion->total_price ?? 0 }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ $offer->latestVersion->lead_time_days ?? '-' }} days lead time
                    </div>

                </div>

            </div>

            <div class="mb-2">
                <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                    {{ $offer->status->label() }}
                </span>
            </div>

            @if($offer->latestVersion?->comment)

                <div class="text-sm text-gray-600 border-l-2 pl-3">
                    {{ $offer->latestVersion->comment }}
                </div>

            @endif

        @endif

    </div>

    {{-- ========================= --}}
    {{-- VERSION HISTORY --}}
    {{-- ========================= --}}
    @if($offer)

        <div class="bg-white border rounded-xl p-6">

            <h2 class="font-semibold mb-4">
                Version History
            </h2>

            <div class="space-y-3">

                @foreach($offer->versions as $version)

                    <div class="border rounded-lg p-4">

                        <div class="flex justify-between">

                            <div class="text-sm font-medium">
                                Version #{{ $version->version_number }}
                            </div>

                            <div class="text-xs text-gray-400">
                                {{ $version->created_at->format('Y-m-d H:i') }}
                            </div>

                        </div>

                        <div class="text-sm text-gray-600 mt-2">

                            <div>
                                Total: ${{ $version->total_price }}
                            </div>

                            <div>
                                Lead time: {{ $version->lead_time_days ?? '-' }} days
                            </div>

                        </div>

                        @if($version->comment)

                            <div class="text-xs text-gray-500 mt-1">
                                {{ $version->comment }}
                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>

    @endif

    {{-- ========================= --}}
    {{-- ACTIONS --}}
    {{-- ========================= --}}
    @if($offer)

        <div class="flex gap-2">

            @if($offer->status !== 'accepted')

                <form method="POST"
                      action="{{ route('supplier.rfq.offers.reject', [$rfq->id, $offer->id]) }}">

                    @csrf

                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg">
                        Reject Offer
                    </button>

                </form>

            @endif

        </div>

    @endif

</div>

@endsection