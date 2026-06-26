@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Available RFQs
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Browse all active RFQs, view details, and submit offers.
        </p>
    </div>

</div>

{{-- EMPTY --}}
@if($rfqs->isEmpty())

<div class="text-gray-500 text-center py-10">
    No RFQs available
</div>

@else

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">

    <table class="w-full text-sm border-collapse">

        {{-- HEADER --}}
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500">
                    RFQ
                </th>

                <th class="px-4 py-3 text-left font-medium text-gray-500">
                    Title
                </th>

                <th class="px-4 py-3 text-left font-medium text-gray-500">
                    Deadline
                </th>

                <th class="px-4 py-3 text-left font-medium text-gray-500">
                    RFQ Status
                </th>
            </tr>
        </thead>

        {{-- BODY --}}
        <tbody class="divide-y divide-gray-100">

            @foreach($rfqs as $rfq)

            <tr class="hover:bg-gray-50 transition cursor-pointer"
                onclick="window.location='{{ route('rfqs.workspace', $rfq->id) }}'">

                {{-- RFQ ID --}}
                <td class="px-4 py-4 font-mono text-gray-600 text-xs">
                    {{ $rfq->public_id }}
                </td>

                {{-- TITLE --}}
                <td class="px-4 py-4">

                    <div class="text-sm font-medium text-gray-900">
                        {{ $rfq->title }}
                    </div>

                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ $rfq->category->name ?? 'General RFQ' }}
                    </div>

                </td>

                {{-- DEADLINE --}}
                <td class="px-4 py-4 text-sm">

                    @if($rfq->closed_at)

                    @if($rfq->closed_at->isPast())

                    <div class="text-gray-400">
                        Closed
                    </div>

                    @elseif($rfq->closed_at->diffInDays() <= 2)

                        <div class="text-red-600 font-medium">
                        {{ $rfq->closed_at->format('d.m.Y') }}
</div>

<div class="text-xs text-red-500 mt-0.5">
    {{ $rfq->closed_at->diffForHumans(null, true) }} left
</div>

@else

<div class="text-gray-700">
    {{ $rfq->closed_at->format('d.m.Y') }}
</div>

<div class="text-xs text-gray-400 mt-0.5">
    {{ $rfq->closed_at->diffForHumans(null, true) }} left
</div>

@endif

@else

<span class="text-gray-400">—</span>

@endif

</td>

{{-- STATUS --}}
<td class="px-4 py-4">

    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-gray-100
    {{ $rfq->status->badgeClasses() }}">

        {{ $rfq->status->label() }}

    </span>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endif



    @php
    use App\Facades\ActiveContext;
    
@endphp

{{-- CLOSED RFQS --}}
@if($closedRfqs->isNotEmpty())

<div class="mt-10">

    <div class="mb-4">

        <h2 class="text-2xl font-semibold text-gray-800">
            Closed RFQs
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            RFQs that have already been closed.
        </p>

    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm border-collapse">

            <thead class="bg-gray-50 border-b">

                <tr>

                    <th class="px-4 py-3 text-left font-medium text-gray-500">
                        RFQ
                    </th>

                    <th class="px-4 py-3 text-left font-medium text-gray-500">
                        Title
                    </th>

                    <th class="px-4 py-3 text-left font-medium text-gray-500">
                        Closed At
                    </th>

                    <th class="px-4 py-3 text-left font-medium text-gray-500">
                        Status
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                @foreach($closedRfqs as $rfq)
@php
 $supplierId = ActiveContext::id();
        $supplierType = ActiveContext::type();
$offer = $rfq->offers
            ->where('participant_type', $supplierType)
            ->where('participant_id', $supplierId)
            ->sortByDesc('id')
            ->first();

        $status = $offer?->status;
@endphp
@php
    $rowClass = match($status) {
        'accepted' => 'bg-green-50 hover:bg-green-100',
        'rejected' => 'bg-red-50 hover:bg-red-100',
        default => 'hover:bg-gray-50'
    };

    $badgeClass = match($status) {
        'accepted' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-600'
    };

    $badgeText = match($status) {
        'accepted' => 'ACCEPTED',
        'rejected' => 'REJECTED',
        default => 'CLOSED'
    };

    
@endphp

                <tr class="{{ $rowClass }} transition cursor-pointer"
    onclick="window.location='{{ route('rfqs.workspace', $rfq->id) }}'">

                    {{-- RFQ ID --}}
                    <td class="px-4 py-4 font-mono text-gray-600 text-xs">
                        {{ $rfq->public_id }}
                    </td>

                    {{-- TITLE --}}
                    <td class="px-4 py-4">

                        <div class="text-sm font-medium text-gray-900">
                            {{ $rfq->title }}
                        </div>

                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $rfq->category->name ?? 'General RFQ' }}
                        </div>

                    </td>

                    {{-- CLOSED DATE --}}
                    <td class="px-4 py-4 text-sm text-gray-500">

                        @if($rfq->updated_at)
                            {{ $rfq->updated_at->format('d.m.Y') }}
                        @else
                            —
                        @endif

                    </td>

                    {{-- STATUS --}}
                    <td class="px-4 py-4">

                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $badgeClass }}">
        {{ $badgeText }}
        
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