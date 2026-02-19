{{-- Модальное окно "Жалоба / Возврат / Спор" --}}
<div id="disputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-lg w-full relative">
        <button onclick="closeModal('disputeModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Жалоба / Возврат / Спор</h3>
        <form action="{{ route('buyer.orders.dispute.store', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block mb-2">Причина спора</label>
            <textarea name="reason" rows="4" placeholder="Опишите проблему" class="w-full border rounded px-3 py-2 mb-4"></textarea>
            <label class="block mb-2">Желаемое действие</label>
            <select name="action" class="w-full border rounded px-3 py-2 mb-4">
                <option value="return">Возврат</option>
                <option value="compensation">Компенсация</option>
                <option value="exchange">Обмен</option>
            </select>
            <label class="block mb-2">Прикрепить файл (опционально)</label>
            <input type="file" name="attachment" class="w-full border rounded px-3 py-2 mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('disputeModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Отправить</button>
            </div>
        </form>
    </div>
</div>