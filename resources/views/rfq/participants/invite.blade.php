@extends('dashboard.layout')

@section('dashboard-content')

<div class="bg-white border rounded-xl p-6">

    <h1 class="text-lg font-semibold mb-4">Invite Supplier</h1>

    <form method="POST" action="{{ route('participants.store', $rfq->id) }}">
        @csrf

        <div>
            <label class="text-sm text-gray-600">Supplier ID</label>
            <input
                type="number"
                name="supplier_id"
                class="w-full border rounded-lg px-3 py-2 text-sm"
                placeholder="Enter supplier ID"
            >
        </div>

        <div class="mt-6 flex justify-end">
            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                Send Invite
            </button>
        </div>

    </form>

</div>

@endsection