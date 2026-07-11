@extends('dashboard.layout')

@section('dashboard-content')

@php
use App\Domain\RFQ\Enums\RfqStatus;
@endphp

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
           class="inline-flex items-center gap-2 mt-3 px-4 py-2
           text-sm font-medium text-gray-700
           bg-white border border-gray-200
           rounded-lg
           hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
           active:scale-[0.98]
           transition-all duration-150 shadow-sm">

           <span class="text-lg leading-none">+</span>
            Create RFQ
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
            
        </tr>
        </thead>

        {{-- BODY --}}
        <tbody class="divide-y divide-gray-100">

        @foreach($rfqs as $rfq)
            <tr class="hover:bg-gray-50 transition cursor-pointer"
                onclick="window.location='{{ route('rfqs.workspace', $rfq->id) }}'">

                {{-- ID --}}
                <td class="px-4 py-2 font-mono text-gray-800">
                    {{ $rfq->public_id }}
                     @if($rfq->customization)
                        <div class="uppercase text-[10px] text-amber-600">customization</div>
                        @endif
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
                <td class="px-4 py-2 text-gray-500 text-xs">
                    {{ $rfq->closed_at
                        ? \Carbon\Carbon::parse($rfq->closed_at)->format('M d, Y H:i')
                        : '-' }}
                </td>

                {{-- STATUS --}}
                <td class="px-4 py-2">

                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg {{ $rfq->status->badgeIndexClasses() }}">
    {{ $rfq->status->label() }}
</span>

                </td>

                

            </tr>
        @endforeach

        </tbody>

    </table>

</div>

@endif


{{-- CLOSED RFQs --}}
@if($closedRfqs->isNotEmpty())

<div class="mt-10">

    <h2 class="text-xl font-semibold text-gray-800">
        Closed RFQs
    </h2>
<p class="text-sm text-gray-500 mb-6">
    Archived procurement requests that have been completed or closed.
</p>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm border-collapse">

            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">ID</th>
                    <th class="px-4 py-2 text-left font-medium">Title</th>
                    <th class="px-4 py-2 text-left font-medium">Category</th>
                    <th class="px-4 py-2 text-left font-medium">Closed</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @foreach($closedRfqs as $rfq)

                <tr class="hover:bg-gray-50 transition cursor-pointer"
                    onclick="window.location='{{ route('rfqs.workspace', $rfq->id) }}'">

                    <td class="px-4 py-2 font-mono text-gray-800">
                        {{ $rfq->public_id }}
                        @if($rfq->customization)
                        <div class="uppercase text-[10px] text-amber-600">customization</div>
                        @endif
                    </td>

                    <td class="px-4 py-2">
                        

                        <div class="justify-between flex items-center">
                        <div>{{ $rfq->title }}</div>
                        <div>
                        @if($rfq->order_id)
                        @else
                        <div class="uppercase text-[10px] border border-green-200 bg-green-50 text-green-600 p-1 rounded-md hover:bg-green-100 transition cursor-pointer">create order</div>
                        @endif
                        </div>
                        </div>
                    </td>

                    <td class="px-4 py-2">
                        {{ $rfq->category->name ?? '-' }}
                    </td>

                    <td class="px-4 py-2 text-gray-500">
                        {{ $rfq->updated_at?->format('M d, Y') }}
                    </td>

                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded-lg bg-red-100 text-red-700">
                            Closed
                        </span>
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endif



@endsection