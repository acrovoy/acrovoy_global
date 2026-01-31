@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-1">Message Center</h2>
<p class="text-sm text-gray-500 mb-6">
    Secure messaging for discussing products, contracts, and collaboration details with suppliers.
</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="height: calc(60vh);">

    {{-- LEFT: Threads --}}
    <div class="lg:col-span-1 border rounded-lg overflow-y-auto" style="height: 100%;">
        @include('dashboard.buyer.partials.message-list', ['threads' => $threads])
    </div>

    {{-- RIGHT: Messages + Form --}}
    <div class="lg:col-span-2 border rounded-lg flex flex-col overflow-y-auto" style="height: 100%;">

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <div id="chatMessages">

                @php
                $product = $activeThread?->products->first();
                @endphp
                @if($product)
                <div class="p-4">
                    @include('dashboard.buyer.partials.product-card', ['product' => $product])
                </div>
                @endif


                @include('dashboard.buyer.partials.message-thread', ['messages' => $messages])
            </div>
        </div>

        {{-- Form --}}
        <div class="border-t p-4">
            @include('dashboard.buyer.partials.message-form', [
            'thread_id' => $activeThread ? $activeThread->id : null
            ])
        </div>

    </div>

</div>

<script>
    let lastMessageId = null;

    // ---------- UI helpers ----------
    const addMessageElem = (message, chatMessagesElem) => {
        const date = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        const buyerMsg = `
        <div class="flex flex-col items-end">
            <div class="bg-blue-900 text-white rounded-lg p-3 max-w-[75%] break-words break-all">
                <p class="text-sm m-0">${message.text}</p>
            </div>
             <span class="text-sm text-gray-600 mt-1">
                 ${message.user?.name ?? 'Unknown User' } ${message.user?.last_name ?? 'Unknown User' }
            </span>
            <span class="text-xs text-gray-300 mt-1">${date}</span>
        </div>`;

        const sellerMsg = `
        <div class="flex flex-col">
            <div class="bg-gray-100 rounded-lg p-3 max-w-[75%] break-words break-all">
                <p class="text-sm m-0">${message.text}</p>
            </div>
             <span class="text-sm text-gray-600 mt-1">
                 ${message.user?.name ?? 'Supplier' }
            </span>
            <span class="text-xs text-gray-400 mt-1">${date}</span>
        </div>`;

        chatMessagesElem.insertAdjacentHTML(
            'beforeend',
            message.role === 'buyer' ? buyerMsg : sellerMsg
        );

        chatMessagesElem.scrollTop = chatMessagesElem.scrollHeight;
    };

    // ---------- Load thread ----------
    const loadThread = async (threadId) => {
        const chatMessagesElem = document.querySelector('.lg\\:col-span-2 .flex-1 #chatMessages .chatMessages_list');
        const activeThreadInput = document.getElementById('activeThreadId');

        const res = await fetch(`/dashboard/messages/${threadId}`);
        if (!res.ok) return;

        const data = await res.json();

        // Очистка блока сообщений
        chatMessagesElem.innerHTML = '';

        // Добавляем новые сообщения
        data.messages.forEach(msg => addMessageElem(msg, chatMessagesElem));

        // Обновляем lastMessageId
        lastMessageId = data.messages.at(-1)?.id ?? null;

        // Обновляем hidden input формы
        if (activeThreadInput) activeThreadInput.value = threadId;
    };

    // ---------- Poll ----------
    const pollMessages = async () => {
        const chatMessagesElem = document.querySelector('.lg\\:col-span-2 .flex-1 #chatMessages .chatMessages_list');
        const activeThreadInput = document.getElementById('activeThreadId');
        const activeThreadId = activeThreadInput?.value;
        if (!activeThreadId || !lastMessageId) return;

        const res = await fetch(`/dashboard/messages/${activeThreadId}/poll?lastMessage=${lastMessageId}`);
        if (!res.ok) return;

        const data = await res.json();
        if (data.messages?.length) {
            lastMessageId = data.messages.at(-1).id;
            data.messages.forEach(msg => addMessageElem(msg, chatMessagesElem));
        }
    };

    setInterval(pollMessages, 3000);

    // ---------- Send ----------
    document.getElementById('chatForm')?.addEventListener('submit', async e => {
        e.preventDefault();

        const input = e.target.querySelector('input[name="text"]');
        const activeThreadInput = document.getElementById('activeThreadId');
        const chatMessagesElem = document.querySelector('#chatMessages .chatMessages_list');

        const text = input.value.trim();
        const activeThreadId = activeThreadInput?.value;
        if (!text || !activeThreadId) return;

        const res = await fetch(`/dashboard/messages/${activeThreadId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                text
            })
        });

        if (res.ok) {
            const msg = await res.json();
            lastMessageId = msg.id;
            addMessageElem(msg, chatMessagesElem);
            chatMessagesElem.parentElement.scrollTop = chatMessagesElem.scrollHeight;
            input.value = '';
        } else {
            alert('Failed to send message.');
        }
    });

    // ---------- Click thread ----------
    document.querySelectorAll('[data-thread-id]').forEach(li => {
        li.addEventListener('click', async () => {
            window.location = '/dashboard/buyer/messages?thread_id=' + li.dataset.threadId;
            // await loadThread(li.dataset.threadId);

            // // визуально выделяем активный тред
            // document.querySelectorAll('[data-thread-id]').forEach(item => item.classList.remove('bg-indigo-100'));
            // li.classList.add('bg-indigo-100');
        });
    });
</script>
@endsection