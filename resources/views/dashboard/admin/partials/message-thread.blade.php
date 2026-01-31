@php
$currentUser = auth()->user();
@endphp

<div class="flex flex-col space-y-4 chatMessages_list">
    @forelse ($messages as $message)
    @php
    // Определяем, мое это сообщение или чужое
    $isMine = $message->role === $currentUser->role && $message->user_id === $currentUser->id;
    @endphp

    @if ($isMine)
    {{-- Мои сообщения — справа, синий --}}
    <div class="flex flex-col items-end">
        <div class="bg-blue-900 text-white rounded-lg p-3 max-w-[75%] break-words break-all">
            <p class="text-sm m-0">{{ $message->text }}</p>
        </div>

        <span class="text-xs text-gray-400 mt-1">{{ $message->created_at }}</span>
    </div>
    @else
    {{-- Сообщения другой стороны — слева, серые --}}
    <div class="flex flex-col">
        @if ($message->role ==='buyer')
        <div class="bg-gray-100 rounded-lg p-3 max-w-[75%] break-words break-all">
            @else
            <div class="bg-blue-100 rounded-lg p-3 max-w-[75%] break-words break-all">
                @endif
                <p class="text-sm">{{ $message->text }}</p>
            </div>
            @if ($message->role ==='buyer')
            <span class="text-sm text-gray-600 mt-1">
                {{ $message->user?->name ?? 'Unknown User' }} {{ $message->user?->last_name ?? 'Unknown User' }}
            </span>
            @else
            <span class="text-sm text-gray-600 mt-1">
                {{ $message->user->supplier->name ?? 'Supplier' }}
            </span>
            @endif
            <span class="text-xs text-gray-400 mt-1">{{ $message->created_at }}</span>
        </div>
        @endif
        @empty
        <p class="text-gray-400 text-sm">No messages in this thread yet.</p>
        @endforelse
    </div>