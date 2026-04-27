@extends('dashboard.layout')

@section('dashboard-content')

<div class="bg-white border rounded-xl p-6">

    <h1 class="text-lg font-semibold mb-4">Edit RFQ</h1>

    <form method="POST" action="{{ route('buyer.rfqs.update', $rfq->id) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            <input type="text" name="title"
                   value="{{ $rfq->title }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">

            <textarea name="description"
                      class="w-full border rounded-lg px-3 py-2 text-sm">{{ $rfq->description }}</textarea>

        </div>

        <div class="mt-6 flex justify-end">
            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                Update
            </button>
        </div>

    </form>

</div>

@endsection