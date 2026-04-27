@extends('dashboard.layout')

@section('dashboard-content')

@include('rfq.partials.header', ['rfq' => $rfq])

@include('rfq.partials.tabs', ['rfq' => $rfq])

<div class="grid grid-cols-3 gap-6">

    {{-- LEFT: VERSION HISTORY --}}
    <div class="col-span-1 bg-white border rounded-xl p-4">

        <h2 class="font-semibold mb-3">Versions</h2>

        <div class="space-y-2">

            @foreach($offer->versions as $version)

                <a href="{{ route('supplier.rfq.offers.edit', [$rfq->id, $offer->id, 'version' => $version->id]) }}"
                   class="block p-2 rounded border text-sm
                          {{ $currentVersion->id === $version->id ? 'bg-gray-100' : '' }}">

                    Version #{{ $version->id }}
                    <div class="text-xs text-gray-500">
                        {{ $version->created_at }}
                    </div>

                </a>

            @endforeach

        </div>

    </div>

    {{-- RIGHT: EDIT FORM --}}
    <div class="col-span-2 bg-white border rounded-xl p-6">

        <h2 class="font-semibold mb-4">
            Edit Offer (Version #{{ $currentVersion->id }})
        </h2>

        <form method="POST"
              action="{{ route('supplier.rfq.offers.update', [$rfq->id, $offer->id, $currentVersion->id]) }}"
              class="space-y-4">

            @csrf
            @method('PUT')

            <div>
                <label class="text-sm text-gray-600">Price</label>
                <input type="number"
                       name="price"
                       value="{{ $currentVersion->price }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-gray-600">Delivery Days</label>
                <input type="number"
                       name="delivery_days"
                       value="{{ $currentVersion->delivery_days }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-gray-600">Notes</label>
                <textarea name="notes"
                          class="w-full border rounded-lg px-3 py-2 text-sm"
                          rows="4">{{ $currentVersion->notes }}</textarea>
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                    Save New Version
                </button>
            </div>

        </form>

    </div>

</div>

@endsection