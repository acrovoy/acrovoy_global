{{-- =========================================================
    PRODUCT SPECIFICATIONS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">
            Product specifications
        </h2>
        <p class="mt-1 text-xs text-stone-500">
            Saved category attributes and their values
        </p>
    </div>

    @if ($product1->attributeValues->isNotEmpty())
    <table class="w-full table-fixed text-sm">
        <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
            <tr>
                <th class="w-[19%] break-words px-3 py-3 font-medium sm:px-5">Attribute group</th>
                <th class="w-[23%] break-words px-3 py-3 font-medium sm:px-5">Attribute</th>
                <th class="w-[14%] break-words px-3 py-3 font-medium sm:px-5">Type</th>
                <th class="w-[44%] break-words px-3 py-3 font-medium sm:px-5">Saved value</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-stone-100">
            @foreach ($product1->attributeValues->sortBy(fn ($value) => strtolower($value->attribute?->group?->name ?? 'General')) as $attributeValue)
            @php
            $attribute = $attributeValue->attribute;
            $displayValue = $attributeValue->display_value;

            if ($displayValue === null || $displayValue === '') {
            $valueTranslation = $attributeValue->translations->firstWhere('locale', 'en')
            ?? $attributeValue->translations->first();

            $displayValue = $valueTranslation?->value;
            }

            $unit = $attribute?->unit;

            if (is_string($unit)) {
            $decodedUnit = json_decode($unit, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedUnit)) {
            $unit = $decodedUnit;
            }
            }

            $attributeUnit = data_get($unit, 'symbol')
            ?: data_get($unit, 'code')
            ?: data_get($unit, 'translation.name')
            ?: (is_string($unit) ? $unit : null);


            $isMultiSelect = strtolower($attribute?->type ?? '') === 'multiselect';
            $displayOptions = collect();

            if ($isMultiSelect && filled($displayValue)) {
            $decodedOptions = json_decode($displayValue, true);

            $optionValues = is_array($decodedOptions)
            ? $decodedOptions
            : preg_split('/\s*,\s*/u', trim($displayValue));

            $displayOptions = collect($optionValues)
            ->flatten()
            ->map(fn ($option) => trim((string) $option))
            ->filter(fn ($option) => $option !== '')
            ->unique()
            ->values();
            }

            $isCustomAttribute = (bool) ($attribute?->is_custom ?? false);


            $isBoolean = strtolower($attribute?->type ?? '') === 'boolean';

if ($isBoolean) {
    $booleanValue = strtolower(trim((string) $displayValue));

    if (in_array($booleanValue, ['0', 'false', ''], true)) {
        $displayValue = null;
    } elseif (in_array($booleanValue, ['1', 'true'], true)) {
        $displayValue = 'Yes';
    }
}


            @endphp

            <tr class="align-top hover:bg-stone-50/70">
                <td class="break-words px-3 py-3 text-xs text-stone-500 sm:px-5 [overflow-wrap:anywhere]">
                    {{ $attribute?->group?->name ?? 'General' }}
                </td>

                <td class="break-words px-3 py-3 font-medium text-stone-800 sm:px-5 [overflow-wrap:anywhere]">
                    <div class="flex min-w-0 flex-wrap items-center gap-2">
                        <span>
                            {{ $attribute?->name ?? $attribute?->code ?? ('Attribute #' . $attributeValue->attribute_id) }}
                        </span>

                        @if ($isCustomAttribute)
                        <span class="inline-flex shrink-0 items-center rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-700">
                            Custom
                        </span>
                        @endif
                    </div>
                </td>

                <td class="break-words px-3 py-3 text-xs text-stone-500 sm:px-5 [overflow-wrap:anywhere]">
                    {{ ucfirst($attribute?->type ?? '—') }}
                </td>

                <td class="min-w-0 px-3 py-3 sm:px-5">
    @if ($isMultiSelect && $displayOptions->isNotEmpty())
        <div class="flex min-w-0 flex-wrap items-start gap-1.5">
            @foreach ($displayOptions as $option)
                <span class="inline-flex max-w-full min-w-0 items-center rounded-md border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium leading-5 text-sky-800">
                    <span class="whitespace-normal break-words [overflow-wrap:anywhere]">{{ $option }}</span>
                </span>
            @endforeach
        </div>
    @elseif (!$isMultiSelect && filled($displayValue))
        @if ($attributeUnit)
            <span class="inline-flex max-w-full items-baseline gap-1 whitespace-nowrap font-medium text-stone-900">
                <span>{{ $displayValue }}</span>
                <span class="shrink-0 text-xs font-normal text-stone-500">{{ $attributeUnit }}</span>
            </span>
        @else
            <span class="whitespace-pre-line break-words font-medium text-stone-900 [overflow-wrap:anywhere]">
                {{ $displayValue }}
            </span>
        @endif
    @else
        <span class="text-sm italic text-stone-400">
            Not set
        </span>
    @endif

    @if ($attributeValue->translations->count() > 1)
        <div class="mt-2 space-y-1">
            @foreach ($attributeValue->translations as $valueTranslation)
                @if (filled($valueTranslation->value))
                    <p class="break-words text-xs text-stone-500 [overflow-wrap:anywhere]">
                        <span class="font-semibold uppercase">{{ $valueTranslation->locale }}:</span>
                        {{ $valueTranslation->value }}
                    </p>
                @endif
            @endforeach
        </div>
    @endif
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="p-5 text-sm text-stone-500">
        No product specifications have been saved.
    </p>
    @endif
</section>