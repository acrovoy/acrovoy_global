@extends('rfq.workspace')

@section('rfq-content')

<div class="bg-white border rounded-xl p-4">

    <div class="font-semibold mb-4">Activity Timeline</div>

    <div class="space-y-3">

        @foreach($events as $event)

            <div class="text-sm border-b pb-2">

                <div class="text-gray-800 font-medium">
                    {{ $event->type }}
                </div>

                <div class="text-gray-500 text-xs">
                    {{ $event->created_at }}
                </div>

            </div>

        @endforeach

    </div>

</div>

@endsection