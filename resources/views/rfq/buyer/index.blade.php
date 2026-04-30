@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold mt-1 text-gray-800">
                My RFQs
            </h1>
            <p class="text-sm text-gray-500">
                Manage procurement requests, track offers and negotiations.
            </p>
        </div>

        <a href="{{ route('buyer.rfqs.create') }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
            + Create RFQ
        </a>
    </div>

</div>

{{-- EMPTY STATE --}}
@if($rfqs->isEmpty())
    <div class="text-gray-500 text-center py-10">
        No RFQs created yet.
    </div>
@else

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">

    <table class="w-full text-sm border-collapse">

        {{-- HEADER --}}
        <thead class="bg-gray-50 border-b">
        <tr>
            <th class="px-4 py-2 text-left font-medium">ID</th>
            <th class="px-4 py-2 text-left font-medium">Title</th>
            <th class="px-4 py-2 text-left font-medium">Category</th>
            
            <th class="px-4 py-2 text-left font-medium">Deadline</th>
            <th class="px-4 py-2 text-left font-medium">Status</th>
            <th class="px-4 py-2 text-right font-medium">Actions</th>
        </tr>
        </thead>

        {{-- BODY --}}
        <tbody class="divide-y divide-gray-100">

        @foreach($rfqs as $rfq)
            <tr class="hover:bg-gray-50 transition">

                {{-- ID --}}
                <td class="px-4 py-2 font-mono text-gray-800">
                    {{ $rfq->public_id }}
                </td>

                {{-- TITLE --}}
                <td class="px-4 py-2 text-gray-800">
                    
                        {{ $rfq->title }}
                    
                </td>

                {{-- CATEGORY --}}
                <td class="px-4 py-2 text-gray-700">
                    {{ $rfq->category->name ?? '-' }}
                </td>

               

                {{-- DEADLINE --}}
                <td class="px-4 py-2 text-gray-700">
                    {{ $rfq->deadline
                        ? \Carbon\Carbon::parse($rfq->deadline)->format('M d, Y H:i')
                        : '-' }}
                </td>

                {{-- STATUS --}}
                <td class="px-4 py-2">

                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                        @if($rfq->status === 'draft')
                            bg-gray-100 text-gray-600
                        @elseif($rfq->status === 'published')
                            bg-blue-100 text-blue-700
                        @elseif($rfq->status === 'in_negotiation')
                            bg-yellow-100 text-yellow-800
                        @elseif($rfq->status === 'closed')
                            bg-green-100 text-green-700
                        @else
                            bg-gray-100 text-gray-600
                        @endif
                    ">
                        {{ $rfq->status->label() }}
                    </span>

                </td>

                {{-- ACTIONS --}}
                <td class="px-4 py-2 text-right space-x-2">

                    <a href="{{ route('rfqs.workspace', $rfq->id) }}"
                       class="text-blue-600 hover:underline text-sm">
                        Open Workspace
                    </a>

                </td>

            </tr>
        @endforeach

        </tbody>

    </table>

</div>

@endif

@endsection