@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('manufacturer.orders') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to orders
        </a>

<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">
                    Order #{{ $order['id'] }} 
                </h2>
                <p class="text-sm text-gray-500">
                    Review order items, track delivery, and handle disputes or status updates.
                </p>

              
        </div>
        <span class="px-3 py-1 rounded text-sm
            @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
            @elseif($order['status'] === 'paid') bg-blue-100 text-blue-800
            @elseif($order['status'] === 'shipped') bg-green-100 text-green-800
            @else bg-gray-100 text-gray-800
            @endif
        ">
            {{ ucfirst($order['status']) }}
        </span>
    </div>


    @php

// Проверка: доставка Acrovoy и цена 0
        $isAcrovoyPending = ($order['delivery_method'] === 'Delivery by Acrovoy' && ($order['delivery_price'] ?? 0) == 0);

@endphp


@if($isAcrovoyPending)
        <div class="p-3 mb-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded">
            Awaiting for the delivery price and delivery time from Acrovoy.
        </div>
    @endif


   {{-- Товары --}}
<div x-data="{
    open: false,
    itemId: null,
    origin_country_id: null,
    origin_region_id: null,
    origin_city_id: null,
    origin_city_manual: '',
    origin_address: '',
    origin_contact_name: '',
    origin_contact_phone: ''
}" class="rounded-lg border p-4 bg-white shadow-sm">

    <div class="flex items-center justify-between mb-2 border-b pb-2">
        <h3 class="text-xs uppercase tracking-wide text-gray-500">
            Products in the order:
        </h3>
    </div>

    @foreach($order['items'] as $item)
        <div class="py-1 flex justify-between items-center">
            <div class="flex items-center gap-3">
                {{-- Картинка товара --}}
                <img
                    src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('images/no-photo.png') }}"
                    alt="{{ $item['product'] }}"
                    class="w-12 h-12 rounded object-contain bg-gray-50 border"
                />

                {{-- Название и количество --}}
                <div class="max-w-xs">
                    <p class="font-medium text-gray-900 truncate" title="{{ $item['product'] }}">
                        {{ $item['product'] }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $item['qty'] }} × {{ number_format($item['price'], 2) }} $
                    </p>
                </div>
            </div>

            @foreach($order_items as $itemO)

                {{-- Место погрузки --}}
                @php
                    $shipment = $itemO->shipments->first();
                @endphp

                <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 w-80">
                    <div class="flex justify-between text-sm font-medium text-gray-900 mb-1">
                        <div>
                             Данные места погрузки
                        </div>
                    


                    </div>

                    @if($shipment && ($shipment->origin_address || $shipment->origin_city_id || $shipment->origin_region_id))
                        <div class="mb-4 text-xs text-gray-700 space-y-1">
                            @if($shipment->origin_contact_name)
                                <div><strong>Contact Name:</strong> {{ $shipment->origin_contact_name }}</div>
                            @endif
                            @if($shipment->origin_contact_phone)
                                <div><strong>Phone:</strong> {{ $shipment->origin_contact_phone }}</div>
                            @endif
                            @if($shipment->origin_address)
                                <div><strong>Address:</strong> {{ $shipment->origin_address }}</div>
                            @endif
                            @if($shipment->originCity?->name)
                                <div><strong>City:</strong> {{ $shipment->originCity?->name }}</div>
                            @endif
                            @if($shipment->originRegion?->name)
                                <div><strong>Region:</strong> {{ $shipment->originRegion?->name }}</div>
                            @endif
                            @if($shipment->originCountry?->name)
                                <div><strong>Country:</strong> {{ $shipment->originCountry?->name }}</div>
                            @endif
                        </div>

                        @foreach($order_items as $itemO)
                            <button type="button" @click="open = true; itemId = {{ $itemO->id }}; 
                                origin_country_id = {{ $itemO->shipments->first()?->origin_country_id ?? 'null' }};
                                origin_region_id = {{ $itemO->shipments->first()?->origin_region_id ?? 'null' }};
                                origin_city_id = {{ $itemO->shipments->first()?->origin_city_id ?? 'null' }};
                                origin_city_manual = '{{ $itemO->shipments->first()?->origin_city_manual ?? '' }}';
                                origin_address = '{{ $itemO->shipments->first()?->origin_address ?? '' }}';
                                origin_contact_name = '{{ $itemO->shipments->first()?->origin_contact_name ?? '' }}';
                                origin_contact_phone = '{{ $itemO->shipments->first()?->origin_contact_phone ?? '' }}';
                            ">
                                <span class="px-3 py-1.5 text-sm
                      border border-blue-300 text-blue-700
                      rounded-md
                      hover:bg-blue-50 hover:border-blue-400">Edit pickup address</span>
                            </button>
                        @endforeach

                    @else
                        <div class="px-3 py-1.5 text-sm
                      border border-blue-300 text-blue-700
                      rounded-md
                      hover:bg-blue-50 hover:border-blue-400">
                            Pickup address not entered
                        </div>
                        
                        <button type="button"
                                class="px-3 py-1.5 text-sm
                      border border-blue-300 text-blue-700
                      rounded-md
                      hover:bg-blue-50 hover:border-blue-400"
                                @click="open = true; itemId = {{ $itemO->id }}">
                            Enter Pickup Address
                        </button>
                        
                    @endif
                </div>
            @endforeach

            {{-- Сумма за товар --}}
            <div class="font-semibold text-gray-900 ml-4">
                {{ number_format($item['total'], 2) }} $
            </div>
        </div>
    @endforeach

    {{-- Стоимость доставки --}}
    @if(!empty($order['delivery_price']) && $order['delivery_price'] > 0)
        <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-xs uppercase tracking-wide text-gray-500">
            <span>Delivery: <span class="text-xs uppercase tracking-wide text-gray-500">{{$order['delivery_method']}}</span></span>
            @if($order['delivery_method'] === 'Delivery by Acrovoy')
            <span class="font-semibold">0.00 $</span>
            @else
            <span class="font-semibold">{{ number_format($order['delivery_price'], 2) }} $</span>
            @endif
        </div>
    @else
        <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-gray-700 text-sm">
            <span>Delivery by Acrovoy</span>
            <span class="font-semibold">{{$order['delivery_price']}}</span>
        </div>
    @endif

    {{-- Общая сумма заказа --}}
    <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-gray-800 font-medium text-lg">
        <span>Total</span>
        <span>{{ number_format($order['total'] + ($order['delivery_price'] ?? 0), 2) }} $</span>
    </div>

    {{-- Модалка для ввода места погрузки --}}
    <div x-show="open" x-transition x-cloak x-data="pickupModal()"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg" @click.away="open = false">
        <h3 class="text-lg font-semibold mb-4">Enter Pickup Address</h3>

        <form method="POST" :action="`/manufacturer/orders/origin/${itemId}`">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Страна --}}
                <div>
                    <label class="text-sm text-gray-600">Country</label>
                    <select name="origin_country_id" id="origin-country" x-model="origin_country_id" class="w-full ...">
                        <option value="">Select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Регион --}}
                <div>
                    <label class="text-sm text-gray-600">Region</label>
                    <select name="origin_region_id" id="origin-region" class="w-full border rounded p-2" disabled>
                        <option value="">Select region</option>
                    </select>
                </div>

                {{-- Город --}}
                <div>
                    <label class="text-sm text-gray-600">City</label>
                    <select name="origin_city_id" id="origin-city" class="w-full border rounded p-2">
                        <option value="">Select city</option>
                    </select>
                    <small class="text-gray-500 block mt-1">
                        If your city is not listed, enter manually below
                    </small>
                    <input type="text" name="origin_city_manual" id="origin-city-manual"
                            x-model="origin_city_manual"
                            placeholder="Enter city"
                            class="w-full border rounded p-2 mt-1">
                </div>

                {{-- Улица --}}
                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-600">Street, house, apartment</label>
                    <input type="text" name="origin_address" id="origin-address"
                            x-model="origin_address"
                            class="w-full border rounded p-2">
                </div>

                {{-- Контакт --}}
                <div>
                    <label class="text-sm text-gray-600">Contact Name</label>
                    <input type="text" name="origin_contact_name" id="origin-contact-name"
                        x-model="origin_contact_name"
                        class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Contact Phone</label>
                    <input type="text" name="origin_contact_phone" id="origin-contact-phone"
                        x-model="origin_contact_phone"
                        class="w-full border rounded p-2">
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

</div>









     {{-- Spory / Disputes для продавца --}}
<div class="border rounded-lg p-4 bg-gray-50" x-data="{ openModalId: null }">
    <h3 class="font-semibold mb-3 text-lg">Disputes</h3>

    @if(count($order['disputes']) > 0)
        <ul class="space-y-3">
            @foreach($order['disputes'] as $dispute)
                <li class="border rounded-lg p-4 bg-white flex flex-col md:flex-row justify-between gap-4">

                    {{-- Левая часть — информация --}}
                    <div class="space-y-1 text-sm">
                        <p>
                            <strong>Customer:</strong>
                            {{ $dispute->user->name ?? '—' }}
                        </p>

                        <p>
                            <strong>Reason:</strong>
                            {{ $dispute->reason }}
                        </p>

                        <p>
                            <strong>Requested action:</strong>
                            {{ ucfirst($dispute->action) }}
                        </p>

                        <p class="flex items-center gap-2">
                            <strong>Status:</strong>

                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                @if($dispute->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($dispute->status === 'supplier_offer') bg-blue-100 text-blue-800
                                @elseif($dispute->status === 'buyer_reject') bg-red-100 text-red-800
                                @elseif($dispute->status === 'resolved') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                            </span>
                        </p>

                        @if($dispute->attachment)
                            <p>
                                <a href="{{ asset('storage/' . $dispute->attachment) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:underline">
                                    View attachment
                                </a>
                            </p>
                        @endif

                        {{-- Показываем комментарий покупателя --}}
@if($dispute->buyer_comment)
    <div class="mt-2 p-3 bg-red-100 border-l-4 border-red-500 rounded text-sm">
        <strong>Comment from customer:</strong><br>
        {{ $dispute->buyer_comment }}
    </div>
@endif

{{-- Комментарий продавца --}}
@if($dispute->supplier_comment)
    <div class="mt-2 p-3 bg-blue-100 border-l-4 border-blue-500 rounded text-sm">
        <strong>Comment from supplier:</strong><br>
        {{ $dispute->supplier_comment }}
    </div>
@endif


{{-- Апелляция к администратору / Решение администратора --}}
@if($dispute->status === 'appealed' || $dispute->admin_comment)
    <div class="mt-2 p-3 bg-orange-100 border-l-4 border-orange-500 rounded text-sm">
        <strong>Admin decision:</strong><br>
        {{ $dispute->admin_comment ?? 'На рассмотрении администратора' }}
    </div>
@endif

                        


                    </div>

                   {{-- Правая часть — действия --}}
<div class="flex items-start">
    @if($dispute->status !== 'admin_review' && $dispute->status !== 'resolved' && $dispute->status !== 'cancelled')
        <button
            @click="openModalId = {{ $dispute->id }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
        >
            Manage dispute
        </button>
    @elseif($dispute->status === 'admin_review')
        <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs">
            Awaiting admin review
        </span>
    @endif
</div>

{{-- Modal для управления спором --}}
<div x-show="openModalId === {{ $dispute->id }}"
     x-cloak
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">

        <button @click="openModalId = null"
                class="absolute top-2 right-3 text-gray-400 hover:text-gray-700 text-xl">
            &times;
        </button>

        <h3 class="font-bold mb-4 text-lg">
            Manage dispute #{{ $dispute->id }}
        </h3>

        {{-- Блокируем форму, если спор в статусе appealed --}}
        <form action="{{ route('manufacturer.orders.dispute.update', [$order['id'], $dispute->id]) }}"
              method="POST"
              class="space-y-4 @if($dispute->status === 'appealed') opacity-50 pointer-events-none @endif">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">
                    Status (propose solution)
                </label>
                <select name="status"
                        class="w-full border rounded px-3 py-2 text-sm">
                    <option value="pending" @if($dispute->status === 'pending') selected @endif>
                        Pending
                    </option>
                    <option value="supplier_offer" @if($dispute->status === 'supplier_offer') selected @endif>
                        Propose solution
                    </option>
                    <option value="rejected" @if($dispute->status === 'rejected') selected @endif>
                        Rejected
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Comment (optional)
                </label>
                <textarea name="supplier_comment"
                          rows="3"
                          class="w-full border rounded px-3 py-2 text-sm"
                          placeholder="Add a comment for the customer">{{ $dispute->admin_comment }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                        @click="openModalId = null"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>


                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500 italic">
            No disputes for this order.
        </p>
    @endif
</div>




   {{-- Change status --}}
<div class="border rounded-lg p-4 bg-gray-50">
    <h3 class="font-semibold mb-3">Change order status</h3>

    @php
        use App\Services\OrderStatusService;
        $available = OrderStatusService::availableStatuses($order['status']);

        // Проверка: доставка Acrovoy и цена 0
        $isAcrovoyPending = ($order['delivery_method'] === 'Delivery by Acrovoy' && ($order['delivery_price'] ?? 0) == 0);


        if ($isAcrovoyPending) {
            // Убираем "confirmed" из доступных статусов
            $available = array_filter($available, fn($s) => $s !== 'confirmed');
        }
    @endphp


    @if($isAcrovoyPending)
        <div class="p-3 mb-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded">
            Status cannot be changed to "Confirmed" yet. Delivery price and delivery time will be presented by Acrovoy soon.
        </div>
    @endif

    @if(count($available) > 0)
        <form method="POST"
              action="{{ route('manufacturer.orders.update-status', $order['id']) }}"
              class="flex flex-col gap-3">
            @csrf

            <select name="status"
                    class="border rounded px-3 py-2 text-sm"
                    required>
                <option value="">Select new status</option>
                @foreach($available as $status)
                    <option value="{{ $status }}">
                        {{ __('order.status.' . $status) }}
                    </option>
                @endforeach
            </select>

            <textarea name="comment"
                      rows="2"
                      placeholder="Comment (optional)"
                      class="border rounded px-3 py-2 text-sm"></textarea>

            <button type="submit"
                    class="self-start px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                Update status
            </button>
        </form>
    @else
        <p class="text-gray-500 italic">
            Status cannot be changed at this moment.
        </p>
    @endif
</div>


{{-- Order status timeline --}}
<div class="border rounded-lg p-4 bg-white px-10">
    <h3 class="font-semibold mb-4">Order timeline</h3>

    <ol class="relative border-l border-gray-300">
        @forelse($order['status_history'] as $history)
            <li class="mb-6 ml-6">
                {{-- Dot --}}
                <span class="absolute -left-3 flex items-center justify-center
                             w-6 h-6 rounded-full
                             @if($history->status === 'cancelled') bg-red-500
                             @elseif($history->status === 'completed') bg-green-600
                             @else bg-blue-600
                             @endif
                             text-white text-sm">
                    ✓
                </span>

                {{-- Status --}}
                <h4 class="font-medium">
                    {{ __('order.status.' . $history->status) }}
                </h4>

                {{-- Date --}}
                <time class="block text-sm text-gray-500">
                    {{ $history->created_at->format('d.m.Y H:i') }}
                </time>

                {{-- Comment --}}
                @if($history->comment)
                    <p class="mt-1 text-gray-600">
                        {{ $history->comment }}
                    </p>
                @endif
            </li>
        @empty
            <li class="ml-6 text-gray-500">
                Status history is empty
            </li>
        @endforelse
    </ol>
</div>


{{-- Tracking number & Invoice --}}
<div class="border rounded-lg p-4 bg-gray-50">
    <h3 class="font-semibold mb-3">Shipping & Invoice</h3>

    <form method="POST"
          action="{{ route('manufacturer.orders.update-tracking', $order['id']) }}"
          enctype="multipart/form-data"
          class="flex flex-col gap-3">
        @csrf

        @php
            $isCompleted = in_array($order['status'], ['completed', 'cancelled', 'pending']);
        @endphp

        {{-- Tracking Number --}}
        <label class="text-sm font-medium">Tracking Number</label>
        <input type="text"
               name="tracking_number"
               value="{{ $order['tracking_number'] ?? '' }}"
               class="border rounded px-3 py-2 text-sm w-full"
               placeholder="Enter tracking number"
               @if($isCompleted) disabled @endif>

        {{-- Invoice --}}
        <label class="text-sm font-medium">Invoice (PDF)</label>
        
        <div class="relative w-full">
            <input type="file" name="invoice_file" accept="application/pdf"
                class="opacity-0 absolute inset-0 w-full h-full cursor-pointer"
                @if(in_array($order['status'], ['pending', 'cancelled'])) disabled @endif
                onchange="document.getElementById('invoice-label').innerText = this.files[0]?.name || 'Choose a file'">

            <button type="button"
                    class="w-full px-4 py-2 bg-gray-200 border border-gray-700 rounded hover:bg-gray-300 text-gray-700 text-sm text-left cursor-pointer"
                    @if(in_array($order['status'], ['pending', 'cancelled'])) disabled @endif>
                <span id="invoice-label">Choose a file</span>
            </button>
        </div>

        @if(!empty($order['invoice_file']))
            <a href="{{ asset('storage/' . $order['invoice_file']) }}" target="_blank"
               class="text-blue-600 hover:underline text-sm">
                View current invoice
            </a>
        @endif

        <button type="submit"
                class="self-start px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                @if($isCompleted) disabled @endif>
            Update
        </button>
    </form>
</div>


    {{-- Customer --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-2">Customer</h3>
        <p>{{ $order['customer'] }}</p>
        <p class="text-sm text-gray-500">{{ $order['email'] }}</p>
    </div>

   {{-- Contact and Shipping Information --}}
<div class="border rounded-lg p-4">
    <h3 class="font-semibold mb-2">Contact and Shipping Information</h3>

    <div class="text-sm space-y-1">
        <p>
            <strong>Name:</strong>
            {{ $order['first_name'] }} {{ $order['last_name'] }}
        </p>

        <p>
            <strong>Country:</strong>
            {{ $order['country'] }}
        </p>

        <p>
            <strong>City:</strong>
            {{ $order['city'] }}
        </p>

        @if(!empty($order['region']))
            <p>
                <strong>Region:</strong>
                {{ $order['region'] }}
            </p>
        @endif

        <p>
            <strong>Street:</strong>
            {{ $order['street'] }}
        </p>

        <p>
            <strong>Postal code:</strong>
            {{ $order['postal_code'] }}
        </p>

        @if(!empty($order['phone']))
            <p>
                <strong>Phone:</strong>
                {{ $order['phone'] }}
            </p>
        @endif
    </div>
</div>


 





    {{-- Footer --}}
    <div class="flex justify-between text-sm text-gray-500">
        <span>Order date: {{ $order['date'] }}</span>
        
    </div>

</div>



<script>
const countrySelect = document.getElementById('origin-country');
const regionSelect = document.getElementById('origin-region');
const citySelect = document.getElementById('origin-city');
const cityManualInput = document.getElementById('origin-city-manual');

const regionsUrl = @json(route('manufacturer.locations.regions'));
const locationsUrl = @json(route('manufacturer.locations.locations'));

// 1. Выбор страны → подгрузка регионов
countrySelect?.addEventListener('change', function() {
    const countryId = this.value;
    regionSelect.disabled = !countryId;
    regionSelect.innerHTML = '<option value="">Select region</option>';
    citySelect.innerHTML = '<option value="">Select city</option>';
    citySelect.disabled = true;
    cityManualInput.value = '';

    if (!countryId) return;

    fetch(`${regionsUrl}?country_id=${countryId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                regionSelect.appendChild(opt);
            });
        })
        .catch(console.error);
});

// 2. Выбор региона → подгрузка городов
regionSelect?.addEventListener('change', function() {
    const regionId = this.value;
    citySelect.disabled = !regionId;
    citySelect.innerHTML = '<option value="">Select city</option>';
    cityManualInput.value = '';

    if (!regionId) return;

    fetch(`${locationsUrl}?region_id=${regionId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                citySelect.appendChild(opt);
            });
        })
        .catch(console.error);
});

// 3. Если выбирают город из списка — очищаем ручной ввод
citySelect?.addEventListener('change', function() {
    if (this.value) cityManualInput.value = '';
});

// 4. Если пользователь вводит город вручную — очищаем select
cityManualInput?.addEventListener('input', function() {
    if (this.value.trim() !== '') citySelect.value = '';
});
</script>


@endsection
