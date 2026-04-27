@extends('rfq.workspace')

@section('rfq-content')

<div class="bg-white border rounded-xl p-6">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-lg font-semibold">RFQ Participants</h1>

        <a href="{{ route('buyer.rfqs.participants.invite', $rfq->id) }}"
           class="px-3 py-2 bg-gray-900 text-white rounded-lg text-sm">
            + Invite Supplier
        </a>
    </div>

    <div class="space-y-2">

        @forelse($rfq->participants as $p)
            <div class="flex justify-between border-b py-2 text-sm">
                <span>Supplier #{{ $p->supplier_id }}</span>
                <span class="text-gray-500">{{ $p->status }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No participants yet</p>
        @endforelse

    </div>

</div>

@endsection