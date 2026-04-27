@extends('rfq.workspace')

@section('rfq-content')

<div class="bg-white border rounded-xl p-4">

    <div class="font-semibold mb-4">Offers</div>

    @foreach($offers as $offer)

        <div class="border rounded-lg p-3 mb-3">

            <div class="flex justify-between">
                <div class="font-medium">
                    {{ $offer->supplier->name }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $offer->total_price }} €
                </div>
            </div>

        </div>

    @endforeach

</div>

@endsection