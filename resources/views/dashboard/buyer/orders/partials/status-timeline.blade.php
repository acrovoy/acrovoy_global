{{-- Таймлайн статусов заказа --}}
<div class="mt-4 ml-4">
    <h4 class="font-semibold mb-3">История заказа</h4>

    <ol class="relative border-l border-gray-300">
        @forelse($order->statusHistory as $history)
            <li class="mb-6 ml-6">
                {{-- Точка --}}
                <span class="absolute -left-3 flex items-center justify-center
                             w-6 h-6 rounded-full
                             @if($history->status === 'cancelled') bg-red-500
                             @elseif($history->status === 'completed') bg-green-600
                             @else bg-blue-600
                             @endif
                             text-white text-sm">
                    ✓
                </span>

                {{-- Статус --}}
                <h5 class="font-medium">
                    {{ __('order.status.' . $history->status) }}
                </h5>

                {{-- Дата --}}
                <time class="block text-sm text-gray-500">
                    {{ $history->created_at->format('d.m.y | H:i') }}
                </time>

                {{-- Комментарий --}}
                @if($history->comment)
                    <p class="mt-1 text-gray-600">
                        {{ $history->comment }}
                    </p>
                @endif
            </li>
        @empty
            <li class="ml-6 text-gray-500">
                История статусов пока отсутствует
            </li>
        @endforelse
    </ol>
</div>