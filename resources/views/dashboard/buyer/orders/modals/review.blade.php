{{-- Модальное окно "Оставить отзыв" --}}
<div id="reviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-lg w-full relative">
        <button onclick="closeModal('reviewModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Оставить отзыв</h3>

        <form action="{{ route('buyer.orders.review.store', $order->id) }}" method="POST">
            @csrf

            @foreach($order->items as $item)
            <div class="mb-6">
                <p class="font-semibold">{{ $item->product_name }}</p>

                {{-- Общая оценка --}}
                <label>Общая оценка:</label>
                <div class="flex space-x-1 rating-stars" data-input="rating-{{ $item->id }}">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star text-gray-300 cursor-pointer text-2xl" data-value="{{ $i }}">&#9733;</span>
                    @endfor
                    <input type="hidden" name="rating[{{ $item->id }}]" id="rating-{{ $item->id }}" value="0">
                </div>

                {{-- Соответствие карточке --}}
                <label class="mt-2">Соответствие карточке:</label>
                <div class="flex space-x-1 rating-stars" data-input="match_rating-{{ $item->id }}">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star text-gray-300 cursor-pointer text-2xl" data-value="{{ $i }}">&#9733;</span>
                    @endfor
                    <input type="hidden" name="match_rating[{{ $item->id }}]" id="match_rating-{{ $item->id }}" value="0">
                </div>

                {{-- Текстовый отзыв к товару --}}
                <textarea name="comment[{{ $item->id }}]" rows="3" placeholder="Ваш отзыв о товаре" class="w-full border rounded px-3 py-2 mt-2"></textarea>
              
            </div>
            @endforeach

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('reviewModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Отправить</button>
            </div>
        </form>
    </div>
</div>