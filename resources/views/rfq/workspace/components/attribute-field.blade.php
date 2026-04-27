@php
$type = $attribute->type;

$isRequired =
    $attribute->pivot->is_required
    ?? $attribute->is_required
    ?? false;

$name = "attributes[{$attribute->id}]";

/*
|--------------------------------------------------------------------------
| OLD VALUE FIX (ROBUST)
|--------------------------------------------------------------------------
| old() — только после redirect back
| saved_value / saved_options — из RFQ
|--------------------------------------------------------------------------
*/

$old = old("attributes.{$attribute->id}");

$savedValue = $attribute->saved_value ?? null;
$savedOptions = $attribute->saved_options ?? [];

if ($old === null) {
    $old = $savedValue;
}
@endphp


<div class="p-3 border border-gray-100 rounded-lg bg-gray-50 hover:bg-white transition">

    {{-- LABEL --}}
    <label class="block text-sm font-medium text-gray-800 mb-2">

        {{ $attribute->name }}

        @if($isRequired)
            <span class="text-red-500">*</span>
        @endif

    </label>


    {{-- TEXT --}}
    @if($type === 'text')

        <input
            type="text"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
            {{ $isRequired ? 'required' : '' }}
        >


    {{-- NUMBER --}}
    @elseif($type === 'number')

        <input
            type="number"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
            {{ $isRequired ? 'required' : '' }}
        >


    {{-- DECIMAL --}}
    @elseif($type === 'decimal')

        <input
            type="number"
            step="0.01"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
            {{ $isRequired ? 'required' : '' }}
        >


    {{-- BOOLEAN --}}
    @elseif($type === 'boolean')

        <label class="flex items-center gap-2 text-sm text-gray-700">

            <input
                type="checkbox"
                name="{{ $name }}"
                value="1"
                @checked(old($name, $savedValue))
                class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
            >

            Yes / No

        </label>


    {{-- SELECT --}}
    @elseif($type === 'select')

        @if($attribute->options->count() <= 6)

            <div class="space-y-2">

                @foreach($attribute->options as $option)

                    <label class="flex items-center gap-2 text-sm text-gray-700">

                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $option->id }}"
                            @checked(old($name, $savedValue) == $option->id)
                            class="text-gray-900 focus:ring-gray-900"
                            {{ $isRequired ? 'required' : '' }}
                        >

                        <span>{{ $option->translatedValue() }}</span>

                    </label>

                @endforeach

            </div>

        @else

            <select
                name="{{ $name }}"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                {{ $isRequired ? 'required' : '' }}
            >

                <option value="">Select option</option>

                @foreach($attribute->options as $option)

                    <option value="{{ $option->id }}"
                        @selected(old($name, $savedValue) == $option->id)
                    >
                        {{ $option->translatedValue() }}
                    </option>

                @endforeach

            </select>

        @endif


    {{-- MULTISELECT --}}
   @elseif($type === 'multiselect')

    @php
        $oldValues = old($name);

        if ($oldValues === null) {
            $oldValues = $savedOptions;
        }

        $oldValues = is_array($oldValues) ? $oldValues : [];
    @endphp

    {{-- 🔥 ВАЖНО: fallback чтобы Laravel получил ключ даже если ничего не выбрано --}}
    <input type="hidden" name="{{ $name }}" value="">

    <div class="space-y-2">

        @foreach($attribute->options as $option)

            <label class="flex items-center gap-2 text-sm text-gray-700">

                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $option->id }}"
                    @checked(in_array($option->id, $oldValues))
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                >

                <span>{{ $option->translatedValue() }}</span>

            </label>

        @endforeach

    </div>


    {{-- DATE --}}
    @elseif($type === 'date')

        <input
            type="date"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
        >


    {{-- FILE --}}
    @elseif($type === 'file')

        <input
            type="file"
            name="{{ $name }}"
            class="w-full text-sm text-gray-700"
        >


    {{-- FALLBACK --}}
    @else

        <input
            type="text"
            name="{{ $name }}"
            value="{{ old($name, $savedValue) }}"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
        >

    @endif


    {{-- UNIT --}}
    @if($attribute->unit)

        <div class="text-xs text-gray-400 mt-1">
            Unit: {{ $attribute->unit }}
        </div>

    @endif

</div>