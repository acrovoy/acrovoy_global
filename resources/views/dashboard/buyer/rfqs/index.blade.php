@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            
            <h1 class="text-2xl font-semibold mt-1 text-gray-800">Available RFQs</h1>
            <p class="text-sm text-gray-500">
                Browse all active RFQs, view details, and submit offers.
            </p>
        </div>

        <a href="{{ route('buyer.rfqs.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
            + Create RFQ
        </a>
    </div>

</div>

@if($rfqs->isEmpty())
    <div class="text-gray-500 text-center py-10">
        No RFQs available at the moment.
    </div>
@else
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">ID</th>
                    <th class="px-4 py-2 text-left font-medium">Title</th>
                    <th class="px-4 py-2 text-left font-medium">Category</th>
                    <th class="px-4 py-2 text-left font-medium">Quantity</th>
                    <th class="px-4 py-2 text-left font-medium">Deadline</th>
                    <th class="px-4 py-2 text-left font-medium">Attachment</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                    <th class="px-4 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach($rfqs as $rfq)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 font-mono text-gray-800">{{ $rfq->id }}</td>
                        <td class="px-4 py-2 text-gray-800">
                            <a href="{{ route('buyer.rfqs.show', $rfq->id) }}" class="text-indigo-600 hover:underline">
                                {{ $rfq->title }}
                            </a>
                        </td>
                        <td class="px-4 py-2 text-gray-800">{{ $rfq->category->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $rfq->quantity ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">
                            {{ $rfq->deadline ? \Carbon\Carbon::parse($rfq->deadline)->format('M d, Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-2">
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
                                <a href="{{ Storage::url($rfq->attachment_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 text-sm">
                                    <img src="{{ $attachmentPreview }}" class="w-5 h-5 object-contain">
                                    Download
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                @if($rfq->status === 'active') bg-green-100 text-green-700
                                @elseif($rfq->status === 'closed') bg-gray-100 text-gray-600
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($rfq->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('buyer.rfqs.show', $rfq->id) }}" class="text-blue-600 hover:underline text-sm">View / Make Offer</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
