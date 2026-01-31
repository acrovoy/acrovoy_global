@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            
            <h2 class="text-2xl font-semibold text-gray-900">Available RFQs</h2>
            <p class="text-sm text-gray-500">
                Browse all active RFQs, view details, and submit offers.
            </p>
        </div>
    </div>

    @if($rfqs->isEmpty())
        <div class="text-gray-500 text-center py-10">
            No RFQs available at the moment.
        </div>
    @else
        {{-- RFQs Grid --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($rfqs as $rfq)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition">

                    {{-- Header: Title & Badge --}}
                    <div class="mb-3">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-lg flex items-center gap-2">
                                @if($rfq->offer_status_badge && $rfq->offer_status_badge === 'new')
                                    <span class="inline-flex items-center justify-center
                                                 px-2 py-0.5 text-xs font-bold
                                                 text-white bg-indigo-600 rounded-full">
                                        New Offers
                                    </span>
                                @endif
                                <a href="{{ route('buyer.rfqs.show', $rfq->id) }}" class="hover:underline text-indigo-600">
                                    {{ $rfq->title }}
                                </a>
                            </p>

                            {{-- Offer Status Badge --}}
                            @if($rfq->offer_status_badge && $rfq->offer_status_badge !== 'new')
                                <span class="inline-flex items-center justify-center
                                             px-2 py-0.5 text-xs font-bold rounded-full
                                             @if($rfq->offer_status_badge === 'accepted') bg-green-600 text-white
                                             @elseif($rfq->offer_status_badge === 'rejected') bg-red-600 text-white
                                             @else bg-gray-100 text-gray-700
                                             @endif">
                                    {{ ucfirst($rfq->offer_status_badge) }}
                                </span>
                            @endif
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ $rfq->description }}
                        </p>
                    </div>

                    {{-- Meta Info --}}
                    <div class="flex flex-col gap-1 text-xs text-gray-400 mb-3">
                        @if($rfq->category)
                            <span>Category: {{ $rfq->category->name }}</span>
                        @endif
                        @if($rfq->quantity)
                            <span>Quantity: {{ $rfq->quantity }}</span>
                        @endif
                        @if($rfq->deadline)
                            <span>Deadline: {{ \Carbon\Carbon::parse($rfq->deadline)->format('M d, Y H:i') }}</span>
                        @endif
                    </div>

                    {{-- Attachment --}}
                    @if($rfq->attachment_path)
                        @php
                            $ext = strtolower(pathinfo($rfq->attachment_path, PATHINFO_EXTENSION));
                            if(in_array($ext, ['jpg','jpeg','png'])){
                                $attachmentPreview = Storage::url($rfq->attachment_path);
                            } elseif($ext === 'pdf') {
                                $attachmentPreview = asset('images/pdf-icon.png');
                            } elseif(in_array($ext, ['dwg','dxf'])) {
                                $attachmentPreview = asset('images/dwg-icon.png');
                            } else {
                                $attachmentPreview = asset('images/file-placeholder.png');
                            }
                        @endphp
                        <p class="mt-2">
                            <a href="{{ Storage::url($rfq->attachment_path) }}" target="_blank"
                               class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                                <img src="{{ $attachmentPreview }}" class="w-5 h-5 object-contain">
                                Download attachment
                            </a>
                        </p>
                    @endif

                    {{-- Status & Actions --}}
                    <div class="flex justify-between items-center mt-4">
                        {{-- RFQ Status --}}
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            @if($rfq->status === 'active') bg-green-100 text-green-700
                            @elseif($rfq->status === 'closed') bg-gray-100 text-gray-600
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($rfq->status) }}
                        </span>

                        {{-- View / Make Offer --}}
                        <a href="{{ route('buyer.rfqs.show', $rfq->id) }}"
                           class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800 transition">
                            View / Make Offer
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
