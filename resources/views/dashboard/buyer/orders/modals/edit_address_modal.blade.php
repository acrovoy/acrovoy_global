<div
    x-show="editAddressModalOpen"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button @click="editAddressModalOpen = false"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            ✕
        </button>

        <h2 class="text-xl font-semibold mb-4">Редактировать адрес</h2>

        <form method="POST" action="{{ route('buyer.orders.update-address', $order->id) }}">
            @csrf
            @method('PUT')

            {{-- Поля формы --}}
            <div class="grid grid-cols-1 gap-4">
                <input type="text" name="first_name" value="{{ old('first_name', $order->first_name) }}" placeholder="Имя" class="border rounded p-2 w-full">
                <input type="text" name="last_name" value="{{ old('last_name', $order->last_name) }}" placeholder="Фамилия" class="border rounded p-2 w-full">
                <input type="text" name="country" value="{{ old('country', $order->country) }}" placeholder="Страна" class="border rounded p-2 w-full">
                <input type="text" name="city" value="{{ old('city', $order->city) }}" placeholder="Город" class="border rounded p-2 w-full">
                <input type="text" name="region" value="{{ old('region', $order->region) }}" placeholder="Регион" class="border rounded p-2 w-full">
                <input type="text" name="street" value="{{ old('street', $order->street) }}" placeholder="Улица" class="border rounded p-2 w-full">
                <input type="text" name="postal_code" value="{{ old('postal_code', $order->postal_code) }}" placeholder="Почтовый индекс" class="border rounded p-2 w-full">
                <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" placeholder="Телефон" class="border rounded p-2 w-full">
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="editAddressModalOpen = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Сохранить</button>
            </div>
        </form>
    </div>
</div>
