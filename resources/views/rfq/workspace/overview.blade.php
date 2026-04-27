

<div class="grid grid-cols-3 gap-4">

    <div class="bg-white border rounded-xl p-4">
        <div class="text-sm text-gray-500">Requirements</div>
        <div class="text-2xl font-semibold">
            @foreach($rfq->attributeValues as $value)

    <div>
        <strong>{{ $value->attribute->name }}</strong>

        @if($value->attribute->type === 'select')
            {{ optional($value->option)->translatedValue() }}
        @elseif($value->attribute->type === 'multiselect')
            @foreach($value->options as $opt)
                {{ $opt->translatedValue() }},
            @endforeach
        @else
            {{ $value->value_text ?? $value->value_number ?? '—' }}
        @endif
    </div>

@endforeach
        </div>
    </div>

    <div class="bg-white border rounded-xl p-4">
        <div class="text-sm text-gray-500">Participants</div>
        <div class="text-2xl font-semibold">
            {{ $rfq->participants->count() }}
        </div>
    </div>

    <div class="bg-white border rounded-xl p-4">
        <div class="text-sm text-gray-500">Offers</div>
        <div class="text-2xl font-semibold">
            {{ $rfq->offers->count() }}
        </div>
    </div>

</div>



<div class="bg-white border rounded-xl p-4 mt-6">
    <div class="text-sm text-gray-500">Actions needed</div>
    <div class="text-sm text-gray-600">
        
    </div>
</div>

