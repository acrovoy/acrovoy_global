@extends('dashboard.admin.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-4 flex items-center justify-between">
    Message Center

    <div class="flex items-center gap-4">

        {{-- Выбор пользователя для просмотра переписки --}}
        <div class="flex items-center gap-2">
            <label for="view_user" class="text-sm font-medium">View:</label>
            <select id="view_user" class="border rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        {{-- Выбор пользователя для нового сообщения --}}
        <div class="flex items-center gap-2">
            <label for="new_message_user" class="text-sm font-medium">Send To:</label>
            <select id="new_message_user" class="border rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

    </div>
</h2>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-4rem)]">

    {{-- LEFT: Threads --}}
    <div class="lg:col-span-1 border rounded-lg overflow-y-auto h-full" id="threads_container">
        <ul class="divide-y">
            @foreach ($threads as $thread)
                <li class="p-4 cursor-pointer hover:bg-gray-50 {{ $thread['unread'] ? 'bg-indigo-50' : '' }}" data-thread-id="{{ $thread['id'] }}">
                    <p class="font-semibold">{{ $thread['title'] }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ $thread['last_message'] }}</p>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- RIGHT: Messages & Form --}}
    <div class="lg:col-span-2 border rounded-lg flex flex-col h-full overflow-y-auto">

        {{-- Чат --}}
            <div id="chat_container" class="flex-1 p-4 overflow-y-auto space-y-4">
                @foreach ($messages as $message)
                    @php
                        $isAdmin = $message['from'] === 'admin';
                    @endphp
                    <div class="flex {{ $isAdmin ? 'flex-col items-end' : 'flex-col' }}">
                        <div class="relative bg-{{ $isAdmin ? 'indigo-600 text-white' : 'gray-100' }} rounded-lg p-3 max-w-[75%]">

                            {{-- Сообщение --}}
                            <p class="text-sm">{{ $message['text'] }}</p>

                            {{-- Крестик для удаления в правом верхнем углу блока --}}
                            <form action="" method="POST" class="absolute -top-2 -right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold hover:bg-red-600">
                                    &times;
                                </button>
                            </form>

                        </div>
                        <span class="text-xs text-gray-400 mt-1">{{ $message['created_at'] }}</span>
                    </div>
                @endforeach
            </div>

        {{-- Форма отправки --}}
        <div class="border-t">
            <form id="send_message_form" class="p-4 flex gap-3" method="POST" action="{{ route('admin.messages.send') }}">
                @csrf
                <input type="hidden" name="user_id" id="form_user_id" value="">
                <input type="text" name="message" placeholder="Type your message..."
                    class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Send
                </button>
            </form>
        </div>

    </div>

</div>

{{-- JS для подгрузки переписки и выбора пользователя --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const newMessageUser = document.getElementById('new_message_user');
    const viewUser = document.getElementById('view_user');
    const formUserId = document.getElementById('form_user_id');

    // При выборе пользователя для отправки
    newMessageUser.addEventListener('change', function() {
        formUserId.value = this.value;
    });

    // При выборе пользователя для просмотра переписки
    viewUser.addEventListener('change', function() {
        const userId = this.value;
        console.log('Load all threads for user ID:', userId);

        // TODO: AJAX: подгрузить переписки выбранного пользователя
        // и отобразить их в #threads_container
    });

    // Клик по существующей переписке
    document.querySelectorAll('[data-thread-id]').forEach(item => {
        item.addEventListener('click', function() {
            const threadId = this.dataset.threadId;
            console.log('Load messages for thread ID:', threadId);

            // TODO: AJAX: подгрузить сообщения для выбранного потока
            // и отобразить их в #chat_container
        });
    });

});
</script>
@endsection
