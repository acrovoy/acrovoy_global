@extends('dashboard.layout')

@section('dashboard-content')
<div class="mb-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            
            <h1 class="text-2xl font-semibold mt-1 text-gray-800">My Cart</h1>
            <p class="text-sm text-gray-500">
                View all your product items.
            </p>
        </div>
    </div>

</div>

@if($cartItems->isEmpty())
    <div class="flex flex-col items-center justify-center text-center py-20">

        {{-- Icon --}}
        <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gray-100 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-10 h-10 text-gray-400"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h14m-9-6v6m4-6v6"/>
            </svg>
        </div>

        {{-- Title --}}
        <h2 class="text-xl font-semibold text-gray-800 mb-2">
            Your cart is empty
        </h2>

        {{-- Subtitle --}}
        <p class="text-gray-500 max-w-md mb-6">
            Looks like you haven’t added any products yet.
            Start exploring our catalog to find something you like.
        </p>

        {{-- Button --}}
        <a href="{{ route('catalog.index') }}"
           class="px-6 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
            Browse Products
        </a>

    </div>
@else
    <div class="space-y-4">
        @foreach($cartItems as $item)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex flex-col md:flex-row justify-between items-start md:items-center hover:shadow-md transition">

                {{-- Left --}}
                <div class="flex items-start md:items-center gap-4 flex-1">
                    <div>
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                    </div>

                    <div class="w-20 h-20 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                        <img src="{{ $item->image_url }}"
                             alt="{{ $item->product?->name }}"
                             class="w-full h-full object-cover">
                    </div>

                    <div>
                        <a href="{{ route('product.show', $item->product?->slug) }}"
   target="_blank"
   class="font-semibold text-gray-900 hover:text-gray-700 transition-colors">
    {{ $item->product?->name }}
</a>

                        <p class="text-sm text-gray-500">
                            Price:
                            <span class="item-price">
                                ${{ number_format($item->price, 2) }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Right --}}
                <div class="flex items-center gap-4 mt-4 md:mt-0">

                    {{-- Quantity --}}
                    <form action="{{ route('buyer.cart.update', $item->id) }}"
      method="POST"
      class="cart-update-form flex items-center gap-2 bg-gray-50 border rounded-lg px-2 py-1"
      data-moq="{{ $item->product->moq ?? 1 }}">

    @csrf
    @method('PATCH')

    <button type="submit"
            name="action"
            value="decrease"
            class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
        −
    </button>

    <span class="min-w-[32px] text-center font-semibold quantity-value">
        {{ $item->quantity }}
    </span>

    <button type="submit"
            name="action"
            value="increase"
            class="px-2 py-1 text-gray-700 hover:bg-gray-100 rounded">
        +
    </button>

</form>

                    {{-- Item total --}}
                    <div class="text-right flex flex-col items-end gap-1 min-w-[90px]">

                        <p class="font-semibold text-gray-900 item-total">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </p>

                        <form action="{{ route('buyer.cart.remove', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="text-sm text-red-500 hover:underline">
                                Remove
                            </button>
                        </form>

                    </div>

                </div>

            </div>
        @endforeach
    </div>

    {{-- Cart total --}}
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 border-t border-gray-200 pt-4 gap-4">

        <span class="text-lg font-semibold text-gray-900">
            Total
        </span>

        <span id="cart-total"
              class="text-lg font-bold text-gray-900">
            ${{ number_format($total, 2) }}
        </span>

        <a href="{{ route('buyer.orders.checkout') }}"
           class="px-6 py-3 bg-[#1877f2] text-white rounded-lg font-semibold hover:bg-blue-700 transition">
            Checkout
        </a>

    </div>
@endif


<script>

function getMoq(form) {
    return parseInt(form.dataset.moq || 1);
}

document.addEventListener('DOMContentLoaded', function () {

    const forms = document.querySelectorAll('form[action*="cart/update"]');

    forms.forEach(function(form) {

        const quantityEl = form.querySelector('.quantity-value');
        const minusBtn = form.querySelector('button[value="decrease"]');
        const moq = getMoq(form);

        function updateState() {

            const qty = parseInt(quantityEl.textContent);

            minusBtn.disabled = qty <= moq;
            minusBtn.classList.toggle('opacity-40', qty <= moq);
            minusBtn.classList.toggle('cursor-not-allowed', qty <= moq);

        }

        updateState();

        form.addEventListener('submit', async function(e) {

            e.preventDefault();

            const button = e.submitter;
            if (!button) return;

            const action = button.value;

            const currentQty = parseInt(quantityEl.textContent);

            // 🔥 MOQ защита (оставляем серверную тоже)
            if (action === 'decrease' && currentQty <= moq) {
                return;
            }

            try {

                const response = await fetch(form.getAttribute('action'), {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },

                    body: new URLSearchParams({
                        _method: 'PATCH',
                        action: action
                    })

                });


                if (!response.ok) {

                    console.error('Server error', response.status);

                    return;

                }


                const data = await response.json();


                if (!data.quantity) {

                    console.error('Invalid JSON', data);

                    return;

                }


                // 🔥 обновляем quantity (span)
                quantityEl.textContent = data.quantity;


                form.closest('.bg-white')
                    .querySelector('.item-total')
                    .textContent = '$' + data.itemTotal;


                const itemPrice = form.closest('.bg-white').querySelector('.item-price');

                if (itemPrice) {

                    if (itemPrice.textContent !== '$' + data.price) {

                        itemPrice.textContent = '$' + data.price;

                        itemPrice.classList.add('price-flash');

                        setTimeout(() => {
                            itemPrice.classList.remove('price-flash');
                        }, 500);

                    }

                }


                document.querySelector('#cart-total')
                    .textContent = '$' + data.cartTotal;


                // 🔥 обновляем состояние MOQ после ответа
                updateState();

            } catch(error) {

                console.error('Fetch failed:', error);

            }

        });

    });

});

</script>

<style>
.price-flash {
    color: #ff0000 !important;  /* ярко-красный */
    font-weight: 700;            /* жирный */
    transition: color 0.5s ease;
}
</style>

@endsection