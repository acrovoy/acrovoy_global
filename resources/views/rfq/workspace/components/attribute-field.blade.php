@php

    $type = $attribute->type;

    $rfqStatus = $rfq->status;

    $isReadonly =
        $rfqStatus->isPublished() ||
        $rfqStatus->isClosed();

    $disabled = $isReadonly
        ? 'disabled'
        : '';

    $isRequired =
        $attribute->pivot->is_required
        ?? $attribute->is_required
        ?? false;

    $name = "attributes[{$attribute->id}]";


    /*
    |--------------------------------------------------------------------------
    | OLD / SAVED VALUE
    |--------------------------------------------------------------------------
    */

    $old = old("attributes.{$attribute->id}");

    $savedValue =
        $attribute->saved_value
        ?? null;

    $savedOptions =
        $attribute->saved_options
        ?? [];


    if ($old === null) {
        $old = $savedValue;
    }

@endphp


<div
    class="
        group
        flex
        flex-col
        min-w-0
        {{ $isReadonly ? 'opacity-80' : '' }}
    "
>

    {{-- ================================================================
        LABEL
    ================================================================= --}}

    <div
        class="
            flex
            items-center
            min-h-[24px]
            mb-1.5
        "
    >

        <label
            class="
                text-[13px]
                font-semibold
                tracking-[-0.01em]
                text-gray-800
            "
        >

            {{ $attribute->name }}

            @if($isRequired)

                <span class="text-red-500 ml-1">
                    *
                </span>

            @endif

        </label>


        {{-- REMOVE ATTRIBUTE --}}
        @if(!$isReadonly)

            <button
                type="button"
                data-url="{{ route('rfqs.custom-attributes.dettach', [$rfq, $attribute]) }}"
                class="
                    remove-attr
                    ml-auto
                    inline-flex
                    items-center
                    justify-center
                    w-7
                    h-7
                    rounded-md
                    text-xs
                    text-red-500
                    hover:bg-red-100
                    transition
                "
            >
                ✕
            </button>

        @endif

    </div>



    {{-- ================================================================
        TEXT
    ================================================================= --}}

    @if(
        $type === 'text' ||
        $type === 'string' ||
        !$type
    )

        <input
            type="text"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            {{ $disabled }}
            class="
                w-full
                h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                placeholder:text-gray-400
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
                {{ $isReadonly
                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                    : ''
                }}
            "
            @required($isRequired)
        >



    {{-- ================================================================
        NUMBER
    ================================================================= --}}

    @elseif($type === 'number')

    @php
        $unitSymbol = $attribute->unit?->symbol
            ?? $attribute->unit?->name
            ?? null;
    @endphp

    <div class="relative">

        <input
            type="number"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            {{ $disabled }}
            class="
                w-full
                h-11
                px-3.5
                {{ $unitSymbol ? 'pr-16' : '' }}
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
                {{ $isReadonly
                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                    : ''
                }}
            "
            @required($isRequired)
        >

        @if($unitSymbol)

            <span
                class="
                    absolute
                    right-3
                    top-1/2
                    -translate-y-1/2
                    text-xs
                    font-medium
                    text-gray-400
                    pointer-events-none
                "
            >
                {{ $unitSymbol }}
            </span>

        @endif

    </div>



    {{-- ================================================================
        DECIMAL
    ================================================================= --}}

    @elseif($type === 'decimal')

        <input
            type="number"
            step="0.01"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            {{ $disabled }}
            class="
                w-full
                h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
                {{ $isReadonly
                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                    : ''
                }}
            "
            @required($isRequired)
        >



    {{-- ================================================================
        MEASUREMENT
    ================================================================= --}}

    @elseif($type === 'measurement')

    @php
        $selectedUnitId =
            old("attributes.{$attribute->id}.unit_id")
            ?? $attribute->saved_unit_id
            ?? $attribute->unit_id;

        $unitGroup = $attribute->unit?->unit_group;

        $measurementUnits = $units
            ->where('unit_group', $unitGroup);
    @endphp

    <div class="flex gap-2">

        <input
            type="number"
            step="any"
            name="{{ $name }}[value]"
            value="{{ old("attributes.{$attribute->id}.value", $savedValue) }}"
            {{ $disabled }}
            class="
                flex-1
                min-w-0
                h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
                {{ $isReadonly
                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                    : ''
                }}
            "
            @required($isRequired)
        >

        <select
            name="{{ $name }}[unit_id]"
            {{ $disabled }}
            class="
                w-36
                h-11
                px-3
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-700
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
                {{ $isReadonly
                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                    : ''
                }}
            "
        >

            @foreach($measurementUnits as $unit)

                <option
                    value="{{ $unit->id }}"
                    @selected(
                        (string) $selectedUnitId === (string) $unit->id
                    )
                >
                    {{ $unit->name }}

                    @if($unit->symbol)
                        ({{ $unit->symbol }})
                    @endif
                </option>

            @endforeach

        </select>

    </div>

    {{-- ================================================================
        BOOLEAN
    ================================================================= --}}

    @elseif($type === 'boolean')

        <label
            class="
                inline-flex
                items-center
                gap-3
                min-h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                cursor-pointer
                transition
                hover:bg-white
                {{ $isReadonly
                    ? 'cursor-not-allowed'
                    : ''
                }}
            "
        >

            <input
                type="hidden"
                name="{{ $name }}"
                value="0"
            >

            <input
                type="checkbox"
                name="{{ $name }}"
                value="1"
                @checked(old($name, $savedValue))
                {{ $disabled }}
                class="
                    w-4
                    h-4
                    rounded
                    border-gray-300
                    text-gray-900
                    focus:ring-gray-400
                "
            >

            <span
                class="
                    text-sm
                    font-medium
                    text-gray-700
                "
            >
                Yes
            </span>

        </label>



    {{-- ================================================================
        SELECT
    ================================================================= --}}

    @elseif($type === 'select')

        @if($attribute->options->count() <= 6)

    <div
        class="
            rounded-lg
            border
            border-gray-200
            bg-gray-50
            max-h-60
            overflow-y-auto
        "
    >

        <div class="p-2 pb-3">

            @foreach(
                $attribute->options->sortBy('sort_order')
                as $option
            )

                <label
                    class="
                        flex
                        items-center
                        gap-3
                        px-3
                        py-2.5
                        rounded-md
                        cursor-pointer
                        transition
                        hover:bg-white
                        text-sm
                        text-gray-700
                        {{ $isReadonly
                            ? 'cursor-not-allowed'
                            : ''
                        }}
                    "
                >

                    <input
                        type="radio"
                        name="{{ $name }}"
                        value="{{ $option->id }}"
                        @checked(
                            old($name, $savedValue)
                            == $option->id
                        )
                        {{ $disabled }}
                        class="
                            w-4
                            h-4
                            rounded-full
                            border-gray-300
                            text-gray-900
                            focus:ring-gray-400
                        "
                        @required($isRequired)
                    >

                    <span>
                        {{ $option->translatedValue() }}
                    </span>

                </label>

            @endforeach

        </div>

    </div>

@else

            <select
                name="{{ $name }}"
                {{ $disabled }}
                class="
                    w-full
                    h-11
                    px-3.5
                    rounded-lg
                    border
                    border-gray-200
                    bg-gray-50
                    text-sm
                    text-gray-900
                    outline-none
                    transition
                    focus:bg-white
                    focus:border-gray-400
                    focus:ring-2
                    focus:ring-gray-100
                    {{ $isReadonly
                        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                        : ''
                    }}
                "
                @required($isRequired)
            >

                <option value="">
                    Select option
                </option>

                @foreach(
                    $attribute->options->sortBy('sort_order')
                    as $option
                )

                    <option
                        value="{{ $option->id }}"
                        @selected(
                            old($name, $savedValue)
                            == $option->id
                        )
                    >
                        {{ $option->translatedValue() }}
                    </option>

                @endforeach

            </select>

        @endif



    {{-- ================================================================
        MULTISELECT
    ================================================================= --}}

    @elseif($type === 'multiselect')

        @php

            $oldValues = old($name);

            if ($oldValues === null) {
                $oldValues = $savedOptions;
            }

            $oldValues = is_array($oldValues)
                ? $oldValues
                : [];

            $oldValues = array_map(
                'strval',
                $oldValues
            );

        @endphp


        <input
            type="hidden"
            name="{{ $name }}"
            value=""
        >


        <div
            class="
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                max-h-60
                overflow-y-auto
            "
        >

            <div class="p-2 pb-3">

                @foreach(
                    $attribute->options->sortBy('sort_order')
                    as $option
                )

                    <label
                        class="
                            flex
                            items-center
                            gap-3
                            px-3
                            py-2.5
                            rounded-md
                            cursor-pointer
                            transition
                            hover:bg-white
                            text-sm
                            text-gray-700
                        "
                    >

                        <input
                            type="checkbox"
                            name="{{ $name }}[]"
                            value="{{ $option->id }}"
                            @checked(
                                in_array(
                                    (string) $option->id,
                                    $oldValues,
                                    true
                                )
                            )
                            {{ $disabled }}
                            class="
                                w-4
                                h-4
                                rounded
                                border-gray-300
                                text-gray-900
                                focus:ring-gray-400
                            "
                        >

                        <span>
                            {{ $option->translatedValue() }}
                        </span>

                    </label>

                @endforeach

            </div>

        </div>



    {{-- ================================================================
        DATE
    ================================================================= --}}

    @elseif($type === 'date')

        <input
            type="date"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            {{ $disabled }}
            class="
                w-full
                h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
            "
            @required($isRequired)
        >



    {{-- ================================================================
        FILE
    ================================================================= --}}

    @elseif($type === 'file')

        <input
            type="file"
            name="{{ $name }}"
            {{ $disabled }}
            class="
                w-full
                text-sm
                text-gray-700
            "
        >



    {{-- ================================================================
        FALLBACK
    ================================================================= --}}

    @else

        <input
            type="text"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            {{ $disabled }}
            class="
                w-full
                h-11
                px-3.5
                rounded-lg
                border
                border-gray-200
                bg-gray-50
                text-sm
                text-gray-900
                outline-none
                transition
                focus:bg-white
                focus:border-gray-400
                focus:ring-2
                focus:ring-gray-100
            "
            @required($isRequired)
        >

    @endif



    {{-- ================================================================
        REQUIRED ERROR
    ================================================================= --}}

    @if($isRequired)

        <div
            data-required-error="{{ $attribute->id }}"
            class="
                hidden
                mt-1.5
                text-[11px]
                text-red-500
            "
        >
            This field is required for this category
        </div>

    @endif

</div>