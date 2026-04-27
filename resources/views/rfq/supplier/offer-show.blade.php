@extends('dashboard.layout')

@section('dashboard-content')

@include('rfq.partials.header', ['rfq' => $rfq])

@include('rfq.partials.tabs', ['rfq' => $rfq])

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white border rounded-xl p-6">

        <div class="flex justify-between">

            <div>
                <h1 class="text-lg font-semibold">
                    Offer #{{ $offer->id }}
                </h1>

                <p class="text-sm text-gray-500">
                    Supplier: {{ $offer->supplier->name ?? 'Unknown' }}
                </p>
            </div>

            <div class="text-right">
                <div class="text-lg font-semibold">
                    ${{ $offer->latestVersion->price ?? 0 }}
                </div>

                <div class="text-sm text-gray-500">
                    {{ $offer->latestVersion->delivery_days ?? '-' }} days
                </div>
            </div>

        </div>

    </div>

    {{-- VERSIONS --}}
    <div class="bg-white border rounded-xl p-6">

        <h2 class="font-semibold mb-4">Version History</h2>

        <div class="space-y-3">

            @foreach($offer->versions as $version)

                <div class="border rounded-lg p-3">

                    <div class="flex justify-between">

                        <div class="text-sm font-medium">
                            Version #{{ $version->id }}
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ $version->created_at }}
                        </div>

                    </div>

                    <div class="text-sm text-gray-600 mt-2">
                        Price: ${{ $version->price }} |
                        Delivery: {{ $version->delivery_days }} days
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        {{ $version->notes }}
                    </div>

                </div>

            @endforeach

        </div>

    </div>

    {{-- ACTIONS --}}
    <div class="flex gap-2">

        <form method="POST"
              action="{{ route('supplier.rfq.offers.reject', [$rfq->id, $offer->id]) }}">

            @csrf

            <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                Reject Offer
            </button>

        </form>

    </div>

</div>

@endsection