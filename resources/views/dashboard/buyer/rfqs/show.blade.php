@extends('dashboard.layout')

@section('dashboard-content')

    <a href="{{ route('buyer.rfqs.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 mb-2 flex items-center gap-1">
            ← Back to RFQs
        </a>
    <h2 class="text-2xl font-bold">RFQ Details</h2>
<p class="text-sm text-gray-500 mb-4">
                View all your product items.
            </p>
    <div class="bg-white border rounded-lg p-6 space-y-4">

        {{-- Заголовок --}}
        <h3 class="text-xl font-semibold text-gray-800">{{ $rfq->title }}</h3>


        @if($rfq->offers->count() === 0)
            <a href="{{ route('buyer.rfqs.edit', $rfq->id) }}"
            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm">
                Edit RFQ
            </a>
        @endif

        {{-- Статус --}}
        <p class="text-sm font-medium
            @if($rfq->status === 'active') text-green-600
            @elseif($rfq->status === 'closed') text-gray-500
            @else text-yellow-600
            @endif">
            Status: {{ ucfirst($rfq->status) }}
        </p>

        {{-- Основная информация --}}
        <div class="text-gray-700 space-y-1 text-sm">
            @if($rfq->category)
                <p>Category: {{ $rfq->category->name }}</p>
            @endif
            @if($rfq->quantity)
                <p>Quantity: {{ $rfq->quantity }}</p>
            @endif
            @if($rfq->deadline)
                <p>Deadline for offers: {{ \Carbon\Carbon::parse($rfq->deadline)->format('M d, Y H:i') }}</p>
            @endif
        </div>

        {{-- Описание --}}
        <div class="text-gray-700 mt-2">
            <p>{{ $rfq->description }}</p>
        </div>

        {{-- Файл --}}
        @if($rfq->attachment_path)
            <div class="mt-2">
                <a href="{{ Storage::url($rfq->attachment_path) }}" target="_blank"
                   class="inline-flex items-center px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Download attachment
                </a>
            </div>
        @endif
    </div>

    {{-- Offers --}}
    <h3 class="text-xl font-bold mt-6 mb-4">Offers from Manufacturers</h3>

    @if($rfq->offers->isEmpty())
    <p class="text-gray-500">No offers yet.</p>
    @else
        <div class="space-y-4">
            @foreach($rfq->offers as $offer)
                <div class="border rounded-lg p-4 flex justify-between items-start bg-white shadow-sm">
                    <div class="flex-1">
                        <p class="font-semibold text-lg">Price: ${{ number_format($offer->price, 2) }}</p>

                    

                        @if($offer->comment)
                            <p class="text-gray-600 text-sm mt-1">Comment: {{ $offer->comment }}</p>
                        @endif

        
                        @if($offer->delivery_days)
                            <p class="text-gray-600 text-sm">Lead time: {{ $offer->delivery_days }} day(s)</p>
                        @endif


                    @if($offer->shipping_template)
                        <div class="mb-4 mt-2 max-w-sm">
                            
                            <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm flex flex-col gap-2">
                                <h4 class="font-semibold text-gray-900">
                                    {{ $offer->shipping_template->translations->firstWhere('locale', app()->getLocale())->title ?? 'Template #' . $offer->shipping_template_id }}
                                </h4>

                                @if($offer->shipping_template->translations->firstWhere('locale', app()->getLocale())->description)
                                    <p class="text-gray-700 text-sm mt-1">
                                        {{ $offer->shipping_template->translations->firstWhere('locale', app()->getLocale())->description }}
                                    </p>
                                @endif

                                <div class="mt-2 text-gray-700 text-sm grid grid-cols-2 gap-2">
                                    @if($offer->shipping_template->price)
                                        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg">
                                            <span class="text-sm text-blue-900 font-medium">Price:</span>
                                            <span class="text-base font-semibold text-blue-900">
                                                ${{ number_format($offer->shipping_template->price, 2) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($offer->shipping_template->delivery_time)
                                        <div>
                                            <div class="font-medium">Delivery Time:</div>
                                            <div>{{ $offer->shipping_template->delivery_time }} days</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif




                        <p class="text-gray-400 text-xs mt-1">
                            Submitted: {{ $offer->created_at->format('M d, Y H:i') }}
                        </p>

                        <p class="text-sm font-medium mt-1
                            @if($offer->status === 'accepted') text-green-600
                            @elseif($offer->status === 'rejected') text-red-600
                            @else text-yellow-600
                            @endif">
                            Status: {{ ucfirst($offer->status) }}
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2 ml-4">
                        @if($rfq->status === 'active' && $offer->status === 'pending')
                            <form action="{{ route('buyer.rfqs.accept', ['rfq' => $rfq->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                <button type="submit"
    class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 text-center leading-tight">
    Accept Offer
    <span class="block sm:inline">& Create Order</span>
</button>
                            </form>
                        @endif

                        {{-- Supplier profile --}}
                        
                        
                       <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 mt-3 p-3 rounded-lg bg-gray-50 border">
                            <!-- Название компании -->
                            <a href="{{ route('supplier.show', $offer->supplier->slug) }}"
                            target="_blank"
                            class="text-sm font-semibold text-gray-900 hover:text-indigo-700">
                                {{ $offer->supplier->name }}
                            </a>

                            <!-- Репутация -->
                            <span class="text-xs text-gray-500 sm:ml-auto text-left sm:text-right">
                                Trusted Supplier Репутация
                            </span>
                        </div>


                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6">
        
    </div>
@endsection
