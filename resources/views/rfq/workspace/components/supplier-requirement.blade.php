@php
$attribute = $value->attribute;
$type = $attribute->type;

$item = $itemsByAttribute[$attribute->id] ?? null;


/**
 * =========================
 * BUYER SNAPSHOT LOGIC
 * =========================
 */
$buyerValue = $buyerSnapshotMap[$attribute->id] ?? null;

$buyerText = $buyerValue?->value_text
    ?? $buyerValue?->value_number
    ?? $buyerValue?->value_date
    ?? null;

$buyerOptions = $buyerValue?->options ?? collect();

$buyerSelectedOptionId =
    $value->attribute_option_id
    ?? $item?->options?->first()?->id;


/**
 * =========================
 * SUPPLIER DATA
 * =========================
 */

$selectedValue = $value->value_text
    ?? $value->value_number
    ?? $value->value_date
    ?? null;

$selectedOptionId = $item?->options?->first()?->id ?? null;

$selectedOptions = $item?->options?->pluck('id')->toArray() ?? [];

$notes = $item?->notes ?? '';
$price = $item?->unit_price ?? '';
@endphp




<div class="p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition mb-3">



    {{-- LABEL --}}
    <label class="block text-sm font-medium text-gray-800 mb-2">
       {{ $attribute->name }}: 

        {{-- BUYER SNAPSHOT --}}
    @if($buyerText)
        
            {{ $buyerText }}
      
    @endif

    {{-- SELECT / MULTISELECT SNAPSHOT --}}
    @if($type === 'select' && $buyerSelectedOptionId)

    <span class="text-xs text-gray-500 mt-1">
        
        {{ $attribute->options->firstWhere('id', $buyerSelectedOptionId)?->translatedValue() }}
    </span>

@endif

    @if($type === 'multiselect' && $buyerOptions->isNotEmpty())
        <span class="text-xs text-gray-500 mt-1">
           
            {{ $buyerOptions->map(fn ($o) => $o->translatedValue())->implode(', ') }}
        </span>
    @endif


    </label>


    <!-- {{-- BUYER REFERENCE --}}
    @if(in_array($type, ['text','number','decimal']))

        <div class="text-xs text-gray-500 mb-1">
            Requirement
        </div>

        <div class="w-full bg-white border rounded px-3 py-2 text-sm mb-3">
            {{ $selectedValue }}
        </div>

    @endif -->





    {{-- SUPPLIER --}}
    <div class="text-xs text-gray-500 mb-1">
        Your offer
    </div>

    <textarea
        name="offer[{{ $attribute->id }}][notes]"
        data-autosave
        data-requirement-id="{{ $attribute->id }}"
        data-field="notes"
        class="w-full border rounded p-2 mb-3 text-sm focus:outline-none focus:ring-0 focus:border-gray-900"
        placeholder="Notes"
    >{{ $notes }}</textarea>


    {{-- SELECT --}}
    @if($type === 'select')

        <div class="space-y-2 mb-3">

            @foreach($attribute->options as $option)

                <label class="flex items-center gap-2 text-sm text-gray-700">

                    <input
                        type="radio"
                        name="offer[{{ $attribute->id }}][value]"
                        value="{{ $option->id }}"
                        data-autosave
                        data-requirement-id="{{ $attribute->id }}"
                        data-field="select"
                        @checked((int)$selectedOptionId === (int)$option->id)
                        class="text-gray-900 focus:ring-gray-900"
                    >

                    <span>{{ $option->translatedValue() }}</span>

                </label>

            @endforeach

        </div>

    @endif


    {{-- MULTISELECT --}}
    @if($type === 'multiselect')

        <div class="space-y-2 mb-3">

            @foreach($attribute->options as $option)

                <label class="flex items-center gap-2 text-sm text-gray-700">

                    <input
                        type="checkbox"
                        name="offer[{{ $attribute->id }}][values][]"
                        value="{{ $option->id }}"
                        data-autosave
                        data-requirement-id="{{ $attribute->id }}"
                        data-field="multiselect"
                        @checked(in_array($option->id, $selectedOptions))
                        class="text-gray-900 rounded focus:ring-gray-900"
                    >

                    <span>{{ $option->translatedValue() }}</span>

                </label>

            @endforeach

        </div>

    @endif


    {{-- PRICE + FILE --}}
    <div class="flex items-center gap-3">

        

        <div class="w-12 h-12 border-dashed border rounded flex items-center justify-center text-gray-400">
            +
        </div>

        <input
            type="text"
            name="offer[{{ $attribute->id }}][price]"
            value="{{ $price }}"
            data-autosave
            data-requirement-id="{{ $attribute->id }}"
            data-field="price"
            class="ml-auto border rounded px-3 py-1 w-32 text-sm focus:outline-none focus:ring-0 focus:border-gray-900"
            placeholder="Price"
        >

    </div>

</div>