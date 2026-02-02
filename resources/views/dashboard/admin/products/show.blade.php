@extends('dashboard.admin.layout')

@section('dashboard-content')

<a href="{{ route('admin.products.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to list
</a>


{{-- Статус и действия --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3  pb-3">

    {{-- Левая часть: статус и действия --}}
    <div class="flex flex-col md:flex-row md:items-center gap-2">

        {{-- Статус --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Current Status:</span>
            <span class="text-sm font-semibold px-2 py-0.5
                @if($product1->status == 'pending') text-yellow-800
                @elseif($product1->status == 'approved') text-green-800
                @elseif($product1->status == 'rejected') text-red-800
                @endif
            ">
                {{ ucfirst($product1->status) ?? 'Pending' }}
            </span>
        </div>

        {{-- Approve --}}
        <form method="POST" action="{{ route('admin.products.approve', $product1->id) }}">
            @csrf
            <button type="submit"
                class="px-3 py-1 bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                Approve
            </button>
        </form>

        {{-- Reject с пояснением --}}
        <form method="POST" action="{{ route('admin.products.reject', $product1->id) }}" class="flex flex-row gap-1 items-center">
            @csrf
            <button type="submit"
    style="background-color:#b91c1c;"
    class="px-3 py-1 text-white text-sm font-medium hover:bg-red-600 transition">
    Reject
</button>
            <input type="text" name="reject_reason" placeholder="Reason"
                class="border border-gray-400 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                required>
          
        </form>
    </div>

    {{-- Правая часть: кнопка Back --}}
   @php
    use App\Models\Language;

    $language = optional(Language::where('code', $product1->supplier->user->language)->first());
@endphp

<div class="ml-auto text-sm">
    Language of supplier: {{ $language->name ?? 'N/A' }}
</div>

</div>




<section class="bg-[#F7F3EA] py-6">
    <div class="container mx-auto px-6">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6 flex flex-wrap gap-1">
            <a href="{{ route('admin.products.index') }}" class="hover:text-black">Products</a> /
            <a href="#" class="hover:text-black">
                {{ $product1->category->name ?? 'Category' }}
            </a> /
            <a href="{{ route('product.show', $product1->slug) }}">
            <span class="text-blue-500">{{ $product1->name }}</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

            {{-- Images --}}
            <div class="bg-white rounded-xl shadow p-4 mb-4">
                <img id="mainImage"
                     src="{{ $product1->image_url }}"
                     class="w-full h-auto object-contain rounded-lg cursor-pointer"
                     alt="Product Image">

                <div class="flex gap-4 mt-4">
                    @foreach($product1->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}"
                             class="thumbnail w-20 h-20 object-contain bg-gray-100 rounded cursor-pointer border border-gray-300 hover:border-blue-900"
                             data-src="{{ asset('storage/' . $img->image_path) }}">
                    @endforeach
                </div>
            </div>

            {{-- Info --}}
            <div class="rounded-xl shadow p-6">
                <div class="flex items-center mb-1">
                    <h1 class="text-3xl font-extrabold text-gray-900">
                        {{ $product1->name }}
                    </h1>
                    <span class="bg-yellow-900 text-white px-2 py-0 rounded text-sm ml-2 mr-6">
                        {{ $product1->id }}
                    </span>

                    @can('update', $product1)
                        <a href="{{ route('products.edit', $product1->id) }}"
                           class="inline-flex items-center gap-2
                                  px-4 py-2
                                  text-sm font-medium
                                  text-blue-700
                                  border border-blue-600
                                  rounded-lg
                                  hover:bg-blue-600 hover:text-white
                                  transition
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Edit
                        </a>
                    @endcan
                </div>

                <p class="text-gray-700 mb-2 leading-relaxed">{{ $product1->undername }}</p>

               



                {{-- Price / Quantity Table --}}
                @if($product1->priceTiers->count())
                    <div class="bg-white rounded-xl shadow p-6 mb-6">
                        <h3 class="font-semibold text-lg leading-none">Price per Quantity</h3>
                        <span class="text-xs text-gray-500 leading-none">Shipping cost not included</span>
                        <table class="w-full text-left text-gray-700 mt-2">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2">Quantity</th>
                                    <th class="py-2">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product1->priceTiers as $tier)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2">
                                            {{ $tier->min_qty }} - {{ $tier->max_qty ?? '∞' }} pcs
                                        </td>
                                        <td class="py-2 font-semibold text-blue-900 {{ $loop->first ? 'text-xl' : 'text-base' }}">
                                            ${{ number_format($tier->price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif






                {{-- Color / Material Options --}}
@php
    $colors = $product1->colors; // Получаем коллекцию цветов
@endphp
@if($colors->isNotEmpty())
<div class="mb-6">
    <h3 class="font-semibold text-lg mb-3">Available Colors / Materials</h3>

    <div class="flex flex-wrap gap-3">
        @foreach($product1->colors as $material)
            @php
                // Цвет или пусто
                $bgStyle = $material->color ? "background-color:{$material->color}" : '';
                
                // Текстура
                $textureUrl = $material->texture_path ? asset('storage/'.$material->texture_path) : '';

                // Ссылка на связанный продукт
                $link = $material->linked_product_id 
                        ? route('product.show', $material->linkedProduct->slug) 
                        : '#';

                // Заголовок
                $title = $material->color ?? 'Texture';
            @endphp

            <button
                class="color-option w-12 h-12 rounded-md border border-gray-300 shadow-sm
                       hover:border-black transition"
                style="{{ $bgStyle }} 
                       @if($textureUrl) background-image: url('{{ $textureUrl }}'); background-size: cover; background-position: center; @endif"
                data-link="{{ $link }}"
                title="{{ $title }}">
            </button>
        @endforeach
    </div>
</div>
@endif





                {{-- Description --}}
                @if(!empty($product1->description))
                    <p class="text-gray-700 mb-6 leading-relaxed">{{ $product1->description }}</p>
                @endif

                {{-- Specifications --}}
                @if($product1->specifications->count())
                    <div class="bg-white rounded-xl shadow p-6 mb-6">
                        <h3 class="font-semibold text-lg mb-2 leading-none">Specifications</h3>
                        <ul class="divide-y divide-gray-200 text-gray-700 mt-2">
                            @foreach($product1->specifications as $spec)
                                <li class="flex justify-between py-2">
                                    <span class="text-gray-600">{{ $spec->key }}</span>
                                    <span class="font-medium text-gray-900">{{ $spec->value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Materials --}}
                @if($product1->materials->isNotEmpty())
                    <div class="mb-8 flex items-start gap-3">
                        <span class="text-sm text-gray-500 pt-1 whitespace-nowrap">Materials used:</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product1->materials as $material)
                                <span class="inline-flex items-center
                                             px-3 py-1.5 rounded-md
                                             bg-gray-300/70
                                             text-gray-800 text-sm leading-none">
                                    {{ $material->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Commercial Terms --}}
                <div class="bg-[#F7F3EA] border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-gray-700">
                        <div>
                            <p class="text-gray-500">MOQ</p>
                            <p class="font-semibold text-gray-900">{{ $product1->moq ?? 'N/A' }} pcs</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Lead time</p>
                            <p class="font-semibold text-gray-900">{{ $product1->lead_time ?? 'N/A' }} days</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Customization</p>
                            <p class="font-semibold text-gray-900">{{ $product1->customization ? 'Available' : 'Not available' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- JS для галереи --}}
<script>
const mainImage = document.getElementById('mainImage');
const thumbnails = document.querySelectorAll('.thumbnail');
thumbnails.forEach(thumb => {
    thumb.addEventListener('click', () => mainImage.src = thumb.dataset.src);
});
</script>

{{-- JS для клика по кнопке цвета --}}
<script>
    document.querySelectorAll('.color-option').forEach(btn => {
        btn.addEventListener('click', () => {
            const link = btn.dataset.link;
            if(link && link !== '#') {
                window.open(link, '_blank'); // открываем связанный продукт в новой вкладке
            }
        });
    });
</script>


@endsection
