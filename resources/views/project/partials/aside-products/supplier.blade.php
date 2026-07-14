<div class="mt-1">

@php
    use App\Facades\ActiveContext;

    $supplierId = ActiveContext::supplierId();
@endphp

@foreach($project->rfqs as $index => $rfq)
<div class="mt-1">
    @php
        $offer = $rfq->offers
            ->where('participant_type', \App\Models\Supplier::class)
            ->where('participant_id', $supplierId)
            ->sortByDesc('id')
            ->first();

        $status = $offer?->status;
    @endphp

    <a
        href="{{ route('supplier.projects.rfq.requirements', $rfq) }}"
        class="group flex items-center gap-3 px-3 py-2.5 rounded-md border
               transition-all duration-200
               hover:bg-black/5
               @if($status === 'rejected')
                    border-red-300 bg-red-50
               @elseif($status === 'accepted')
                    border-green-300 bg-green-50
               @else
                    border-gray-200
               @endif">

        {{-- INDEX --}}
        <div class="text-[11px] text-gray-400 w-4 text-right">
            {{ $index + 1 }}.
        </div>

        {{-- IMAGE --}}
        <img
            src="{{ $rfq->image ?? asset('images/no-photo.png') }}"
            class="w-10 h-10 rounded-md object-cover border border-gray-200">

        {{-- CONTENT --}}
        <div class="flex-1 min-w-0">

            <div class="text-sm text-gray-800 truncate">
                {{ $rfq->title }}
            </div>

            <div class="text-[11px] text-gray-400">
                {{ $rfq->public_id }}
            </div>

        </div>

        {{-- ARROW --}}
        <div class="text-gray-300 group-hover:text-gray-500 transition">
            →
        </div>

    </a>
</div>
@endforeach

@if($project->rfqs->isEmpty())

    <div class="px-3 py-4 text-xs text-gray-400">
        No procurement items.
    </div>

@endif

</div>