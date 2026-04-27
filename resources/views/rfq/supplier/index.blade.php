@extends('dashboard.layout')

@section('dashboard-content')

<div class="bg-white border rounded-xl p-6">

    <h1 class="text-lg font-semibold mb-6">
        Incoming RFQs
    </h1>

    @forelse($rfqs as $rfq)

        <a
            href="{{ route('supplier.rfq.show', $rfq) }}"
            class="block border rounded-lg p-4 mb-3 hover:bg-gray-50 transition"
        >

            <div class="flex justify-between items-center">

                <div>
                    <div class="font-medium text-gray-900">
                        {{ $rfq->title }}
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ ucfirst($rfq->status) }}
                    </div>
                </div>

                <div class="text-xs text-gray-400">
                    {{ $rfq->created_at->diffForHumans() }}
                </div>

            </div>

        </a>

    @empty

        <div class="text-gray-500 text-sm">
            No RFQs available
        </div>

    @endforelse

</div>

@endsection