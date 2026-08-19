@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-3xl">

    <x-alerts />


   {{-- ============================================================
     HEADER
============================================================ --}}

<div class="flex items-start justify-between gap-4 mb-6">

    <div>

        <div class="flex items-center gap-3">

            <h2 class="text-2xl font-semibold text-gray-900">
                Edit Attribute
            </h2>

            {{-- ATTRIBUTE TYPE --}}
            <span
                class="
                    inline-flex
                    items-center
                    px-2.5
                    py-1
                    rounded-md
                    text-xs
                    font-medium
                    border
                    {{ $attribute->is_custom
                        ? 'bg-blue-50 text-blue-700 border-blue-200'
                        : 'bg-gray-50 text-gray-600 border-gray-200'
                    }}
                "
            >
                {{ $attribute->is_custom ? 'Custom' : 'System' }}
            </span>

        </div>


        {{-- ATTRIBUTE CODE --}}
        <div class="flex items-center gap-2 mt-1.5">

            <span class="text-sm text-gray-500">
                Attribute:
            </span>

            <code
                class="
                    px-2
                    py-0.5
                    rounded
                    bg-gray-100
                    border border-gray-200
                    text-xs
                    font-mono
                    text-gray-700
                "
            >
                {{ $attribute->code }}
            </code>

        </div>


        <p class="text-sm text-gray-500 mt-2">
            Edit attribute configuration, translations and display settings.
        </p>

    </div>

</div>



    {{-- Form Card --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form
            action="{{ route('admin.settings.attributes.update',$attribute->id) }}"
            method="POST"
            class="p-6 flex flex-col gap-6">

            @csrf
            @method('PUT')


            @php

            use App\Models\Language;

            $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

            $translations = $attribute
            ->translations
            ->pluck('name','locale');

            @endphp


           {{-- ============================================================
     ENTITY TYPE
============================================================ --}}

<div>

    <label
        for="entity_type"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Entity Type
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Defines which type of entity this attribute belongs to.
    </p>

    <select
        name="entity_type"
        id="entity_type"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
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
    >

        <option value="">
            Select entity type
        </option>

        <option
            value="product"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'product')
        >
            Product
        </option>

        <option
            value="rfq"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'rfq')
        >
            RFQ
        </option>

        <option
            value="offer"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'offer')
        >
            Offer
        </option>

        <option
            value="contract"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'contract')
        >
            Contract
        </option>

        <option
            value="company"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'company')
        >
            Company
        </option>

        <option
            value="user"
            @selected(old('entity_type', $attribute->entity_type ?? '') === 'user')
        >
            User
        </option>

    </select>

    @error('entity_type')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>

            {{-- Context --}}

            <div>

                <label class="block font-medium mb-1">
                    Context OLD Type
                </label>

                <input
                    type="text"
                    name="context"
                    value="{{ old('context',$attribute->context) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                    required>

            </div>



            {{-- ============================================================
    ATTRIBUTE GROUP
============================================================ --}}

<div>

    <label
        for="group_id"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Attribute Group
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Select the group this attribute belongs to.
    </p>

    <select
        name="group_id"
        id="group_id"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
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
    >

        <option value="">
            No Group
        </option>

        @foreach($attributeGroups as $group)

            <option
                value="{{ $group->id }}"
                @selected(
                    old('group_id', $attribute->group_id) == $group->id
                )
            >
                {{ $group->name }}
            </option>

        @endforeach

    </select>

    @error('group_id')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>



          {{-- ============================================================
     TRANSLATIONS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Translations
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Manage the attribute name for each available language.
        </p>

    </div>


    {{-- LANGUAGES --}}
    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            @foreach($languages as $lang)

                <div>

                    <label
                        for="translation-{{ $lang->code }}"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Name
                        <span class="text-gray-400 font-medium">
                            ({{ strtoupper($lang->code) }})
                        </span>
                    </label>

                    <input
                        type="text"
                        name="translations[{{ $lang->code }}]"
                        id="translation-{{ $lang->code }}"
                        value="{{ old(
                            'translations.' . $lang->code,
                            $translations[$lang->code] ?? ''
                        ) }}"
                        class="
                            mt-2
                            w-full
                            h-10
                            px-3
                            rounded-lg
                            border border-gray-200
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
                        "
                    >

                    @error('translations.' . $lang->code)
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            @endforeach

        </div>

    </div>

</div>



          {{-- ============================================================
     ATTRIBUTE CODE
============================================================ --}}

<div>

    <label
        for="code"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Code
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Unique technical identifier used by the system.
    </p>

    <input
        type="text"
        name="code"
        id="code"
        value="{{ old('code', $attribute->code) }}"
        class="
            mt-2
            w-full
            h-10
            px-3
            rounded-lg
            border border-gray-200
            bg-gray-50
            text-sm
            font-mono
            text-gray-900
            outline-none
            transition
            focus:bg-white
            focus:border-gray-400
            focus:ring-2
            focus:ring-gray-100
        "
        required
    >

    @error('code')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>



           {{-- ============================================================
     TYPE & UNIT
============================================================ --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- TYPE --}}
    <div>

        <label
            for="type"
            class="block text-[13px] font-semibold text-gray-800"
        >
            Type
        </label>

        <p class="mt-1 text-[11px] text-gray-400">
            Defines how the attribute value is entered.
        </p>

        <select
            name="type"
            id="type"
            class="
                mt-2
                w-full
                h-10
                px-3
                rounded-lg
                border border-gray-200
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
            required
        >

            <option value="text"
                @selected(old('type', $attribute->type) === 'text')>
                Text
            </option>

            <option value="number"
                @selected(old('type', $attribute->type) === 'number')>
                Number
            </option>

            <option value="select"
                @selected(old('type', $attribute->type) === 'select')>
                Select
            </option>

            <option value="multiselect"
                @selected(old('type', $attribute->type) === 'multiselect')>
                Multiselect
            </option>

            <option value="boolean"
                @selected(old('type', $attribute->type) === 'boolean')>
                Boolean
            </option>

            <option value="measurement"
                @selected(old('type', $attribute->type) === 'measurement')>
                Measurement
            </option>

        </select>

        @error('type')
            <span class="block mt-1.5 text-xs text-red-500">
                {{ $message }}
            </span>
        @enderror

    </div>


    {{-- UNIT --}}
    <div>

        <label
            for="unit_id"
            class="block text-[13px] font-semibold text-gray-800"
        >
            Unit
            <span class="font-medium text-gray-400">(optional)</span>
        </label>

        <p class="mt-1 text-[11px] text-gray-400">
            Measurement or numeric unit displayed next to the value.
        </p>

        <select
            name="unit_id"
            id="unit_id"
            class="
                mt-2
                w-full
                h-10
                px-3
                rounded-lg
                border border-gray-200
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
        >

            <option value="">
                No unit
            </option>

            @foreach($units as $group => $groupUnits)

                <optgroup
                    label="{{ ucfirst(str_replace('_', ' ', $group)) }}"
                    data-unit-group="{{ $group }}"
                >

                    @foreach($groupUnits as $unit)

                        <option
                            value="{{ $unit->id }}"
                            data-unit-group="{{ $group }}"
                            @selected(
                                old('unit_id', $attribute->unit_id) == $unit->id
                            )
                        >
                            {{ $unit->symbol }}
                            —
                            {{ $unit->translation()?->name ?? $unit->code }}
                        </option>

                    @endforeach

                </optgroup>

            @endforeach

        </select>

        @error('unit_id')
            <p class="mt-1 text-xs text-red-500">
                {{ $message }}
            </p>
        @enderror

    </div>

</div>


{{-- ============================================================
     ATTRIBUTE FLAGS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Attribute Settings
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Configure how this attribute behaves across the platform.
        </p>

    </div>


    <div class="p-5">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">


            {{-- REQUIRED --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_required"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_required', $attribute->is_required))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Required
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Value must be provided
                    </span>
                </span>

            </label>


            {{-- FILTERABLE --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_filterable"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_filterable', $attribute->is_filterable))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Filterable
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Available in product filters
                    </span>
                </span>

            </label>


            {{-- CUSTOM --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_custom"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_custom', $attribute->is_custom))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Custom
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Supplier-specific attribute
                    </span>
                </span>

            </label>


            {{-- OFFERABLE --}}
            <label
                class="
                    flex items-start gap-3
                    p-3
                    rounded-lg
                    border border-gray-200
                    bg-white
                    cursor-pointer
                    transition
                    hover:bg-gray-50
                "
            >

                <input
                    type="checkbox"
                    name="is_offerable"
                    value="1"
                    class="
                        mt-0.5
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(old('is_offerable', $attribute->is_offerable))
                >

                <span>
                    <span class="block text-sm font-medium text-gray-800">
                        Offerable
                    </span>

                    <span class="block mt-0.5 text-[11px] text-gray-400">
                        Available in supplier offers
                    </span>
                </span>

            </label>

        </div>

    </div>

</div>


{{-- ============================================================
     SORT ORDER
============================================================ --}}

<div>

    <label
        for="sort_order"
        class="block text-[13px] font-semibold text-gray-800"
    >
        Sort Order
    </label>

    <p class="mt-1 text-[11px] text-gray-400">
        Defines the default display order of this attribute.
    </p>

    <input
        type="number"
        name="sort_order"
        id="sort_order"
        min="0"
        value="{{ old('sort_order', $attribute->sort_order) }}"
        class="
            mt-2
            w-32
            h-10
            px-3
            rounded-lg
            border border-gray-200
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
    >

    @error('sort_order')
        <span class="block mt-1.5 text-xs text-red-500">
            {{ $message }}
        </span>
    @enderror

</div>


{{-- ============================================================
     SYSTEM INFORMATION
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            System Information
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Ownership and creation information for this attribute.
        </p>

    </div>


    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


            {{-- OWNER TYPE --}}
            <div>

                <label
                    for="owner_type"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Owner Type
                </label>

                <input
                    type="text"
                    name="owner_type"
                    id="owner_type"
                    value="{{ old('owner_type', $attribute->owner_type) }}"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-100
                        text-sm
                        font-mono
                        text-gray-600
                        outline-none
                        focus:border-gray-300
                    "
                >

            </div>


            {{-- OWNER ID --}}
            <div>

                <label
                    for="owner_id"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Owner ID
                </label>

                <input
                    type="text"
                    name="owner_id"
                    id="owner_id"
                    value="{{ old('owner_id', $attribute->owner_id) }}"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-100
                        text-sm
                        font-mono
                        text-gray-600
                        outline-none
                        focus:border-gray-300
                    "
                >

            </div>


            {{-- CREATED BY --}}
            <div class="md:col-span-2">

                <label
                    for="created_by"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Created By User
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    User who originally created this attribute.
                </p>

                <input
                    type="text"
                    name="created_by"
                    id="created_by"
                    value="{{ old('created_by', $attribute->created_by) }}"
                    class="
                        mt-2
                        w-full
                        h-10
                        px-3
                        rounded-lg
                        border border-gray-200
                        bg-gray-100
                        text-sm
                        font-mono
                        text-gray-500
                        cursor-not-allowed
                    "
                    disabled
                >

            </div>

        </div>

    </div>

</div>

            {{-- Actions --}}

            <div class="flex justify-end gap-3 pt-4 border-t">

                <a
                    href="{{ route('admin.settings.attributes.index') }}"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">

                    Отмена

                </a>


                <button
                    type="submit"
                    class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">

                    Обновить

                </button>

            </div>


        </form>

    </div>

</div>



<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect = document.getElementById('type');
    const unitSelect = document.getElementById('unit_id');

    if (!typeSelect || !unitSelect) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Current attribute unit group
    |--------------------------------------------------------------------------
    */

    function getSelectedUnitGroup() {

        const selectedOption =
            unitSelect.options[unitSelect.selectedIndex];

        return selectedOption?.dataset.unitGroup ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter units
    |--------------------------------------------------------------------------
    */

    function filterUnits() {

        const type = typeSelect.value;

        const selectedGroup = getSelectedUnitGroup();


        const options = unitSelect.querySelectorAll(
            'option[data-unit-group]'
        );


        options.forEach(option => {

            const optionGroup =
                option.dataset.unitGroup;


            /*
            |--------------------------------------------------------------------------
            | NUMBER
            |
            | Number can use any unit.
            |--------------------------------------------------------------------------
            */

            if (type === 'number') {

                option.hidden = false;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | MEASUREMENT
            |
            | Measurement uses the unit group of the selected unit.
            |--------------------------------------------------------------------------
            */

            if (type === 'measurement') {

                /*
                 * No unit selected yet.
                 * Show all units so the admin can choose one.
                 */

                if (!selectedGroup) {

                    option.hidden = false;

                    return;
                }


                /*
                 * Once a unit is selected,
                 * show only units from the same group.
                 */

                option.hidden =
                    optionGroup !== selectedGroup;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | OTHER TYPES
            |--------------------------------------------------------------------------
            */

            option.hidden = true;

        });


        /*
        |--------------------------------------------------------------------------
        | Hide "No unit" for unsupported types
        |--------------------------------------------------------------------------
        */

        const noUnitOption =
            unitSelect.querySelector('option[value=""]');

        if (noUnitOption) {

            noUnitOption.hidden =
                type !== 'number' &&
                type !== 'measurement';

        }


        /*
        |--------------------------------------------------------------------------
        | Clear unit for types that do not support units
        |--------------------------------------------------------------------------
        */

        if (
            type !== 'number' &&
            type !== 'measurement'
        ) {

            unitSelect.value = '';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | TYPE CHANGE
    |--------------------------------------------------------------------------
    */

    typeSelect.addEventListener('change', function () {

        filterUnits();

    });


    /*
    |--------------------------------------------------------------------------
    | UNIT CHANGE
    |--------------------------------------------------------------------------
    */

    unitSelect.addEventListener('change', function () {

        /*
         * When Measurement is selected,
         * changing the unit establishes the unit group.
         *
         * After that only units from the same group
         * remain available.
         */

        if (typeSelect.value === 'measurement') {

            filterUnits();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    filterUnits();

});
</script>



@endsection