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
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 uppercase text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">RFQ</th>
                        <th class="px-5 py-3 text-left font-medium">RFQ Status</th>
                        <th class="px-5 py-3 text-left font-medium">Title</th>
                        <!-- <th class="px-5 py-3 text-left font-medium">Description</th> -->
                        <!-- <th class="px-5 py-3 text-left font-medium">Category</th> -->
                        <!-- <th class="px-5 py-3 text-left font-medium">Quantity</th> -->
                        <th class="px-5 py-3 text-left font-medium">Deadline</th>
                        <th class="px-5 py-3 text-left font-medium">Attachment</th>
                        <th class="px-5 py-3 text-left font-medium">Offer Status</th>
                        
                        <!-- <th class="px-5 py-3 text-right font-medium">Actions</th> -->
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rfqs as $rfq)
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
            onclick="window.location='{{ route('supplier.rfqs.show', $rfq->id) }}'">
                            <td class="px-5 py-3 text-gray-700 ">
                                {{ $rfq->id }}
                            </td>

                            {{-- RFQ Status --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($rfq->status === 'active') bg-green-100 text-green-700
                                    @elseif($rfq->status === 'closed') bg-gray-100 text-gray-600
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($rfq->status) }}
                                </span>
                            </td>


                            {{-- Title --}}
                            <td class="px-5 py-3 font-medium text-gray-900 w-full">
                                <div class="text-gray-600">
                                    {{ $rfq->title }}
</div>
                                <div class="text-xs text-gray-400">
                                  {{ $rfq->category?->name ?? '—' }}  
                                </div>
                            </td>

                            <!-- {{-- Description --}}
                            <td class="px-5 py-3 text-gray-700 line-clamp-2 max-w-xs">
                                {{ $rfq->description }}
                            </td> -->

                            <!-- {{-- Category --}}
                            <td class="px-5 py-3 text-gray-700">
                                {{ $rfq->category?->name ?? '—' }}
                            </td> -->

                            <!-- {{-- Quantity --}}
                            <td class="px-5 py-3 text-gray-700">
                                {{ $rfq->quantity ?? '—' }}
                            </td> -->

                            {{-- Deadline --}}
                            <td class="px-5 py-3 text-gray-700">
                                @if($rfq->deadline)
                                    {{ \Carbon\Carbon::parse($rfq->deadline)->format('M d, Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Attachment --}}
                            <td class="px-5 py-3 text-gray-700">
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
                                    <a href="{{ Storage::url($rfq->attachment_path) }}" target="_blank" class="flex items-center gap-2 hover:underline">
                                        <img src="{{ $attachmentPreview }}" class="w-5 h-5 object-contain">
                                        Download
                                    </a>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Offer Status --}}
                            <td class="px-5 py-3">
                                @if($rfq->offer_status_badge)
                                    <span class="inline-flex items-center justify-center
                                        px-2 py-0.5 text-xs font-bold rounded-full
                                        @if($rfq->offer_status_badge === 'accepted') bg-green-600 text-white
                                        @elseif($rfq->offer_status_badge === 'rejected') bg-red-600 text-white
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ ucfirst($rfq->offer_status_badge) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            

                            <!-- {{-- Actions --}}
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('supplier.rfqs.show', $rfq->id) }}"
                                   class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                                    View / Make Offer
                                </a>
                            </td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
