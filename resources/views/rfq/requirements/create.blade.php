

@extends('rfq.workspace')

@section('rfq-content')

<div class="bg-white border rounded-xl p-6">

    <h2 class="text-lg font-semibold mb-4">
        Add requirement
    </h2>

    <form method="POST"
          action="{{ route('buyer.rfqs.requirements.store', $rfq->id) }}">

        @csrf

        @include('rfq.requirements._form')

        <div class="mt-6 flex justify-end">
            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                Save
            </button>
        </div>

    </form>

</div>

@endsection