@extends('layouts.app')

@section('content')

<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6 flex flex-wrap gap-1">
            <a href="{{ route('catalog.index') }}" class="hover:text-black">All Categories</a> /
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

                        {{-- Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5h2m2 2l4 4M7 7l10 10m-6 6H5v-6l10-10" />
                        </svg>

                        Edit
                    </a>
                    @endcan
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
                    <span>({{ $reviewsCount }} отзыв{{ $reviewsCount != 1 ? 'ов' : '' }})</span>

                    @if($soldCount > 0)
                    <span class="mx-2">•</span>
                    <span>Продано: {{ $soldCount }}</span>
                    @endif
                </div>


                {{-- Price / Quantity Table --}}
                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <h3 class="font-semibold text-lg leading-none">
                        Price per Quantity
                    </h3>

                    <span class="text-xs text-gray-500 leading-none">
                        Shipping cost not included
                    </span>
                    <table class="w-full text-left text-gray-700">
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
                                    {{ price($tier['price']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Color / Material Options --}}
                @php
                $colors = $product1->colors; // Получаем коллекцию цветов
                @endphp
                @if($colors->isNotEmpty())
                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3">Available Colors</h3>

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
                    <p class="text-sm text-gray-500 leading-tight">
                        The shipping templates selected for this product.
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



                {{-- Materials --}}
                @if($product1->materials->isNotEmpty())
                <div class="mb-8 flex items-start gap-3">

                    <span class="text-sm text-gray-500 pt-1 whitespace-nowrap">
                        Materials used:
                    </span>

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
                            <p class="font-semibold text-gray-900">
                                {{ $product1->moq ?? 'N/A' }} pcs
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Lead time</p>
                            <p class="font-semibold text-gray-900">
                                {{ $product1->lead_time ?? 'N/A' }} days
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Customization</p>
                            <p class="font-semibold text-gray-900">
                                {{ $product1->customization ? 'Available' : 'Not available' }}
                            </p>
                        </div>
                    </div>
                </div>


                {{-- Shipping Information --}}
                @if($product1->shippingTemplates->isNotEmpty())
                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <h3 class="font-semibold text-lg mb-2 leading-none">Shipping Information</h3>

                    <p class="text-sm text-gray-500 leading-tight">
                        The shipping templates selected for this product. Templates are managed in the <span class="font-medium">Shipping Center</span>.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                        @foreach($product1->shippingTemplates as $template)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <h4 class="font-semibold text-gray-900">{{ $template->title }}</h4>
                            @if(!empty($template->description))
                            <p class="text-gray-700 text-sm mt-1">{{ $template->description }}</p>
                            @endif
                            <div class="mt-2 text-gray-700 text-sm grid grid-cols-2 gap-2">
                                @if($template->price)
                                <div class="inline-flex items-center gap-2
            bg-blue-50 border border-blue-100
            px-3 py-1.5 rounded-lg">
                                    <span class="text-sm text-blue-900 font-medium">
                                        Price:
                                    </span>
                                    <span class="text-base font-semibold text-blue-900">
                                        ${{ number_format($template->price, 2) }}
                                    </span>
                                </div>
                                @endif
                                @if($template->delivery_time)
                                <div>
                                    <div class="font-medium">Delivery Time:</div>
                                    <div>{{ $template->delivery_time }} days</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Description --}}

                <p class="text-gray-700 mb-6 leading-relaxed">Place of Origin: <strong>{{ $product1->country?->name ?? 'Country not specified' }}</strong>
                </p>


                {{-- CTA Panel --}}
                <div class="mt-8 bg-white border border-gray-200 rounded-2xl p-6 shadow-lg mb-6">
                    <button
                        class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4 rounded-xl
                               text-lg font-semibold tracking-wide shadow-md transition-all transform hover:scale-105 mb-4">
                        Checkout
                    </button>

                    <div class="grid grid-cols-2 gap-4">
                        <button id="contactSupplierBtn"
                            class="w-full border border-gray-300 py-3 rounded-xl
                                   text-gray-800 font-medium shadow-sm
                                   hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105">
                            Contact Supplier
                        </button>





                        <form method="POST" action="{{ route('buyer.cart.add', $product1->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full border border-gray-300 py-3 rounded-xl
               text-gray-800 font-medium shadow-sm
               hover:border-black hover:text-black hover:shadow-md
               transition-all transform hover:scale-105">
                                Add to Cart
                            </button>
                        </form>




                    </div>
                </div>

                {{-- Supplier Info --}}
                <div class="text-gray-700">
                    Supplier:
                    <a href="{{ url('/supplier/' . $product1->supplier->slug) }}" class="font-medium text-blue-600 hover:underline">
                        {{ $product1->supplier->name }}
                    </a>
                    ({{ $product1->supplier->country ? $product1->supplier->country->name : 'N/A' }})
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
        <h3 class="font-semibold text-lg">Chat with {{ $product1->supplier->name }}</h3>
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
            <input type="text" name="text" placeholder="Type your message..."
                class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
            <button type="submit"
                class="bg-[#23423F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#1D2D33]">
                Send
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