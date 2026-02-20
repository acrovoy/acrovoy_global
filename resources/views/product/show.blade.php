@extends('layouts.app')

@section('content')

<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6 flex flex-wrap gap-1">
            <a href="{{ route('catalog.index') }}" class="hover:text-black">{{ __('product/product_show.root') }}</a> /
            <a href="{{ route('catalog.index', $product1->category->slug) }}" class="hover:text-black">
                {{ $product1->category->name ?? 'Category' }}
            </a> /
            <span class="text-gray-900">{{ $product1->name }}</span>
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
            <div class="rounded-xl shadow p-6" x-data="{ showProjectBox: false }">
                <div class="flex items-start mb-1">

        {{-- Title --}}
        <div>
        <div class="flex items-center flex-wrap gap-3">
            <h1 class="text-3xl font-extrabold text-gray-900">
                {{ $product1->name }}
            </h1>

            <span class="bg-yellow-900 text-white px-2 py-0 rounded text-sm">
                {{ $product1->id }}
            </span>



        </div>

<p class="text-gray-700 mb-2 leading-relaxed">{{ $product1->undername }}</p>

                {{-- ⭐ Рейтинг и продано --}}
                @php
                $reviewsCount = $product1->reviews->count();
                $rating = $reviewsCount > 0 ? round($product1->reviews->avg('rating'), 1) : 0;
                $soldCount = $product1->orders->where('status', 'completed')->sum('quantity');
                @endphp

                <div class="flex items-center text-gray-600 text-xs mb-4">
                    {{-- Звёзды --}}
                    <div class="flex items-center gap-1 mr-3">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <=floor($rating))
                            <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                            </svg>
                            @elseif ($i - $rating < 1)
                                <svg class="w-4 h-4 fill-current text-yellow-300" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                </svg>
                                @else
                                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                </svg>
                                @endif
                                @endfor
                                <span>{{ number_format($rating, 1) }}</span>
                    </div>

                    {{-- Количество отзывов --}}
                    <span>({{ $reviewsCount }} {{ __('product/product_show.reviews') }})</span>

                    @if($soldCount > 0)
                    <span class="mx-2">•</span>
                    <span>{{ __('product/product_show.sold') }}: {{ $soldCount }}</span>
                    @endif
                </div>







</div>

        





       {{-- Actions --}}
<div class="ml-auto flex items-center gap-2">

    {{-- ➕ Add to project --}}
    <div class="inline-flex flex-col items-end w-[180px]">
    <!-- Button -->
    <button
        @click="showProjectBox = !showProjectBox"
        title="Add to project"
        class="inline-flex items-center gap-2
               px-4 py-2
               rounded-lg
               bg-gray-500 text-white
               text-sm font-semibold
               shadow-sm
               hover:bg-gray-700 hover:shadow
               transition-colors duration-200">
        <!-- Icon -->
        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-700/50 transition">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </span>

        <!-- Text -->
        <span class="whitespace-nowrap">
            Add to project
        </span>
    </button>

    <!-- Text below button, same width -->
    <p class="mt-1 text-xs text-gray-500 text-right inline-block">
        Organize your products into projects — create a project in your dashboard first.
    </p>
</div>





    {{-- Edit --}}
    @can('update', $product1)
        <a href="{{ route('products.edit', $product1->id) }}"
           class="inline-flex items-center gap-2
                  px-4 py-2
                  text-sm font-medium
                  text-blue-700
                  border border-blue-600
                  rounded-lg
                  hover:bg-blue-600 hover:text-white
                  transition">
            Edit
        </a>
    @endcan

</div>
    </div>




                




@include('product.partials.notification')

  
@include('product.partials.add-to-project', ['product1' => $product1, 'projects' => $projects])

@include('product.partials.price-table', ['product1' => $product1])


                


                
                {{-- Color / Material Options --}}
                @php
                $colors = $product1->colors; // Получаем коллекцию цветов
                @endphp
                @if($colors->isNotEmpty())
                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3">{{ __('product/product_show.available_colors') }}</h3>

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
                    <h3 class="font-semibold text-lg mb-2 leading-none">{{ __('product/product_show.specification') }}</h3>
                    <p class="text-sm text-gray-500 leading-tight">
                        {{ __('product/product_show.shipping_templates_selected_text') }}
                    </p>

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

@include('product.partials.materials-table', ['product1' => $product1])

               
                
                {{-- Commercial Terms --}}
<div class="bg-[#F7F3EA] border border-gray-200 rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-gray-700">
        <div>
            <p class="text-gray-500">{{ __('product/product_show.MOQ') }}</p>
            <p class="font-semibold text-gray-900">
                {{ $product1->moq ?? 'N/A' }} {{ __('product/product_show.pcs') }}
            </p>
        </div>
        <div>
            <p class="text-gray-500">{{ __('product/product_show.lead_time') }}</p>
            <p class="font-semibold text-gray-900">
                {{ $product1->lead_time ?? 'N/A' }} {{ __('product/product_show.days') }}
            </p>
        </div>
        <div>
            <p class="text-gray-500">{{ __('product/product_show.customization') }}</p>
            <p class="font-semibold text-gray-900">
                {{ $product1->customization ? 'Available' : 'Not available' }}
            </p>
        </div>
    </div>
</div>

{{-- Заказать кастомизацию --}}
@if($product1->customization)

{{-- Customization order panel --}}
<div class="mt-6 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-6">

    <h4 class="text-base font-semibold text-gray-900 mb-1">
        Request product customization
    </h4>

    <p class="text-sm text-gray-500 mb-4">
        Create a dedicated project for customized production of this product.
    </p>

    {{-- Instruction --}}
    <div class="mb-4 rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-700">
        <p class="font-medium mb-1">How it works:</p>
        <ul class="list-disc list-inside space-y-1 text-gray-600">
            <li>A new project will be created automatically</li>
            <li>All product data will be copied into the project</li>
            <li>You can edit specifications and send RFQ back to supplier</li>
        </ul>
    </div>

    @auth
        <form action="{{ route('buyer.custom-orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product1->id }}">

            <button
                type="submit"
                class="w-full bg-indigo-300 hover:bg-indigo-400 text-white py-3 rounded-lg
                       text-sm font-semibold tracking-wide transition shadow-md">
                Order the customization of this product
            </button>
        </form>
    @endauth

    @guest
        <div class="text-center py-4">
            <p class="text-sm text-gray-500 mb-2">
                Only registered users can request product customization.
            </p>
            <button
                disabled
                class="w-full bg-gray-400 text-white py-3 rounded-lg
                       text-sm font-semibold cursor-not-allowed">
                Order the customization of this product
            </button>
        </div>
    @endguest

</div>

@endif





@include('product.partials.shippingtemplates-table', ['product1' => $product1])
                

                

                


                {{-- CTA Panel --}}
                <div class="mt-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-lg mb-6">
                    <button
                        class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4 rounded-xl
                               text-lg font-semibold tracking-wide shadow-md transition-all transform hover:scale-105 mb-4">
                        {{ __('product/product_show.checkout') }}
                    </button>

                    <div class="grid grid-cols-2 gap-4">
                        <button id="contactSupplierBtn"
                            class="w-full border border-gray-300 py-3 rounded-xl
                                   text-gray-800 font-medium shadow-sm
                                   hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105">
                            {{ __('product/product_show.contact_supllire') }}
                        </button>





                        <form method="POST" action="{{ route('buyer.cart.add', $product1->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full border border-gray-300 py-3 rounded-xl
                                        text-gray-800 font-medium shadow-sm
                                        hover:border-black hover:text-black hover:shadow-md
                                        transition-all transform hover:scale-105">
                                                            {{ __('product/product_show.add_to_cart') }}
                            </button>
                        </form>




                    </div>


               




                    




                </div>

                <p class="text-gray-700 mb-2 leading-relaxed">{{ __('product/product_show.place_of_origin') }} <strong>{{ $product1->country?->name ?? 'Country not specified' }}</strong>
                </p>

                {{-- Supplier Info --}}
                <div class="text-gray-700">
                    {{ __('product/product_show.supplier') }}
                    <a href="{{ url('/supplier/' . $product1->supplier->slug) }}" class="font-medium text-blue-600 hover:underline">
                        {{ $product1->supplier->name }}
                    </a>
                    
                </div>

            </div>
        </div>
    </div>
</section>


{{-- Chat Drawer --}}
<div id="chatDrawer"
    class="fixed top-0 right-0 h-full w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50 flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-semibold text-lg">{{ __('product/product_show.chat_with') }} {{ $product1->supplier->name }}</h3>
        <button id="closeChat" class="text-gray-500 hover:text-black">&times;</button>
    </div>

    {{-- Messages --}}
    <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-4">
        <div class="flex items-center gap-4 max-w-full" id="messages-product">

            {{-- Image --}}
            @if($product1->image_url)
            <img
                src="{{ $product1->image_url }}"
                alt="{{ $product1->name }}"
                class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
            @endif

            {{-- Text --}}
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-900 leading-tight">
                    {{ $product1->name }}
                </span>

                @if($product1->category)
                <span class="text-xs text-gray-500 mt-1">
                    {{ $product1->category->name }}
                </span>
                @endif

                <span class="text-xs text-gray-400 mt-2">
                    by {{ $product1->supplier->name }}
                </span>
            </div>

        </div>
    </div>


    {{-- Input --}}
    <div class="p-4 border-t">

        <form id="chatForm" class="flex gap-3">
            <input type="text" name="text" placeholder="{{ __('product/product_show.type_your_message') }}"
                class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
            <button type="submit"
                class="bg-[#23423F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#1D2D33]">
                {{ __('product/product_show.send') }}
            </button>
        </form>

    </div>
</div>

{{-- JS: AJAX sendMessage --}}
<script>
    window.onload = () => {
        let lastMessageId = null;
        let onFetchedMessages = false;
        const onThreadOpen = async () => {
            if (onFetchedMessages) return;
            const data = await fetchThread();
            if (!data) return;

            const {
                messages,
                thread
            } = data;

            lastMessageId = messages ? messages[messages.length - 1]?.id : null;

            if (messages && messages.length > 0)
                messages.forEach(addMessageElem);
            createMessageFeature(thread.id);

            setInterval(() => pollMessages(thread.id), 3000)
            onFetchedMessages = true;
        }

        document.getElementById('contactSupplierBtn').onclick = onThreadOpen;

        const fetchThread = async () => {
            const response = await fetch('/product/{{ $product1->id }}/chat', {
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const json = await response.json();
                return json;
            } else {
                alert('Failed to create tread.');
            }
            return null;
        }

        const chatMessagesElem = document.getElementById('chatMessages');

        const addMessageElem = (message) => {
            const text = message.text;
            const date = new Date(message.created_at).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            })

            const manufacturerMsg = `
                <div class="flex flex-col">
                    <div class="bg-gray-100 rounded-lg p-3 max-w-[75%] break-words break-all">
                        <p class="text-sm">${text}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-1">${date}</span>
                </div>`;

            const buyerMsg = `
                <div class="flex flex-col items-end">
                    <div class="bg-blue-900 text-white rounded-lg p-3 max-w-[75%] break-words break-all">
                        <p class="text-sm">${text}</p>
                    </div>
                    <span class="text-xs text-gray-300 mt-1">${date}</span>
                </div>`;

            const adminMsg = `
                <div class="flex flex-col">
                    <div class="bg-red-100 rounded-lg p-3 max-w-[75%] break-words break-all">
                        <p class="text-sm">${text}</p>
                    </div>
                    <span class="text-sm text-red-400 mt-1">ACROVOY MANAGER</span><span class="text-xs text-gray-400 mt-1">${date}</span>
                </div>`;

            const elem =
                message.role == 'buyer' ? buyerMsg :
                message.role == 'manufacturer' ? manufacturerMsg :
                message.role == 'admin' ? adminMsg :
                null;


            chatMessagesElem.insertAdjacentHTML('beforeend', elem);
        }

        const createMessageFeature = (threadId) => {
            document.getElementById('chatForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = this.querySelector('input[name="text"]');
                const text = input.value.trim();
                if (!text) return;
                await pollMessages(threadId);
                const response = await fetch(`/dashboard/messages/${threadId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        text
                    })
                });

                if (response.ok) {
                    const msg = await response.json();
                    lastMessageId = msg.id;
                    addMessageElem(msg);
                    chatMessagesElem.scrollTop = chatMessagesElem.scrollHeight;
                    input.value = '';
                } else {
                    alert('Failed to send message.');
                }
            });
        }


        const pollMessages = async (threadId) => {
            if (!lastMessageId) return;
            onLoadingMessages = true;
            const response = await fetch(`/dashboard/messages/${threadId}/poll?lastMessage=${lastMessageId}`, {
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const json = await response.json();
                const messages = json.messages;

                if (messages && messages.length > 0) {
                    lastMessageId = messages[messages.length - 1].id;
                    messages.forEach(addMessageElem)
                }
            }
        }
    };
</script>

<script>
    // Если хочешь, можно сделать клик по кнопке для перехода на связанный товар
    document.querySelectorAll('.color-option').forEach(btn => {
        btn.addEventListener('click', () => {
            const link = btn.dataset.link;
            if (link && link !== '#') {
                window.location.href = link;
            }
        });
    });
</script>

<script>
    const contactBtn = document.getElementById('contactSupplierBtn');
    const chatDrawer = document.getElementById('chatDrawer');
    const closeChat = document.getElementById('closeChat');

    contactBtn.addEventListener('click', () => {
        chatDrawer.classList.remove('translate-x-full');
        chatDrawer.classList.add('translate-x-0');
    });

    closeChat.addEventListener('click', () => {
        chatDrawer.classList.add('translate-x-full');
        chatDrawer.classList.remove('translate-x-0');
    });
</script>


{{-- JS для интерактивной галереи --}}
<script>
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => mainImage.src = thumb.dataset.src);
    });

    mainImage.addEventListener('click', () => {
        const lightbox = document.createElement('div');
        lightbox.className = 'fixed inset-0 bg-black/80 flex items-center justify-center z-50';
        const img = document.createElement('img');
        img.src = mainImage.src;
        img.className = 'rounded-lg shadow-lg cursor-zoom-in';
        img.style.maxHeight = '90%';
        img.style.maxWidth = '90%';

        // Масштаб при колесике мыши
        let scale = 1;
        img.addEventListener('wheel', e => {
            e.preventDefault();
            scale += e.deltaY * -0.001;
            scale = Math.min(Math.max(.5, scale), 3);
            img.style.transform = `scale(${scale})`;
        });

        lightbox.appendChild(img);
        document.body.appendChild(lightbox);

        lightbox.addEventListener('click', e => {
            if (e.target === lightbox) lightbox.remove();
        });
    });
</script>

<script>
    const colorOptions = document.querySelectorAll('.color-option');

    colorOptions.forEach(option => {
        option.addEventListener('click', () => {

            // remove active state from all
            colorOptions.forEach(o =>
                o.classList.remove('ring-2', 'ring-blue-900')
            );

            // add active state
            option.classList.add('ring-2', 'ring-blue-900');

            // change main image
            if (option.dataset.image) {
                mainImage.src = option.dataset.image;
            }
        });
    });
</script>



@endsection