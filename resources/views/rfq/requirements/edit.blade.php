@extends('rfq.workspace')

@section('dashboard-content')

<div class="bg-white border rounded-xl p-6">

    <h1 class="text-lg font-semibold mb-4">Edit Requirement</h1>

    <form method="POST" action="{{ route('rfq.requirements.update', [$rfq->id, $requirement->id]) }}">
        @csrf
        @method('PUT')

        @include('rfq.requirements._form', ['requirement' => $requirement])

        <div class="mt-6 flex justify-end">
            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                Update
            </button>
        </div>

    </form>

</div>

@endsection