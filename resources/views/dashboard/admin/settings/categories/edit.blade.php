@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-semibold text-gray-900">Edit Category <span class="text-amber-600">{{ $category->name }}</span> <span class="p-1 text-sm text-gray-600 rounded-md border border-gray-900 bg-gray-100">{{ $category->id }}</span>
    </h1>
</div>

<x-alerts />

<form action="{{ route('admin.settings.categories.update', $category) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    @php
        use App\Models\Language;

        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- SLUG --}}
    <div>
        <label class="block text-gray-700">
            Slug
        </label>

        <input
            type="text"
            name="slug"
            class="mt-1 block w-full
                   border-gray-300
                   rounded-lg"
            value="{{ old('slug', $category->slug) }}"
        >

        @error('slug')
            <span class="text-red-500 text-sm">
                {{ $message }}
            </span>
        @enderror
    </div>


    {{-- PARENT CATEGORY --}}
    <div>
        <label class="block text-gray-700">
            Parent Category
        </label>

        <select
            name="parent_id"
            class="mt-1 block w-full
                   border-gray-300
                   rounded-lg"
        >
            <option value="">
                None
            </option>

            @foreach($categories as $cat)

                <option
                    value="{{ $cat->id }}"
                    @selected(
                        old('parent_id', $category->parent_id) == $cat->id
                    )
                >
                    {{ $cat->name }}
                </option>

            @endforeach

        </select>

        @error('parent_id')
            <span class="text-red-500 text-sm">
                {{ $message }}
            </span>
        @enderror
    </div>

</div>

    {{-- ============================================================
    CATEGORY TRANSLATIONS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER / TOGGLE --}}
    <button
        type="button"
        id="category-translations-toggle"
        class="w-full px-5 py-4
               bg-gray-50
               border-b border-gray-200
               flex items-center justify-between
               text-left
               hover:bg-gray-100
               transition"
        aria-expanded="false"
    >

        <div>
            <h3 class="font-semibold text-gray-800 text-sm">
                Category Names
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Manage category names for different languages.
            </p>
        </div>

        <span
            id="category-translations-arrow"
            class="flex items-center justify-center
                   w-8 h-8
                   rounded-lg
                   border border-gray-200
                   bg-white
                   text-gray-500
                   transition-transform duration-200"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </span>

    </button>


    {{-- TRANSLATIONS CONTENT --}}
    <div
        id="category-translations-content"
        class="hidden"
    >

        <div class="p-5 space-y-3">

            @foreach($languages as $lang)

                <div>

                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        {{ strtoupper($lang->code) }}
                    </label>

                    <input
                        type="text"
                        name="name[{{ $lang->code }}]"
                        placeholder="{{ strtoupper($lang->code) }}"
                        class="block w-full
                               border border-gray-200
                               rounded-lg
                               px-3 py-2
                               text-sm
                               text-gray-800
                               bg-white
                               focus:border-gray-400
                               focus:ring-2
                               focus:ring-gray-100
                               outline-none"
                        value="{{ old(
                            "name.$lang->code",
                            $category->translations
                                ->firstWhere('locale', $lang->code)
                                ?->name
                        ) }}"
                    >

                    @error("name.$lang->code")
                        <span class="text-red-500 text-xs mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            @endforeach

        </div>

    </div>

</div>



    {{-- COMMISSION PERCENT --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Commission Percent
    </label>

    <div class="relative max-w-xs">

        <input
            type="number"
            name="commission_percent"
            step="0.01"
            min="0"
            max="100"
            class="w-full h-10
                   px-3 pr-10
                   rounded-lg
                   border border-gray-200
                   bg-white
                   text-sm text-gray-800
                   outline-none
                   transition
                   focus:border-gray-400
                   focus:ring-2
                   focus:ring-gray-100"
            value="{{ old('commission_percent', $category->commission_percent) }}"
        >

        <span
            class="absolute right-3 top-1/2
                   -translate-y-1/2
                   text-xs font-medium
                   text-gray-400
                   pointer-events-none"
        >
            %
        </span>

    </div>

    <p class="text-xs text-gray-500 mt-1.5">
        Commission percentage applied to Level 3 categories.
    </p>

    @error('commission_percent')
        <span class="block text-red-500 text-xs mt-1">
            {{ $message }}
        </span>
    @enderror
</div>

    {{-- CATEGORY ATTRIBUTES --}}
<div
    class="border border-gray-200 rounded-xl overflow-hidden"
    x-data="categoryAttributesManager()"
>

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <div class="flex items-start justify-between gap-4">

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">
                    Category Attributes
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Select attributes and configure whether they are required and their display order.
                </p>
            </div>

            {{-- SELECTED COUNT --}}
            <div
                class="shrink-0 inline-flex items-center gap-1.5
                       px-2.5 py-1 rounded-full
                       bg-gray-100 border border-gray-200
                       text-xs font-medium text-gray-600"
            >
                <span>Selected</span>

                <span
                    class="inline-flex items-center justify-center
                           min-w-5 h-5 px-1
                           rounded-full
                           bg-white border border-gray-200
                           text-[11px] font-semibold text-gray-800"
                    x-text="selectedCount"
                ></span>
            </div>

        </div>

    </div>


    {{-- SEARCH / FILTER --}}
    <div class="p-4 border-b border-gray-200 bg-white">

        <div class="flex flex-col sm:flex-row gap-3">

            {{-- SEARCH --}}
            <div class="relative flex-1">

                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2
                           w-4 h-4 text-gray-400 pointer-events-none"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"
                    />
                </svg>

                <input
                    type="text"
                    x-model="search"
                    placeholder="Search attributes..."
                    class="w-full h-10 pl-9 pr-9
                           rounded-lg
                           border border-gray-200
                           bg-gray-50
                           text-sm text-gray-800
                           placeholder:text-gray-400
                           outline-none
                           transition
                           focus:bg-white
                           focus:border-gray-400
                           focus:ring-2
                           focus:ring-gray-100"
                >

                <button
                    type="button"
                    x-show="search"
                    x-cloak
                    @click="search = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2
                           text-gray-400 hover:text-gray-700"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6L6 18"
                        />
                    </svg>
                </button>

            </div>


            {{-- TYPE FILTER --}}
            <select
                x-model="type"
                class="sm:w-48 h-10 px-3
                       rounded-lg
                       border border-gray-200
                       bg-gray-50
                       text-sm text-gray-700
                       outline-none
                       focus:bg-white
                       focus:border-gray-400
                       focus:ring-2
                       focus:ring-gray-100"
            >

                <option value="">All types</option>
                <option value="select">Select</option>
                <option value="multiselect">Multiselect</option>
                <option value="number">Number</option>
                <option value="text">Text</option>
                <option value="boolean">Boolean</option>

            </select>

        </div>


        {{-- ACTIVE FILTER INFO --}}
        <div
            x-show="search || type"
            x-cloak
            class="mt-3 flex items-center justify-between"
        >

            <span class="text-xs text-gray-500">
                <span x-text="filteredCount"></span>
                attribute(s) found
            </span>

            <button
                type="button"
                @click="clearFilters()"
                class="text-xs font-medium text-gray-600 hover:text-gray-900"
            >
                Clear filters
            </button>

        </div>

    </div>


    {{-- SELECTED ATTRIBUTES --}}
    <div
        x-show="selectedAttributes.length"
        x-cloak
        class="border-b border-gray-200"
    >

        <div class="px-5 py-3 bg-gray-50">

            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                Selected attributes
            </div>

        </div>


        <div class="divide-y divide-gray-100">

            <template
                x-for="attribute in selectedAttributes"
                :key="'selected-' + attribute.id"
            >

                <div
                    class="px-5 py-3"
                    x-show="matches(attribute)"
                >

                    <div class="flex items-center gap-3">

                        {{-- ENABLE --}}
                        <input
                            type="checkbox"
                            :name="`attributes[${attribute.id}][enabled]`"
                            value="1"
                            :id="`attr-${attribute.id}`"
                            checked
                            @change="toggleEnabled(attribute)"
                            class="w-4 h-4 rounded
                                   border-gray-300
                                   text-gray-900
                                   focus:ring-gray-400"
                        >


                        {{-- NAME --}}
                        <div class="flex-1 min-w-0">

                            <label
                                :for="`attr-${attribute.id}`"
                                class="text-sm font-medium text-gray-800 cursor-pointer"
                                x-text="attribute.name"
                            ></label>

                            <div class="flex items-center gap-2 mt-0.5">

                                <span
                                    class="text-[10px] text-gray-400 uppercase"
                                    x-text="attribute.type"
                                ></span>

                                <template x-if="attribute.unit">
                                    <span
                                        class="text-[10px] text-gray-400"
                                        x-text="'· ' + attribute.unit"
                                    ></span>
                                </template>

                            </div>

                        </div>


                        {{-- EXPAND --}}
                        <button
                            type="button"
                            @click="toggleOpen(attribute.id)"
                            class="w-8 h-8 flex items-center justify-center
                                   rounded-lg
                                   text-gray-400
                                   hover:bg-gray-100
                                   hover:text-gray-700"
                        >

                            <svg
                                class="w-4 h-4 transition-transform"
                                :class="isOpen(attribute.id) ? 'rotate-180' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                        </button>

                    </div>


                    {{-- SETTINGS --}}
                    <div
                        x-show="isOpen(attribute.id)"
                        x-collapse
                        class="mt-3 ml-7 pl-4
                               border-l border-gray-200"
                    >

                        <div class="flex flex-wrap items-center gap-5">

                            {{-- REQUIRED --}}
                            <label class="flex items-center gap-2 cursor-pointer">

                                <input
                                    type="checkbox"
                                    :name="`attributes[${attribute.id}][is_required]`"
                                    value="1"
                                    :checked="attribute.required"
                                    @change="attribute.required = $event.target.checked"
                                    class="w-4 h-4 rounded
                                           border-gray-300
                                           text-gray-900
                                           focus:ring-gray-400"
                                >

                                <span class="text-sm text-gray-600">
                                    Required
                                </span>

                            </label>


                            {{-- ORDER --}}
                            <div class="flex items-center gap-2">

                                <label
                                    :for="`sort-${attribute.id}`"
                                    class="text-sm text-gray-500"
                                >
                                    Order
                                </label>

                                <input
                                    type="number"
                                    min="0"
                                    :name="`attributes[${attribute.id}][sort_order]`"
                                    :id="`sort-${attribute.id}`"
                                    x-model="attribute.sort_order"
                                    class="w-20 h-9 px-2
                                           rounded-lg
                                           border border-gray-200
                                           bg-white
                                           text-sm text-gray-800
                                           focus:border-gray-400
                                           focus:ring-2
                                           focus:ring-gray-100"
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </template>

        </div>

    </div>


    {{-- AVAILABLE ATTRIBUTES --}}
    <div>

        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">

            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                Available attributes
            </div>

        </div>


        <div class="divide-y divide-gray-100">

            <template
                x-for="attribute in filteredAttributes"
                :key="'available-' + attribute.id"
            >

                <div class="px-5 py-3">

                    <div class="flex items-center gap-3">

                        {{-- ENABLE --}}
                        <input
                            type="checkbox"
                            :name="`attributes[${attribute.id}][enabled]`"
                            value="1"
                            :id="`available-attr-${attribute.id}`"
                            :checked="attribute.enabled"
                            @change="toggleEnabled(attribute)"
                            class="w-4 h-4 rounded
                                   border-gray-300
                                   text-gray-900
                                   focus:ring-gray-400"
                        >


                        {{-- NAME --}}
                        <div class="flex-1 min-w-0">

                            <label
                                :for="`available-attr-${attribute.id}`"
                                class="text-sm font-medium text-gray-800 cursor-pointer"
                                x-text="attribute.name"
                            ></label>

                            <div class="flex items-center gap-2 mt-0.5">

                                <span
                                    class="text-[10px] text-gray-400 uppercase"
                                    x-text="attribute.type"
                                ></span>

                                <template x-if="attribute.unit">
                                    <span
                                        class="text-[10px] text-gray-400"
                                        x-text="'· ' + attribute.unit"
                                    ></span>
                                </template>

                            </div>

                        </div>

                    </div>

                </div>

            </template>


            {{-- EMPTY --}}
            <div
                x-show="filteredAttributes.length === 0"
                x-cloak
                class="px-5 py-10 text-center"
            >

                <div class="text-sm font-medium text-gray-500">
                    No attributes found
                </div>

                <div class="text-xs text-gray-400 mt-1">
                    Try changing your search or filter.
                </div>

            </div>

        </div>

    </div>

</div>








   @php
    $types = [
        'product' => 'Product',
        'rfq' => 'RFQ',
        'project' => 'Project',
    ];

    $selectedTypes = old(
        'types',
        $category->types->pluck('type')->toArray()
    );
@endphp


{{-- ============================================================
     CATEGORY CONTEXTS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Category Contexts
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Select where this category can be used.
        </p>

    </div>


    {{-- CONTEXTS --}}
    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            @foreach($types as $key => $label)

                @php
                    $isSelected = in_array($key, $selectedTypes);
                @endphp

                <label
                    for="type-{{ $key }}"
                    class="
                        group
                        relative
                        flex items-center
                        gap-3
                        px-4 py-3
                        rounded-lg
                        border
                        cursor-pointer
                        transition

                        {{ $isSelected
                            ? 'border-gray-400 bg-gray-50'
                            : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'
                        }}
                    "
                >

                    <input
                        type="checkbox"
                        name="types[]"
                        value="{{ $key }}"
                        id="type-{{ $key }}"
                        class="
                            w-4 h-4
                            rounded
                            border-gray-300
                            text-gray-900
                            focus:ring-gray-400
                        "
                        @checked($isSelected)
                    >


                    <div class="flex flex-col min-w-0">

                        <span
                            class="
                                text-sm
                                font-medium
                                text-gray-800
                            "
                        >
                            {{ $label }}
                        </span>

                        <span class="text-[11px] text-gray-400">
                            {{ match($key) {
                                'product' => 'Product catalog',
                                'rfq' => 'Request for quotation',
                                'project' => 'Project',
                                default => '',
                            } }}
                        </span>

                    </div>

                </label>

            @endforeach

        </div>


        {{-- VALIDATION --}}
        @error('types')
            <div class="mt-3 text-xs text-red-500">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>



   <div>
    <label class="block text-gray-700">OLD Category Type</label>

    <div class="mt-1 w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-gray-700">
        {{ $category->type ?? '—' }}
    </div>
</div>

    {{-- ============================================================
     CATEGORY SETTINGS
============================================================ --}}

<div class="border border-gray-200 rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Category Settings
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Configure category hierarchy, visibility and display order.
        </p>

    </div>


    {{-- SETTINGS --}}
    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">


            {{-- =====================================================
                 LEVEL
            ====================================================== --}}

            <div>

                <label
                    for="level"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Level
                </label>

                <input
                    type="number"
                    name="level"
                    id="level"
                    min="0"
                    value="{{ old('level', $category->level) }}"
                    class="
                        mt-1.5
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

                @error('level')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- =====================================================
                 SORT ORDER
            ====================================================== --}}

            <div>

                <label
                    for="sort_order"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Sort Order
                </label>

                <input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    min="0"
                    value="{{ old('sort_order', $category->sort_order) }}"
                    class="
                        mt-1.5
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

                @error('sort_order')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- =====================================================
                 SELECTABLE
            ====================================================== --}}

            <div
                class="
                    flex items-center
                    min-h-[72px]
                    px-4
                    rounded-lg
                    border border-gray-200
                    bg-white
                    transition
                    hover:bg-gray-50
                "
            >

                <label
                    for="is_selectable"
                    class="flex items-center gap-3 w-full cursor-pointer"
                >

                    <input
                        type="checkbox"
                        name="is_selectable"
                        id="is_selectable"
                        value="1"
                        class="
                            w-4 h-4
                            rounded
                            border-gray-300
                            text-gray-900
                            focus:ring-gray-400
                        "
                        @checked(
                            old(
                                'is_selectable',
                                $category->is_selectable
                            )
                        )
                    >

                    <div>

                        <div class="text-sm font-medium text-gray-800">
                            Selectable
                        </div>

                        <div class="text-[11px] text-gray-400">
                            Users can select this category
                        </div>

                    </div>

                </label>

            </div>


            {{-- =====================================================
                 LEAF
            ====================================================== --}}

            <div
                class="
                    flex items-center
                    min-h-[72px]
                    px-4
                    rounded-lg
                    border border-gray-200
                    bg-white
                    transition
                    hover:bg-gray-50
                "
            >

                <label
                    for="is_leaf"
                    class="flex items-center gap-3 w-full cursor-pointer"
                >

                    <input
                        type="checkbox"
                        name="is_leaf"
                        id="is_leaf"
                        value="1"
                        class="
                            w-4 h-4
                            rounded
                            border-gray-300
                            text-gray-900
                            focus:ring-gray-400
                        "
                        @checked(
                            old(
                                'is_leaf',
                                $category->is_leaf
                            )
                        )
                    >

                    <div>

                        <div class="text-sm font-medium text-gray-800">
                            Leaf Category
                        </div>

                        <div class="text-[11px] text-gray-400">
                            Has no child categories
                        </div>

                    </div>

                </label>

            </div>


        </div>


        {{-- =========================================================
             VISIBILITY
        ========================================================== --}}

        <div
            class="
                mt-4
                flex items-center
                min-h-[60px]
                px-4
                rounded-lg
                border border-gray-200
                bg-gray-50
            "
        >

            <label
                for="is_visible"
                class="flex items-center gap-3 w-full cursor-pointer"
            >

                <input
                    type="checkbox"
                    name="is_visible"
                    id="is_visible"
                    value="1"
                    class="
                        w-4 h-4
                        rounded
                        border-gray-300
                        text-gray-900
                        focus:ring-gray-400
                    "
                    @checked(
                        old(
                            'is_visible',
                            $category->is_visible
                        )
                    )
                >

                <div>

                    <div class="text-sm font-medium text-gray-800">
                        Visible
                    </div>

                    <div class="text-[11px] text-gray-400">
                        Show this category throughout the platform
                    </div>

                </div>

            </label>

        </div>

    </div>

</div>

    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 shadow">Update Category</button>
</form>


<script>
function categoryAttributesManager() {

    return {

        search: '',
        type: '',
        open: [],

        attributes: @js(
            $attributes->map(function ($attribute) use ($category) {

                $categoryAttribute = $category->attributes
                    ->firstWhere('id', $attribute->id);

                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'type' => $attribute->type,
                    'unit' => $attribute->unit,

                    'enabled' => $categoryAttribute !== null,

                    'required' => $categoryAttribute
                        ? (bool) $categoryAttribute->pivot->is_required
                        : false,

                    'sort_order' => $categoryAttribute
                        ? $categoryAttribute->pivot->sort_order
                        : 0,
                ];

            })->values()
        ),


        get selectedAttributes() {

            return this.attributes.filter(attribute =>
                attribute.enabled
            );

        },


        get selectedCount() {

            return this.selectedAttributes.length;

        },


        get filteredAttributes() {

            return this.attributes.filter(attribute => {

                // Уже выбранные не показываем здесь
                if (attribute.enabled) {
                    return false;
                }

                return this.matches(attribute);

            });

        },


        get filteredCount() {

            return this.filteredAttributes.length;

        },


        matches(attribute) {

            const search = this.search
                .trim()
                .toLowerCase();

            const matchesSearch =
                !search ||
                attribute.name
                    ?.toLowerCase()
                    .includes(search);


            const matchesType =
                !this.type ||
                attribute.type === this.type;


            return matchesSearch && matchesType;

        },


        toggleEnabled(attribute) {

            attribute.enabled = !attribute.enabled;

            if (!attribute.enabled) {

                attribute.required = false;
                attribute.sort_order = 0;

                this.open = this.open.filter(
                    id => id !== attribute.id
                );

            }

        },


        toggleOpen(id) {

            if (this.open.includes(id)) {

                this.open = this.open.filter(
                    item => item !== id
                );

            } else {

                this.open.push(id);

            }

        },


        isOpen(id) {

            return this.open.includes(id);

        },


        clearFilters() {

            this.search = '';
            this.type = '';

        }

    };
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('category-translations-toggle');
    const content = document.getElementById('category-translations-content');
    const arrow = document.getElementById('category-translations-arrow');

    if (!toggle || !content || !arrow) {
        return;
    }

    toggle.addEventListener('click', function () {

        const isOpen = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute(
            'aria-expanded',
            isOpen ? 'false' : 'true'
        );

        content.classList.toggle('hidden', isOpen);

        arrow.classList.toggle(
            'rotate-180',
            !isOpen
        );
    });

});
</script>


@endsection