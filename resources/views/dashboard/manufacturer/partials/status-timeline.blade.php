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