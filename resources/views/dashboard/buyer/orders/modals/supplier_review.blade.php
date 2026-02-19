{{-- Модальное окно "Оценить продавца" --}}
<div id="supplierReviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-md w-full relative">
        <button onclick="closeModal('supplierReviewModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Оценить продавца</h3>

        <form action="{{ route('buyer.orders.supplier.review.store', $order->id) }}" method="POST">
            @csrf

            <div class="flex space-x-1 rating-stars mb-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star text-gray-300 cursor-pointer text-3xl" data-value="{{ $i }}">&#9733;</span>
                @endfor
                <input type="hidden" name="rating" class="rating-input" value="0">
            </div>

            <label class="block mb-2">Комментарий:</label>
            <textarea name="comment" rows="3" class="border rounded w-full px-2 py-1 mb-4" placeholder="Ваш отзыв о продавце"></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('supplierReviewModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Отправить</button>
            </div>
        </form>
    </div>
</div>