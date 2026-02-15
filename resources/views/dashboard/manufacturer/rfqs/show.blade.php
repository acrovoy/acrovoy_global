@extends('dashboard.layout')

@section('dashboard-content')
<div class="max-w-5xl mx-auto py-8 flex flex-col gap-6">

    {{-- RFQ Header --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ $rfq->title }}</h1>

        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4">
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

        <p class="text-gray-700 mb-4 line-clamp-4">{!! nl2br(e($rfq->description)) !!}</p>

        @if($rfq->attachment_path)
            @php
                $ext = strtolower(pathinfo($rfq->attachment_path, PATHINFO_EXTENSION));
                if(in_array($ext, ['jpg','jpeg','png'])){
                    $attachmentPreview = Storage::url($rfq->attachment_path);
                } elseif($ext === 'pdf'){
                    $attachmentPreview = asset('images/pdf-icon.png');
                } elseif(in_array($ext, ['dwg','dxf'])){
                    $attachmentPreview = asset('images/dwg-icon.png');
                } else {
                    $attachmentPreview = asset('images/file-placeholder.png');
                }
            @endphp
            <a href="{{ Storage::url($rfq->attachment_path) }}" target="_blank"
               class="flex items-center gap-2 text-sm text-blue-600 hover:underline mb-2">
                <img src="{{ $attachmentPreview }}" class="w-5 h-5 object-contain">
                Download attachment
            </a>
        @endif
    </div>

    {{-- Make Offer Form --}}
    @if($rfq->status === 'active' && !$rfq->offers->where('supplier_id', auth()->user()->supplier->id)->count())
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Make an Offer</h2>

            <form action="{{ route('manufacturer.rfqs.offer.store', $rfq->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (USD)</label>
                    <input type="number" step="0.01" name="price" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Template</label>
                    <select name="shipping_template_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                        <option value="">-- Select a template --</option>
                        @foreach($shippingTemplates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->translations->firstWhere('locale', app()->getLocale())->title ?? $template->id }}
                            </option>
                        @endforeach
                        @foreach($defaultShippingTemplate as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->translations->firstWhere('locale', app()->getLocale())->title ?? $template->id }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Optional: select a shipping template for this offer.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lead Time (days)</label>
                    <input type="number" name="delivery_days" min="1"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="comment" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                        placeholder="Optional message to the buyer"></textarea>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Submit Offer
                </button>
            </form>
        </div>
    @endif

    {{-- Your Offer --}}
    @php
        $myOffer = $rfq->offers->where('supplier_id', auth()->user()->supplier->id)->first();
    @endphp
    @if($myOffer)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Offer</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">

                {{-- Left info --}}
                <div class="flex flex-col gap-2">
                    <p class="font-semibold text-gray-900 text-lg">Price: ${{ number_format($myOffer->price, 2) }}</p>

                    @if($myOffer->delivery_days)
                        <p class="text-gray-700 text-sm">Delivery time: {{ $myOffer->delivery_days }} day(s)</p>
                    @endif

                    @if($myOffer->comment)
                        <p class="text-gray-700 text-sm">Comment: {{ $myOffer->comment }}</p>
                    @endif

                    @if($myOffer->shipping_template)
                        <p class="text-gray-700 text-sm">
                            Delivery: {{ $myOffer->shipping_template->translations->firstWhere('locale', app()->getLocale())->title ?? $myOffer->shipping_template->id }}
                        </p>
                    @endif

                    <p class="text-gray-400 text-xs mt-1">Submitted: {{ $myOffer->created_at->format('M d, Y H:i') }}</p>

                    <p class="text-sm font-medium
                        @if($myOffer->status === 'accepted') text-green-600
                        @elseif($myOffer->status === 'rejected') text-red-600
                        @else text-yellow-600
                        @endif">
                        Status: {{ ucfirst($myOffer->status) }}
                    </p>
                </div>

                {{-- Right button --}}
                <div class="flex justify-end items-start">
                    @if($myOffer->status === 'accepted' && $myOffer->order)
                        <a href="{{ route('manufacturer.orders.show', $myOffer->order->id) }}"
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition whitespace-nowrap">
                            Open Order
                        </a>
                    @endif
                </div>

            </div>
        </div>
    @else
        <div class="text-gray-500 text-sm text-center">
            You have not submitted an offer yet.
        </div>
    @endif

</div>
@endsection
