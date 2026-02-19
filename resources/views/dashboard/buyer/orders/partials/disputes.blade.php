{{-- Disputes --}}
@if($order->disputes->count())
<div class="mt-6 border border-gray-200 rounded-lg bg-gray-50">

    <h3 class="pt-6 pr-6 pl-6 font-semibold text-lg">Disputes</h3>

    <div class="p-4 divide-y divide-gray-200">
    @foreach($order->disputes as $dispute)
        <div class="p-4 bg-white rounded-lg my-2 shadow-sm">

            {{-- Status --}}
            <div class="flex justify-between items-center mb-3">
                <div class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    Статус:
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        @if($dispute->status === 'pending') bg-yellow-100 text-yellow-700
                        @elseif($dispute->status === 'supplier_offer') bg-blue-100 text-blue-700
                        @elseif($dispute->status === 'buyer_reject') bg-red-100 text-red-700
                        @elseif($dispute->status === 'rejected') bg-red-200 text-red-800
                        @elseif($dispute->status === 'resolved') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-600
                        @endif
                    ">
                        {{ __('dispute.status.' . $dispute->status) ?? ucfirst(str_replace('_', ' ', $dispute->status)) }}
                    </span>
                </div>

                <span class="text-xs text-gray-500">
                    {{ $dispute->created_at->format('d.M.y | H:i') }}
                </span>
            </div>

            {{-- Причина --}}
            <p class="text-sm text-gray-700 mb-1">
                <strong>Причина:</strong> {{ $dispute->reason }}
            </p>

            {{-- Запрос --}}
            <p class="text-sm text-gray-700 mb-2">
                <strong>Запрос:</strong>
                {{ __('dispute.action.' . $dispute->action) ?? ucfirst($dispute->action) }}
            </p>

            {{-- Ответ продавца --}}
            @if($dispute->supplier_comment)
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
                    <strong>Ответ продавца:</strong><br>
                    {{ $dispute->supplier_comment }}
                </div>
            @endif

            {{-- Комментарий покупателя --}}
            @if($dispute->buyer_comment)
                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded text-sm">
                    <strong>Комментарий покупателя:</strong><br>
                    {{ $dispute->buyer_comment }}
                </div>
            @endif

            {{-- Решение администратора --}}
            @if($dispute->admin_comment)
                <div class="mt-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded text-sm">
                    <strong>Решение администратора:</strong><br>
                    {{ $dispute->admin_comment }}
                </div>
            @endif

            {{-- Вложение --}}
            @if($dispute->attachment)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $dispute->attachment) }}"
                       target="_blank"
                       class="text-blue-600 hover:underline text-sm">
                        Посмотреть вложение
                    </a>
                </div>
            @endif

            {{-- КНОПКИ --}}
            <div class="mt-4 flex flex-wrap gap-3">

                {{-- pending --}}
                @if($dispute->status === 'pending')
                    <form method="POST" action="{{ route('buyer.disputes.cancel', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Отменить спор
                        </button>
                    </form>

                    <a href="{{ route('buyer.support.chat', $dispute->id) }}"
                       class="text-sm text-blue-600 hover:text-blue-800 underline">
                        Связаться с поддержкой
                    </a>
                @endif

                {{-- supplier_offer --}}
                @if($dispute->status === 'supplier_offer')
                    <form method="POST" action="{{ route('buyer.disputes.accept', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-green-600 hover:text-green-800 underline">
                            Принять решение
                        </button>
                    </form>

                    <form method="POST" action="{{ route('buyer.disputes.reject', $dispute->id) }}" class="flex flex-col gap-2 w-full md:w-auto">
                        @csrf
                        @method('PUT')

                        <textarea name="buyer_comment" rows="2"
                                  placeholder="Комментарий (необязательно)"
                                  class="border border-gray-300 rounded px-2 py-1 text-sm"></textarea>

                        <button type="submit"
                                class="text-sm text-red-600 hover:text-red-800 underline self-start">
                            Отклонить решение
                        </button>
                    </form>
                @endif

                {{-- rejected --}}
                @if($dispute->status === 'rejected')
                    <form method="POST" action="{{ route('buyer.disputes.appeal', $dispute->id) }}" class="flex flex-col gap-2 w-full md:w-auto">
                        @csrf
                        @method('PUT')

                        <textarea name="buyer_comment" rows="2"
                                  placeholder="Комментарий к апелляции (необязательно)"
                                  class="border border-gray-300 rounded px-2 py-1 text-sm"></textarea>

                        <button type="submit"
                                class="text-sm text-blue-600 hover:text-blue-800 underline self-start">
                            Подать апелляцию
                        </button>
                    </form>

                    <form method="POST" action="{{ route('buyer.disputes.close', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-green-600 hover:text-green-800 underline">
                            Закрыть спор
                        </button>
                    </form>
                @endif

            </div>

        </div>
    @endforeach
    </div>

</div>
@endif