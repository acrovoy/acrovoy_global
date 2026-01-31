@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Заголовок --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Supplier Details</h1>
        <a href="{{ route('admin.sellers.index') }}" class="text-gray-500 hover:underline">← Back to list</a>
    </div>
   

    {{-- Основная информация о продавце --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Basic Information</h2> 
        <a href="{{ route('supplier.show', $seller->slug) }}" 
           target="_blank"
           class="px-1 py-0 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
           View Public Card
        </a>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>ID:</strong> {{ $seller->id }}</div>
            <div><strong>Name:</strong> {{ $seller->name }}</div>
            <div><strong>Email:</strong> {{ $seller->email ?? '—' }}</div>
            <div><strong>Phone:</strong> {{ $seller->phone ?? '—' }}</div>
            <div><strong>Status:</strong> 
                <span class="px-2 py-1 rounded text-xs
                    @if($seller->status === 'active') bg-green-100 text-green-700
                    @elseif($seller->status === 'pending') bg-yellow-100 text-yellow-700
                    @else bg-gray-200 text-gray-600 @endif">
                    {{ ucfirst($seller->status) }}
                </span>
            </div>
            <div><strong>Country:</strong> {{ $seller->country?->name ?? '—' }}</div>
            <div><strong>Created At:</strong> {{ $seller->created_at->format('Y-m-d H:i') }}</div>
            <div><strong>Updated At:</strong> {{ $seller->updated_at->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    {{-- Адрес и описание --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Address & Description</h2>
        <div class="grid grid-cols-1 gap-2 text-sm">
            <div><strong>Address:</strong> {{ $seller->address ?? '—' }}</div>
            <div><strong>Short Description:</strong> {{ $seller->short_description ?? '—' }}</div>
            <div><strong>Description:</strong> {{ $seller->description ?? '—' }}</div>
        </div>
    </div>

    {{-- Логотип и изображения --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Media</h2>
        <div class="flex items-center gap-4">
            <div>
                <strong>Logo:</strong>
                @if($seller->logo)
                    <img src="{{ asset('storage/' . $seller->logo) }}" alt="Logo" class="w-24 h-24 object-cover rounded border">
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </div>
            <div>
                <strong>Catalog Image:</strong>
                @if($seller->catalog_image)
                    <img src="{{ asset('storage/' . $seller->catalog_image) }}" alt="Catalog" class="w-24 h-24 object-cover rounded border">
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </div>
        </div>
    </div>



    {{-- Репутация --}}
<div class="bg-white shadow rounded p-4 mt-4">
    <h2 class="text-lg font-semibold mb-2">Reputation</h2>
    
    {{-- Средний рейтинг --}}
    <div class="flex items-center mb-3">
        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-medium">Score: {{ number_format($reputation, 1) }}</span>
        <div class="ml-3 flex items-center">
            @for ($i = 1; $i <= 5; $i++)
                @if($i <= round($reputation))
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><polygon points="10 1.5 12.59 7.2 18.9 7.6 13.95 11.6 15.45 17.5 10 14 4.55 17.5 6.05 11.6 1.1 7.6 7.41 7.2 10 1.5"/></svg>
                @else
                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><polygon points="10 1.5 12.59 7.2 18.9 7.6 13.95 11.6 15.45 17.5 10 14 4.55 17.5 6.05 11.6 1.1 7.6 7.41 7.2 10 1.5"/></svg>
                @endif
            @endfor
            {{-- Количество отзывов справа от звездочек --}}
    <span class="text-sm text-gray-600">({{ $reviewsCount }} reviews)</span>
        </div>
    </div>

    {{-- Последние отзывы --}}
    <div class="max-h-48 overflow-y-auto space-y-3">
        @forelse($reviews as $review)
            <div class="border p-2 rounded">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-medium text-gray-700">{{ $review->user->name ?? 'Anonymous' }} {{ $review->user->last_name ?? '' }}</span>
                    <span class="text-sm text-gray-500">{{ $review->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="flex items-center mb-1">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                            <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20"><polygon points="10 1.5 12.59 7.2 18.9 7.6 13.95 11.6 15.45 17.5 10 14 4.55 17.5 6.05 11.6 1.1 7.6 7.41 7.2 10 1.5"/></svg>
                        @else
                            <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20"><polygon points="10 1.5 12.59 7.2 18.9 7.6 13.95 11.6 15.45 17.5 10 14 4.55 17.5 6.05 11.6 1.1 7.6 7.41 7.2 10 1.5"/></svg>
                        @endif
                    @endfor
                </div>
                <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
            </div>
        @empty
            <p class="text-gray-400 text-sm">No reviews yet</p>
        @endforelse
    </div>
</div>


    {{-- Сертификаты --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Certificates</h2>
        @if($seller->certificates->count())
            <div class="flex flex-wrap gap-2">
                @foreach($seller->certificates as $certificate)
                    <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank"
                       class="px-2 py-1 border rounded text-blue-600 text-sm hover:underline truncate max-w-[150px]">
                        {{ $certificate->name }}
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">No certificates uploaded</p>
        @endif
    </div>

    {{-- Список товаров с прокруткой --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Products</h2>
        @if($seller->products->count())
            <div class="overflow-x-auto border rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-left">Price</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seller->products as $product)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $product->id }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $product->category?->name ?? '—' }}</td>
                                <td class="px-4 py-2">
    @php
        $avgRating = $product->reviews()->avg('rating') ?? 0;
        $reviewsCount = $product->reviews()->count();
    @endphp
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
            <polygon points="10 1.5 12.59 7.2 18.9 7.6 13.95 11.6 15.45 17.5 10 14 4.55 17.5 6.05 11.6 1.1 7.6 7.41 7.2 10 1.5"/>
        </svg>
        <span>{{ number_format($avgRating, 1) }} ({{ $reviewsCount }})</span>
    </span>
</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-xs
                                        @if($product->status === 'active') bg-green-100 text-green-700
                                        @elseif($product->status === 'pending') bg-yellow-100 text-yellow-700
                                        @else bg-gray-200 text-gray-600 @endif">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $product->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400">No products uploaded</p>
        @endif
    </div>

</div>
@endsection
