{{-- resources/views/rfq/workspace/components/supplier-offer.blade.php --}}

@php

$attribute = $value->attribute;
$type = $attribute->type;

$item = $itemsByAttribute[$attribute->id] ?? null;







/*
|--------------------------------------------------------------------------
| BUYER ORIGINAL REQUIREMENT
|--------------------------------------------------------------------------
*/

$buyerValue = $value->value_text
?? $value->value_number
?? $value->value_date
?? null;

$buyerSelectedOptionId =
$value->attribute_option_id
?? null;

$buyerOptions = $value->options ?? collect();

$offerBuyerNotes = $item?->notes ?? '';

$offerBuyerPrice = $item?->unit_price ?? '';



$isCounter  = $item?->version?->is_counter ?? false;

$supplierItem = $supplierOfferVersionToCounter?->items
?->firstWhere('attribute_id', $attribute->id);


/*
|--------------------------------------------------------------------------
| SUPPLIER OFFER
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| SUPPLIER OFFER DATA
|--------------------------------------------------------------------------
*/
if ($isCounter) {

    $offerNotes = $supplierItem?->notes ?? '';
    $offerPrice = $supplierItem?->unit_price ?? '';

    $offerSelectedOptionId = $supplierItem?->options?->first()?->id;

    $offerSelectedOptions = $supplierItem?->options?->pluck('id')->toArray() ?? [];

    $supplierOfferDate = $supplierOfferVersion?->created_at;

} else {

    $offerNotes = $item?->notes ?? '';
    $offerPrice = $item?->unit_price ?? '';

    $offerSelectedOptionId = $item?->options?->first()?->id;

    $offerSelectedOptions = $item?->options?->pluck('id')->toArray() ?? [];

    $supplierOfferDate = $supplierOfferVersion?->created_at;
}



// dd($supplierOfferDate, $counterDate = $item?->version?->id);
@endphp



<div class="p-3 border border-gray-100 rounded-lg bg-gray-50 mb-3">

    {{-- ATTRIBUTE TITLE --}}
    <div class="mb-3">

        <div class="text-sm font-medium text-gray-900">
            {{ $attribute->name }}
        </div>

        {{-- BUYER VALUE --}}
        @if($buyerValue)

        <div class="text-xs text-gray-500 mt-1">
            Buyer initial requirement:
            <span class="text-gray-700">
                {{ $buyerValue }}
            </span>
        </div>

        @endif

        {{-- BUYER SELECT --}}
        @if($type === 'select' && $buyerSelectedOptionId)

        <div class="text-xs text-gray-500 mt-1">
            Buyer initial requirement:
            <span class="text-gray-700">
                {{ $attribute->options
                        ->firstWhere('id', $buyerSelectedOptionId)
                        ?->translatedValue() }}
            </span>
        </div>

        @endif

        {{-- BUYER MULTISELECT --}}
        @if($type === 'multiselect' && $buyerOptions->isNotEmpty())

        <div class="text-xs text-gray-500 mt-1">
            Buyer initial requirement:
            <span class="text-gray-700">
                {{ $buyerOptions
                        ->map(fn ($o) => $o->translatedValue())
                        ->implode(', ') }}
            </span>
        </div>

        @endif

    </div>


    {{-- SUPPLIER NOTES --}}
    <div class="mb-3">

        <div class="text-xs text-gray-500 mb-1">
            Supplier offer
        </div>

        <div class="w-full border border-gray-200 rounded p-2 {{ $isReadonly ? 'bg-gray-50 text-gray-600' : 'bg-white text-gray-800' }} text-sm min-h-[44px]">

            @if($offerNotes)
            {{ $offerNotes }}
            @else
            <span class="text-gray-400">
                No notes provided
            </span>
            @endif

        </div>

        @if($isCounter )
        <div class="text-xs text-yellow-600 mb-1 mt-3">
            Your notes
        </div>

        <div class="w-full border border-yellow-200 rounded p-2 {{ $isReadonly ? 'bg-yellow-50 text-gray-600' : 'bg-white text-yellow-800' }} text-sm min-h-[44px]">

            @if($offerBuyerNotes)
            {{ $offerBuyerNotes }}
            @else
            <span class="text-gray-400">
                No notes provided
            </span>
            @endif

        </div>
        @endif



    </div>


    {{-- SELECT --}}
    @if($type === 'select')

    <div class="space-y-2 mb-3">

        @foreach($attribute->options as $option)

        <label class="flex items-center gap-2 text-sm text-gray-700">

            <input
                type="radio"
                disabled
                @checked((int)$offerSelectedOptionId===(int)$option->id)
            class="{{ $isReadonly ? 'text-gray-500' : 'text-gray-900' }} focus:ring-gray-900"
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
                disabled
                @checked(in_array($option->id, $offerSelectedOptions))
            class="rounded focus:ring-gray-900
            {{ $isReadonly ? 'text-gray-500' : 'text-gray-900' }}"
            >

            <span>{{ $option->translatedValue() }}</span>

        </label>

        @endforeach

    </div>

    @endif


    {{-- FOOTER --}}
    <div class="flex items-center justify-between mt-4">

        {{-- ATTACHMENTS --}}
        <div class="text-xs text-gray-500">
            Attachments submitted
        </div>
        <div>
            {{-- PRICE --}}
            <div class="border border-gray-200 rounded px-3 py-1 {{ $isReadonly ? 'bg-gray-50 text-gray-600' : 'bg-white text-gray-800' }} text-sm  min-w-[90px] text-right">

                @if($offerPrice)

                ${{ number_format((float)$offerPrice, 2) }}

                @else

                <span class="text-gray-400">
                    —
                </span>

                @endif

            </div>
            @if($isCounter  && $offerBuyerPrice)
           
            <div class="text-xs text-yellow-600 mb-1 mt-1">
                Your proposed price
            </div>

            <div class=" border border-yellow-200 rounded px-3 py-1 {{ $isReadonly ? 'bg-yellow-50 text-yellow-600' : 'bg-white text-yellow-800' }} text-sm  min-w-[90px] text-right">



                ${{ number_format((float)$offerBuyerPrice, 2) }}





            </div>

           
            @endif

        </div>



    </div>

</div>