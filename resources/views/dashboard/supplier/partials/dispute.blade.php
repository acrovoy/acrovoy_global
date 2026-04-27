{{-- Spory / Disputes для продавца --}}
<div class="border rounded-lg p-4 bg-gray-50" x-data="{ openModalId: null }">
    <h3 class="font-semibold mb-3 text-lg">Disputes</h3>

    @if(count($order['disputes']) > 0)
        <ul class="space-y-3">
            @foreach($order['disputes'] as $dispute)
                <li class="border rounded-lg p-4 bg-white flex flex-col md:flex-row justify-between gap-4">

                    {{-- Левая часть — информация --}}
                    <div class="space-y-1 text-sm">
                        <p>
                            <strong>Customer:</strong>
                            {{ $dispute->user->name ?? '—' }}
                        </p>

                        <p>
                            <strong>Reason:</strong>
                            {{ $dispute->reason }}
                        </p>

                        <p>
                            <strong>Requested action:</strong>
                            {{ ucfirst($dispute->action) }}
                        </p>

                        <p class="flex items-center gap-2">
                            <strong>Status:</strong>

                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                @if($dispute->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($dispute->status === 'supplier_offer') bg-blue-100 text-blue-800
                                @elseif($dispute->status === 'buyer_reject') bg-red-100 text-red-800
                                @elseif($dispute->status === 'resolved') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                            </span>
                        </p>

                        @if($dispute->attachment)
                            <p>
                                <a href="{{ asset('storage/' . $dispute->attachment) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:underline">
                                    View attachment
                                </a>
                            </p>
                        @endif

                        {{-- Показываем комментарий покупателя --}}
@if($dispute->buyer_comment)
    <div class="mt-2 p-3 bg-red-100 border-l-4 border-red-500 rounded text-sm">
        <strong>Comment from customer:</strong><br>
        {{ $dispute->buyer_comment }}
    </div>
@endif

{{-- Комментарий продавца --}}
@if($dispute->supplier_comment)
    <div class="mt-2 p-3 bg-blue-100 border-l-4 border-blue-500 rounded text-sm">
        <strong>Comment from supplier:</strong><br>
        {{ $dispute->supplier_comment }}
    </div>
@endif


{{-- Апелляция к администратору / Решение администратора --}}
@if($dispute->status === 'appealed' || $dispute->admin_comment)
    <div class="mt-2 p-3 bg-orange-100 border-l-4 border-orange-500 rounded text-sm">
        <strong>Admin decision:</strong><br>
        {{ $dispute->admin_comment ?? 'На рассмотрении администратора' }}
    </div>
@endif

                        


                    </div>

                   {{-- Правая часть — действия --}}
<div class="flex items-start">
    @if($dispute->status !== 'admin_review' && $dispute->status !== 'resolved' && $dispute->status !== 'cancelled')
        <button
            @click="openModalId = {{ $dispute->id }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
        >
            Manage dispute
        </button>
    @elseif($dispute->status === 'admin_review')
        <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs">
            Awaiting admin review
        </span>
    @endif
</div>

{{-- Modal для управления спором --}}
<div x-show="openModalId === {{ $dispute->id }}"
     x-cloak
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">

        <button @click="openModalId = null"
                class="absolute top-2 right-3 text-gray-400 hover:text-gray-700 text-xl">
            &times;
        </button>

        <h3 class="font-bold mb-4 text-lg">
            Manage dispute #{{ $dispute->id }}
        </h3>

        {{-- Блокируем форму, если спор в статусе appealed --}}
        <form action="{{ route('manufacturer.orders.dispute.update', [$order['id'], $dispute->id]) }}"
              method="POST"
              class="space-y-4 @if($dispute->status === 'appealed') opacity-50 pointer-events-none @endif">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">
                    Status (propose solution)
                </label>
                <select name="status"
                        class="w-full border rounded px-3 py-2 text-sm">
                    <option value="pending" @if($dispute->status === 'pending') selected @endif>
                        Pending
                    </option>
                    <option value="supplier_offer" @if($dispute->status === 'supplier_offer') selected @endif>
                        Propose solution
                    </option>
                    <option value="rejected" @if($dispute->status === 'rejected') selected @endif>
                        Rejected
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Comment (optional)
                </label>
                <textarea name="supplier_comment"
                          rows="3"
                          class="w-full border rounded px-3 py-2 text-sm"
                          placeholder="Add a comment for the customer">{{ $dispute->admin_comment }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                        @click="openModalId = null"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>


                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500 italic">
            No disputes for this order.
        </p>
    @endif
</div>