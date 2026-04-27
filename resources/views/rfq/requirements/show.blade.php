<div class="space-y-3">

    @forelse($rfq->requirements as $req)

        <div class="border rounded-lg p-3 bg-gray-50">

            <div class="text-sm font-medium">
                {{ $req->attribute?->name ?? 'Unknown attribute' }}
            </div>

            <div class="text-sm text-gray-600">
                {{ $req->value_text ?? $req->value_number ?? $req->value_boolean }}
            </div>

        </div>

    @empty

        <div class="text-sm text-gray-400">
            No requirements defined
        </div>

    @endforelse

</div>