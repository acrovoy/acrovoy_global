

<x-alerts />


{{-- RFQ PARTICIPANTS WORKSPACE --}}

<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">

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


    <div class="bg-white p-4 mb-3">

        <div class="font-semibold mb-3">
            Visibility
        </div>

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
                    🚀 Open RFQ (future public)
                </option>

            </select>

        </form>

    </div>


   @include('rfq.workspace.components.participants-invite-panel', [
    'rfq' => $rfq,
    'suppliers' => $suppliers,
    'visibility' => $rfq->visibility_type->value,
])

    {{-- LIST --}}
    <div class="space-y-3 mt-4">

    @forelse($participants as $participant)

        <div class="group flex items-center justify-between p-4 border border-gray-100 rounded-lg bg-white hover:border-gray-200 transition">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                    {{ strtoupper(substr($participant->participant?->name ?? 'S', 0, 1)) }}
                </div>

                <div>

                    <div class="text-sm font-medium text-gray-900">
                        {{ $participant->participant?->name ?? 'Unknown supplier' }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Invited {{ optional($participant->invited_at)->format('d M Y H:i') ?? '—' }}
                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-3">

    <span class="{{ $participant->status->badge() }}">
    {{ $participant->status->label() }}
</span>

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