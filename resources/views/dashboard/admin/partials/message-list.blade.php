<ul class="divide-y bg-white">
@foreach ($threads as $thread)
    <li
        data-thread-id="{{ $thread['id'] }}"
        class="
            p-4 cursor-pointer transition
            hover:bg-gray-50
            {{ isset($activeThread) && $activeThread->id === $thread['id']
                ? 'bg-gray-100 border-l-4 border-red-500'
                : '' }}
        "
    >
        <div class="flex flex-col gap-1">
            {{-- Название продукта --}}
            @if(!empty($thread['product_name']))
                <span class="text-sm text-gray-400 truncate">
                    {{ $thread['product_name'] }}
                </span>
            @endif

            <div class="flex items-center justify-between">
                <p class="text-base font-semibold text-gray-900 truncate">
                    {{ $thread['title'] }}
                </p>
                

                
            </div>
           
            <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">
                {{ $thread['updated_at'] }}
            </span>

            @if(!$thread['isRead'])
                    <span class="text-xs font-semibold text-orange-600">
                        NEW
                    </span>
                @endif

            </div>

            <p class="text-sm text-gray-600 truncate mt-1">
                {{ $thread['last_message'] }}
            </p>
        </div>
    </li>
@endforeach
</ul>
