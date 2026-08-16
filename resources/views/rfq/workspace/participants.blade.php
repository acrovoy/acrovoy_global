@php
$isClosed = $rfq->status->isClosed();
@endphp

{{-- BACK --}}
<a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
    class="text-sm text-gray-500 hover:text-gray-900 transition">

    ← Back to RFQ Overview
</a>


<x-alerts />


{{-- RFQ PARTICIPANTS WORKSPACE --}}

<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 mt-4">

    {{-- HEADER --}}
    <div class="">


        <div class="text-sm text-gray-500">
            RFQ Participants
        </div>

        <div class="text-lg font-semibold text-gray-900">
            Manage suppliers invited to this RFQ
        </div>


        <div class="text-xs text-gray-500 mt-1">
            Invite suppliers and track their participation status
        </div>

    </div>

    @if($isClosed)

    <div class="mt-4 mb-4 p-3 rounded-lg border border-red-200 bg-red-50">

        <div class="text-sm font-medium text-red-700">
            RFQ Closed
        </div>

        <div class="text-xs text-red-600 mt-1">
            Participants, visibility settings and invitations are locked.
        </div>

    </div>

    @endif


    <div class="bg-white p-4 mb-3">

        <div class="font-semibold mb-3 flex items-center gap-1.5">
            Visibility

            <x-help-tooltip width="w-96">
                <div class="space-y-3 leading-relaxed">
                    <div class="font-semibold text-white">
                        RFQ Visibility
                    </div>
                    <div class="text-gray-200 text-sm">
                        Выберите, кто сможет увидеть ваш RFQ и отправить на него предложение.
                        Чем шире видимость, тем больше потенциальных поставщиков сможет откликнуться.
                    </div>
                    <ul class="text-gray-300 text-xs space-y-2">
                        <li>
                            <span class="text-white font-medium">🔒 Private</span>
                            — RFQ увидят только поставщики, которых вы пригласите.
                            Подходит для работы с конкретными или проверенными поставщиками.
                        </li>
                        <li>
                            <span class="text-white font-medium">🧭 Category</span>
                            — RFQ будет доступен поставщикам, работающим в выбранной категории.
                            Это поможет получить предложения от подходящих специалистов.
                        </li>
                        <li>
                            <span class="text-white font-medium">🌐 Platform</span>
                            — RFQ смогут увидеть все зарегистрированные поставщики платформы.
                            Подходит, если вы хотите получить больше предложений и сравнить поставщиков.
                        </li>
                        <li>
                            <span class="text-white font-medium">🚀 Open</span>
                            — RFQ станет публичным и сможет отображаться в открытом разделе RFQ.
                            Его смогут увидеть даже незарегистрированные посетители сайта.
                            Для отправки предложения поставщику потребуется зарегистрироваться.
                        </li>
                    </ul>
                    <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                        Совет:
                        <span class="text-gray-200">
                            Используйте Private для конкретных поставщиков,
                            Category для поиска профильных поставщиков,
                            Platform для максимального охвата внутри Acrovoy,
                            а Open — если хотите привлечь новых поставщиков через публичный сайт.
                        </span>
                    </div>
                </div>
            </x-help-tooltip>


        </div>
        @if(!$isClosed)
        <form method="POST"
            action="{{ route('buyer.rfq.visibility.update', $rfq) }}">

            @csrf
            @method('PATCH')

            <select name="visibility_type"
                onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">

                <option value="private"
                    {{ $rfq->visibility_type->value === 'private' ? 'selected' : '' }}>
                    🔒 Private (only invited suppliers)
                </option>

                <option value="category"
                    {{ $rfq->visibility_type->value === 'category' ? 'selected' : '' }}>
                    🧭 Category suppliers
                </option>

                <option value="platform"
                    {{ $rfq->visibility_type->value === 'platform' ? 'selected' : '' }}>
                    🌐 All platform suppliers
                </option>

                <option value="open"
                    {{ $rfq->visibility_type->value === 'open' ? 'selected' : '' }}>
                    🚀 Open RFQ (public)
                </option>

            </select>

        </form>
        @else

        <div class="w-full border border-gray-200 bg-gray-100 rounded px-3 py-2 text-sm text-gray-600">
            {{ $rfq->visibility_type->label() }}
        </div>

        @endif

    </div>

    @if(!$isClosed)
    @include('rfq.workspace.components.participants-invite-panel', [
    'rfq' => $rfq,
    'suppliers' => $allSuppliers,
    'visibility' => $rfq->visibility_type->value,
    'allSuppliers' => $allSuppliers,
    ])
    @else

    <div class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">

        <div class="text-sm font-medium text-gray-700">
            RFQ Closed
        </div>

        <div class="text-xs text-gray-500 mt-1">
            New suppliers can no longer be invited.
        </div>

    </div>

    @endif
    {{-- LIST --}}
<div class="space-y-3 mt-4">

    @forelse($participants as $participant)

        @php
            $supplier = $participant->participant;
        @endphp

        <div
            class="group flex items-center justify-between
                   p-4 border border-gray-100 rounded-lg
                   bg-white hover:border-gray-200 transition"
        >

            {{-- LEFT --}}
            <div class="flex items-center gap-3 min-w-0">

                {{-- LOGO --}}
                <div
                    class="w-10 h-10 shrink-0
                           rounded-lg overflow-hidden
                           border border-gray-200
                           bg-gray-50
                           flex items-center justify-center"
                >

                    @if($supplier?->logo?->cdn_url)

                        <img
                            src="{{ $supplier->logo->cdn_url }}"
                            alt="{{ $supplier->name }}"
                            class="w-full h-full object-cover"
                        >

                    @else

                        <span class="text-xs font-semibold text-gray-500">
                            {{ strtoupper(substr($supplier?->name ?? 'S', 0, 1)) }}
                        </span>

                    @endif

                </div>

                {{-- SUPPLIER INFO --}}
                <div class="min-w-0">

                    <div class="text-sm font-medium text-gray-900 truncate">
                        {{ $supplier?->name ?? 'Unknown supplier' }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Invited
                        {{ optional($participant->invited_at)->format('d M Y H:i') ?? '—' }}
                    </div>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="flex items-center gap-3 shrink-0">

                <span class="{{ $participant->status->badge() }}">
                    {{ $participant->status->label() }}
                </span>

                @if(!$isClosed)

                    <form
                        method="POST"
                        action="{{ route('buyer.rfq.participants.remove', [$rfq, $participant]) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="text-xs text-gray-400 hover:text-red-600"
                        >
                            Remove
                        </button>

                    </form>

                @endif

            </div>

        </div>

    @empty

        <div class="p-6 border border-dashed border-gray-200 rounded-lg text-center">

            <div class="text-sm text-gray-500">
                No suppliers invited yet
            </div>

        </div>

    @endforelse

</div>

</div>