<div class="bg-white border rounded-xl shadow-sm">

    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-50 border-b">
            <tr>
                
                <th class="px-4 py-2 text-left font-medium">Product</th>
                <th class="px-4 py-2 text-left font-medium">Category</th>
                <th class="px-4 py-2 text-left font-medium">Price</th>
                <th class="px-4 py-2 text-left font-medium">Stock</th>
                <th class="px-4 py-2 text-left font-medium">Status</th>
                <th class="px-4 py-2 text-right font-medium">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $product)

            @php
                $image = $product->images?->where('is_main', 1)->first()?->cdn_url 
                    ?? $product->images?->first()?->cdn_url;
            @endphp

                <tr class="hover:bg-gray-50 transition">
                   

                    <td class="px-4 py-2 flex items-center gap-2">
                        @if(isset($product->images) && count($product->images) > 0)
                            <img
                                src="{{ $image ?? asset('images/no-image.png') }}"
                                alt="{{ $product->name }}"
                                class="w-12 h-12 object-cover rounded"
                            >
                        @else
                            <img src="" alt="No Image" class="w-12 h-12 object-cover rounded">
                        @endif
                        <span class="text-gray-800">{{ $product->name }}</span>
                    </td>

                    <td class="px-4 py-2 text-gray-800">{{ $product->category->name ?? '—' }}</td>

                    <td class="px-4 py-2">
                        <button class="price-button text-blue-600 hover:underline"
                                data-product-id="{{ $product->id }}"
                                data-product-price-tiers='@json($product->priceTiers ?? [])'>
                            @if($product->priceTiers->count())
                                @php $minPriceTier = $product->priceTiers->min('price'); @endphp
                                ${{ number_format($minPriceTier, 2) }}
                            @else
                                —
                            @endif
                        </button>
                    </td>

                    <td class="px-4 py-2">
                        <button class="stock-button text-blue-600 hover:underline"
        data-product-id="{{ $product->id }}"
        data-product-name="{{ $product->name }}"
        data-product-warehouses='@json($product->warehouses)'
>
    {{ $product->warehouses->sum(fn($w) => $w->pivot->quantity ?? 0) }}
</button>
                    </td>

                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                            @if($product->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($product->status == 'approved') bg-green-100 text-green-800
                            @elseif($product->status == 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-700 @endif">
                            @if($product->status == 'pending') On moderation
                            @else {{ ucfirst($product->status) }} @endif
                        </span>

                        @if($product->status == 'rejected')
                            <span class="relative group cursor-pointer text-yellow-600 ml-1">
                                ⚠️
                                <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 w-48 p-2 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50">
                                    {{ $product->reject_reason ?? 'No reason provided' }}
                                </div>
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('product.show', $product->slug) }}" class="text-gray-800 hover:underline">View</a>

                        @if($product->status !== 'pending')
                            <a href="{{ route('supplier.products.edit-step', [$product->id, 1]) }}"
   class="text-blue-600 hover:underline">
    Edit
</a>
                            <button type="button" class="text-red-600 hover:underline delete-product" data-id="{{ $product->id }}">Delete</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                        No products found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- Stock Drawer --}}
<div id="stock-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="stock-drawer"
     class="fixed right-0 top-0 h-full w-[440px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300
            z-50 flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Stock management
        </h3>

        <p class="text-sm text-gray-500 mt-1" id="stock-product-name"></p>
    </div>

    {{-- Body --}}
    <form id="stock-form" class="flex flex-col flex-1">

        <input type="hidden" name="product_id" id="stock-product-id">

        {{-- Warehouses --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-3">

            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                Warehouses
            </div>

            <div id="warehouse-stock-list" class="space-y-3">
                {{-- JS inject --}}
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between">

            <div class="text-xs text-gray-400">
                Changes apply immediately after saving
            </div>

            <div class="flex gap-2">
                <button type="button"
                        id="stock-cancel"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-200
                               text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save changes
                </button>
            </div>
        </div>

    </form>
</div>

{{-- Price Drawer --}}
<div id="price-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="price-drawer"
     class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300 z-50
            flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Price tiers
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Configure quantity-based pricing
        </p>
    </div>

    {{-- Body --}}
    <form id="price-form" class="flex flex-col flex-1">

        {{-- Notifications --}}
        <div id="drawer-notification-block" class="px-6 pt-4"></div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                Tiers
            </div>

            <div id="drawer-price-tiers" class="space-y-3">
                {{-- JS inject --}}
            </div>

            <button type="button"
                    onclick="addDrawerPriceTier()"
                    class="mt-2 text-sm text-gray-500 hover:text-gray-800
                           flex items-center gap-1 transition">
                + Add price tier
            </button>
        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between">

            <div class="text-xs text-gray-400">
                Changes apply after saving
            </div>

            <div class="flex gap-2">
                <button type="button"
                        id="price-cancel"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-200
                               text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save changes
                </button>
            </div>

        </div>

    </form>
</div>




<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ===========================
       STOCK DRAWER (WAREHOUSES)
    =========================== */

     const stockDrawer = document.getElementById('stock-drawer');
    const stockOverlay = document.getElementById('stock-drawer-overlay');
    const stockCancelBtn = document.getElementById('stock-cancel');

    const productNameEl = document.getElementById('stock-product-name');
    const productIdInput = document.getElementById('stock-product-id');
    const warehouseList = document.getElementById('warehouse-stock-list');
    const stockForm = document.getElementById('stock-form');

    const allWarehouses = @json($warehouses);

    /* ===========================
       OPEN
    =========================== */

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.stock-button');
        if (!btn) return;

        const productId = btn.dataset.productId;
        const productName = btn.dataset.productName ?? '';

        const productWarehouses = JSON.parse(btn.dataset.productWarehouses || '[]');

        productNameEl.textContent = productName;
        productIdInput.value = productId;

        renderWarehouses(allWarehouses, productWarehouses);

        openStockDrawer();
    });

    /* ===========================
       RENDER (MAIN FIX)
    =========================== */

    function renderWarehouses(allWarehouses, productWarehouses) {

    warehouseList.innerHTML = '';

    // EMPTY STATE
    if (!allWarehouses || allWarehouses.length === 0) {
        warehouseList.innerHTML = `
            <div class="flex flex-col items-start gap-3 p-5 rounded-xl
                        border border-gray-200 bg-gray-50">

                <div class="flex items-center gap-2 text-gray-900 font-medium text-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01M10.29 3.86l-8 14A2 2 0 004 21h16a2 2 0 001.71-3.14l-8-14a2 2 0 00-3.42 0z"/>
                    </svg>

                    No warehouses found
                </div>

                <p class="text-xs text-gray-500 leading-relaxed">
                    Before you can manage stock, you need to create at least one warehouse.
                </p>

                <a href="{{ route('supplier.warehouses.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm
                          bg-gray-900 text-white rounded-lg
                          hover:bg-gray-800 transition shadow-sm">

                    Create warehouse

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        `;
        return;
    }

    // NORMAL STATE
    allWarehouses.forEach(wh => {

        const match = productWarehouses.find(w => w.id === wh.id);
        const qty = match?.pivot?.quantity ?? 0;

        warehouseList.insertAdjacentHTML('beforeend', `
            <div class="group flex items-center justify-between gap-4
                        px-4 py-3 rounded-lg border border-gray-200
                        bg-white hover:bg-gray-50 transition">

                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900">
                        ${wh.name}
                    </span>

                    <span class="text-xs text-gray-400">
                        Stock quantity
                    </span>
                </div>

                <div class="flex items-center gap-2">

                    <input
                        type="number"
                        name="stocks[${wh.id}]"
                        value="${qty}"
                        min="0"
                        class="w-24 px-3 py-1.5 text-sm text-right
                               border border-gray-200 rounded-md
                               bg-white text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-gray-900/10
                               focus:border-gray-400
                               transition"
                    />

                    <span class="text-xs text-gray-400">
                        pcs
                    </span>
                </div>
            </div>
        `);
    });
}

    /* ===========================
       OPEN / CLOSE
    =========================== */

    function openStockDrawer() {
        stockDrawer.classList.remove('translate-x-full');
        stockOverlay.classList.remove('hidden');
    }

    function closeStockDrawer() {
        stockDrawer.classList.add('translate-x-full');
        stockOverlay.classList.add('hidden');
    }

    stockOverlay.addEventListener('click', closeStockDrawer);
    stockCancelBtn.addEventListener('click', closeStockDrawer);

    /* ===========================
       SUBMIT
    =========================== */

    stockForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const productId = productIdInput.value;

        const formData = new FormData(stockForm);
        const stocks = {};

        for (const [key, value] of formData.entries()) {
            if (key.startsWith('stocks[')) {
                const warehouseId = key.match(/\[(\d+)\]/)[1];
                stocks[warehouseId] = value === '' ? 0 : parseInt(value, 10);
            }
        }

        fetch(`/dashboard/supplier/products/${productId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ stocks })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update stock');
            }
        })
        .catch(err => console.error(err));
    });

    /* ===========================
       PRICE DRAWER
    =========================== */
    const drawer = document.getElementById('price-drawer');
    const overlay = document.getElementById('price-drawer-overlay');
    const cancelBtn = document.getElementById('price-cancel');
    const drawerTiers = document.getElementById('drawer-price-tiers');

    /* ===========================
       OPEN DRAWER
    ============================ */
    document.querySelectorAll('.price-button').forEach(btn => {
        btn.addEventListener('click', () => {
            drawerTiers.innerHTML = '';

            const productId = btn.dataset.productId;
            const tiers = JSON.parse(btn.dataset.productPriceTiers || '[]');

           if (tiers && tiers.length > 0) {
    tiers.forEach((tier, index) => {
        drawerTiers.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-4 gap-3 mb-2 items-center p-2 border rounded-lg bg-gray-50">
                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.min_qty ?? ''}"
                       placeholder="Min">

                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.max_qty ?? ''}"
                       placeholder="Max">

                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.price ?? ''}"
                       placeholder="Price">

                ${index === tiers.length - 1 && tiers.length > 1
                    ? '<button type="button" class="text-red-500 hover:text-red-700 text-lg remove-tier">×</button>'
                    : ''
                }
            </div>
        `);
    });
} else {
    window.addDrawerPriceTier?.();
}

            drawer.dataset.productId = productId;
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        });
    });

    /* ===========================
       CLOSE DRAWER
    ============================ */
    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }
    overlay.addEventListener('click', closeDrawer);
    cancelBtn.addEventListener('click', closeDrawer);

    function updateDrawerTierChain(startIndex) {
        const rows = drawerTiers.querySelectorAll('.grid');
        for (let i = startIndex; i < rows.length; i++) {
            const prevRow = rows[i - 1];
            const prevMaxInput = prevRow.querySelector('input:nth-of-type(2)');
            const currentRow = rows[i];
            const currentMinInput = currentRow.querySelector('input:nth-of-type(1)');
            const currentMaxInput = currentRow.querySelector('input:nth-of-type(2)');

            if (prevMaxInput && currentMinInput) {
                const prevMaxValue = prevMaxInput.value.trim();
                const prevMax = parseInt(prevMaxValue, 10);
                if (prevMaxValue !== '' && !isNaN(prevMax)) {
                    currentMinInput.value = prevMax + 1;
                }
            }

            if (currentMinInput && currentMaxInput) {
                const minValue = currentMinInput.value.trim();
                const maxValue = currentMaxInput.value.trim();
                const min = parseInt(minValue, 10);
                const max = parseInt(maxValue, 10);
                if (maxValue !== '' && !isNaN(max) && !isNaN(min) && max <= min) {
                    currentMaxInput.value = min + 1;
                }
            }
        }
    }

    function showDrawerNotification(message, type = 'error') {
        const container = document.getElementById('drawer-notification-block');
        if (!container) return;

        const colors = {
            success: 'bg-green-100 border-green-300 text-green-800',
            error: 'bg-red-100 border-red-300 text-red-800'
        };

        const html = `
            <div class="mb-2 rounded-lg ${colors[type]} px-4 py-3">
                ${message}
            </div>
        `;

        container.innerHTML = html;
    }

    function removeDrawerNotifications() {
        const container = document.getElementById('drawer-notification-block');
        if (!container) return;
        container.innerHTML = '';
    }

    function refreshDrawerDeleteButtons() {
        const rows = drawerTiers.querySelectorAll('.grid');
        rows.forEach((row, idx) => {
            const btn = row.querySelector('.remove-tier');
            if (idx === rows.length - 1 && rows.length > 1) {
                if (!btn) {
                    row.insertAdjacentHTML('beforeend', '<button type="button" class="text-red-600 remove-tier">&times;</button>');
                } else {
                    btn.style.display = '';
                }
            } else {
                if (btn) btn.remove();
            }
        });
    }

    function checkDrawerMaxMinValidation() {
        const rows = drawerTiers.querySelectorAll('.grid');
        let isError = false;

        for (const row of rows) {
            const inputs = row.querySelectorAll('input');
            const minValue = inputs[0]?.value.trim();
            const maxValue = inputs[1]?.value.trim();
            const min = parseInt(minValue, 10);
            const max = parseInt(maxValue, 10);
            if (minValue !== '' && maxValue !== '' && !isNaN(min) && !isNaN(max) && max < min) {
                isError = true;
                break;
            }
        }

        removeDrawerNotifications();
        if (isError) {
            showDrawerNotification('Max Quantity не может быть меньше Min Quantity!', 'error');
        }
        return !isError;
    }

    drawerTiers.addEventListener('change', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        const row = e.target.closest('.grid');
        if (!row) return;

        const rows = Array.from(drawerTiers.querySelectorAll('.grid'));
        const index = rows.indexOf(row);
        const inputs = row.querySelectorAll('input');
        if (e.target === inputs[1]) {
            updateDrawerTierChain(index + 1);
        }
        checkDrawerMaxMinValidation();
    });

    /* ===========================
       ADD TIER
    ============================ */
    window.addDrawerPriceTier = function () {
        const rows = drawerTiers.querySelectorAll('.grid');
        const currentCount = rows.length;

        if (rows.length > 0) {
            const lastMaxInput = rows[rows.length - 1].querySelector('input:nth-of-type(2)');
            const lastMaxValue = lastMaxInput?.value.trim() ?? '';
            if (lastMaxValue === '' || isNaN(parseInt(lastMaxValue, 10))) {
                removeDrawerNotifications();
                return;
            }

            const prevButton = rows[rows.length - 1].querySelector('.remove-tier');
            if (prevButton) prevButton.remove();
        }

        const prevMaxValue = rows.length > 0 ? parseInt(rows[rows.length - 1].querySelector('input:nth-of-type(2)').value.trim(), 10) : '';
        const nextMinValue = rows.length > 0 ? prevMaxValue + 1 : '';
        const minReadonly = rows.length > 0 ? 'readonly' : '';

        drawerTiers.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-4 gap-4 mb-2 items-center" data-index="${currentCount}">
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Min Qty" value="${nextMinValue}" ${minReadonly}>
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Max Qty">
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Unit Price $">
                ${currentCount === 0 ? '' : '<button type="button" class="text-red-600 remove-tier">&times;</button>'}
            </div>
        `);
    };

    /* ===========================
       REMOVE TIER
    ============================ */
    drawerTiers.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-tier')) {
            e.target.closest('.grid').remove();
            refreshDrawerDeleteButtons();
        }
    });

    /* ===========================
       SAVE TIERS → AJAX
    ============================ */
    document.getElementById('price-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!checkDrawerMaxMinValidation()) {
        return;
    }

    const productId = drawer.dataset.productId;
    const tiers = [];

    drawerTiers.querySelectorAll('.grid').forEach(tier => {
        const inputs = tier.querySelectorAll('input');
        if (!inputs[0].value && !inputs[1].value && !inputs[2].value) return;

        tiers.push({
            min_qty: inputs[0].value,
            max_qty: inputs[1].value,
            price: inputs[2].value
        });
    });

    fetch(`/dashboard/supplier/products/${productId}/update-price-tiers`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tiers })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const priceBtn = document.querySelector(`.price-button[data-product-id="${productId}"]`);
            if (data.tiers.length) {
                const minTier = data.tiers.reduce((prev, curr) => prev.min_qty < curr.min_qty ? prev : curr);
                priceBtn.textContent = `$${parseFloat(minTier.price).toFixed(2)}`;
                priceBtn.dataset.productPriceTiers = JSON.stringify(data.tiers);
            }

            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        } else {
            alert('Failed to save price tiers.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error while saving price tiers.');
    });
});


});
</script>











<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-product').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;

            if (!confirm('Are you sure you want to delete this product?')) return;

            fetch(`/dashboard/manufacturer/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.closest('tr').remove();
                } else {
                    alert(data.message || 'Failed to delete product.');
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>

