@php
$type = $value->attribute->type;
$attribute = $value->attribute;

$counterItem  = $counterItemsByAttribute[$attribute->id] ?? null;



/*
|--------------------------------------------------------------------------
| SUPPLIER OFFER (READ ONLY)
|--------------------------------------------------------------------------
*/

$item = $itemsByAttribute[$attribute->id] ?? null;

$supplierNotes = $item?->notes;
$supplierPrice = $item?->unit_price;

$supplierSelectedOptionId =
    $item?->options?->first()?->id ?? null;

$supplierSelectedOptions =
    $item?->options?->pluck('id')->toArray() ?? [];

/*
|--------------------------------------------------------------------------
| BUYER REQUIREMENT (ORIGINAL RFQ)
|--------------------------------------------------------------------------
*/

$buyerValue =
    $value->value_text
    ?? $value->value_number
    ?? $value->value_date
    ?? null;

$buyerSelectedOptionId = $value->attribute_option_id ?? null;
$buyerOptions = $value->options ?? collect();

/*
|--------------------------------------------------------------------------
| BUYER COUNTER INPUT (VERSION BASED - FIXED)
|--------------------------------------------------------------------------
*/



$counterNotes = old(
    "{$attribute->id}.notes",
     $counterItem?->notes
);

$counterPrice = old(
    "{$attribute->id}.unit_price",
    $counterItem?->unit_price ?? $supplierPrice
);

$counterSelectedOptionId = old(
    "{$attribute->id}.option_id",
    $item?->options?->first()?->id?? $supplierSelectedOptionId
);

$counterSelectedOptions = old(
    "{$attribute->id}.option_ids",
    $item?->options?->pluck('id')->toArray() ?? $supplierSelectedOptions
);
@endphp


<div
    class="p-4 border border-gray-100 rounded-xl bg-white mb-3 hover:bg-gray-50 transition"
    data-attribute-id="{{ $attribute->id }}"
>

    {{-- ATTRIBUTE NAME --}}
    <div class="mb-3">

        <div class="text-sm font-semibold text-gray-900">
            {{ $attribute->name }}
        </div>

        {{-- BUYER REQUIREMENT --}}
        @if($buyerValue)
            <div class="text-xs text-gray-500 mt-1">
                Requirement:
                <span class="text-gray-700">{{ $buyerValue }}</span>
            </div>
        @endif

        @if($type === 'select' && $buyerSelectedOptionId)
            <div class="text-xs text-gray-500 mt-1">
                Requirement:
                <span class="text-gray-700">
                    {{ $attribute->options->firstWhere('id', $buyerSelectedOptionId)?->translatedValue() }}
                </span>
            </div>
        @endif

        @if($type === 'multiselect' && $buyerOptions->isNotEmpty())
            <div class="text-xs text-gray-500 mt-1">
                Requirement:
                <span class="text-gray-700">
                    {{ $buyerOptions->map(fn($o) => $o->translatedValue())->implode(', ') }}
                </span>
            </div>
        @endif

    </div>


    {{-- ========================================= --}}
    {{-- SUPPLIER OFFER (READ ONLY BLOCK) --}}
    {{-- ========================================= --}}

    <div class="mb-4 p-3 bg-gray-50 border border-gray-100 rounded-lg">

        <div class="text-xs text-gray-500 mb-2">
            Supplier offer
        </div>

        <div class="text-sm text-gray-800 mb-2 min-h-[20px]">
            {{ $supplierNotes ?? '—' }}
        </div>

        {{-- SELECT --}}
        @if($type === 'select')

            <div class="space-y-2 mb-4">

                @foreach($attribute->options as $option)

                    <label class="flex items-center gap-2 text-sm text-gray-700">

                        <input
                            type="radio"
                            name="option_id[{{ $attribute->id }}]"
                            value="{{ $option->id }}"
                            disabled
                            @checked((int)$counterSelectedOptionId === (int)$option->id)
                            class="text-gray-500 focus:ring-gray-500"
                        >

                        <span>{{ $option->translatedValue() }}</span>

                    </label>

                @endforeach

            </div>

        @endif


        {{-- MULTISELECT --}}
        @if($type === 'multiselect')

            <input type="hidden" name="option_ids[{{ $attribute->id }}]" value="">

            <div class="space-y-2 mb-4">

                @foreach($attribute->options as $option)

                    <label class="flex items-center gap-2 text-sm text-gray-700">

                        <input
                            type="checkbox"
                            name="option_ids[{{ $attribute->id }}][]"
                            value="{{ $option->id }}"
                            disabled
                            @checked(in_array($option->id, $counterSelectedOptions))
                            class="rounded text-gray-500 focus:ring-gray-500"
                        >

                        <span>{{ $option->translatedValue() }}</span>

                    </label>

                @endforeach

            </div>

        @endif

        <div class="text-xs text-gray-600">
            Price:
            <span class="font-medium text-gray-900">
                {{ $supplierPrice ? number_format($supplierPrice, 2) : '—' }}
            </span>
        </div>

    </div>


    {{-- ========================================= --}}
    {{-- BUYER COUNTER (EDITABLE) --}}
    {{-- ========================================= --}}

    <div class="mb-4">

        <div class="text-xs text-blue-600 mb-2">
            Your counter offer
        </div>

        <input
            type="text"
            name="notes[{{ $attribute->id }}]"
            value="{{ $counterNotes }}"
            class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-1 focus:ring-gray-900"
            placeholder="Add your counter notes..."
        >

    </div>


    {{-- PRICE --}}
    <div class="flex items-end justify-between mt-4">

        <div class="text-xs text-gray-500">
            Your proposed price
        </div>

        <div class="flex flex-col items-end">

            <div class="text-[11px] text-gray-400 mb-1">
                USD
            </div>

            <input
                type="number"
                step="0.01"
                name="unit_price[{{ $attribute->id }}]"
                value="{{ $counterPrice }}"
                class="w-40 border border-blue-200 rounded-lg px-3 py-2 text-sm text-right
                       focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                placeholder="0.00"
            >

        </div>

    </div>

</div>