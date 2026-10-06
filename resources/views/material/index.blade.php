@extends('dashboard.layout')

@section('dashboard-content')

<div class="flex flex-col gap-6">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Materials & Colors
            </h2>

            <p class="text-sm text-gray-500">
                Browse materials organized by material groups
            </p>
        </div>


        {{-- =====================================================
            HEADER ACTIONS
        ====================================================== --}}

        <div class="flex items-center gap-3">
            {{-- Group Management --}}
            <button
                type="button"
                onclick="openMaterialGroupDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 active:scale-[0.98] transition-all duration-150 shadow-sm"
            >
                <span>Group Management</span>
            </button>

            {{-- Add Material --}}
            <a
                href="{{ route('supplier.materials.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 active:scale-[0.98] transition-all duration-150 shadow-sm"
            >
                <span class="text-lg leading-none">+</span>
                <span>Add Material</span>
            </a>
        </div>

    </div>


    <x-alerts />

    

    {{-- =========================================================
        GROUP BLOCKS
    ========================================================== --}}

    <div class="flex flex-col gap-6">

        @foreach($groups as $group)

    {{-- =================================================
        GROUP BLOCK
    ================================================== --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        {{-- =================================================
            GROUP HEADER
        ================================================== --}}

        <div class="px-6 py-5 border-b border-gray-200">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $group->translatedName() ?? $group->slug }}

                        {{-- MATERIAL COUNT --}}
                        <span
                            class="shrink-0 inline-flex items-center
                                   px-2.5 py-1 rounded-md
                                   bg-gray-100 text-gray-600 text-xs ml-2"
                        >
                            {{ $group->materials->count() }}
                            {{ $group->materials->count() === 1 ? 'material' : 'materials' }}
                        </span>
                    </h3>

                    @if($group->translatedDescription())

                        <p class="mt-1 text-sm text-gray-500 max-w-4xl">
                            {{ $group->translatedDescription() }}
                        </p>

                    @endif

                </div>

                <div>

                    {{-- BRAND --}}
                    @if($group->brand)

                        <div class="text-sm text-gray-500 mt-3">
                            Brand: {{ $group->brand }}
                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- =================================================
            MATERIAL PREVIEWS
        ================================================== --}}

        <div class="p-6">

            @if($group->materials->isNotEmpty())

                <div
                    class="grid
                           grid-cols-2
                           sm:grid-cols-3
                           md:grid-cols-4
                           lg:grid-cols-5
                           xl:grid-cols-6
                           gap-x-5
                           gap-y-7"
                >

                    @foreach($group->materials as $material)

                        {{-- =====================================
                            MATERIAL CARD
                        ====================================== --}}

                        <a
                            href="{{ route('supplier.materials.edit', $material) }}"
                            class="group block"
                        >

                            {{-- =================================
                                IMAGE
                            ================================== --}}

                            <div
                                class="aspect-square
                                       overflow-hidden
                                       rounded-xl
                                       border
                                       border-gray-200
                                       bg-gray-50
                                       transition-all
                                       duration-200
                                       group-hover:border-gray-300
                                       group-hover:shadow-sm"
                            >

                                @if($material->photo?->cdn_url)

                                    <img
                                        src="{{ $material->photo->cdn_url }}"
                                        alt="{{ $material->name ?: $material->slug }}"
                                        class="w-full
                                               h-full
                                               object-cover
                                               transition-transform
                                               duration-300
                                               group-hover:scale-[1.03]"
                                    >

                                @else

                                    <div
                                        class="w-full
                                               h-full
                                               flex
                                               items-center
                                               justify-center
                                               text-gray-400
                                               text-sm"
                                    >
                                        No photo
                                    </div>

                                @endif

                            </div>


                            {{-- =================================
                                MATERIAL NAME
                            ================================== --}}

                            <div class="mt-1">

                                <div
                                    class="text-xs
                                           font-medium
                                           text-gray-900
                                           text-center
                                           transition-colors
                                           duration-150
                                           group-hover:text-gray-600"
                                >
                                    {{ $material->name ?: $material->slug }}
                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

            @else

                {{-- EMPTY GROUP --}}

                <div class="py-10 text-center">

                    <p class="text-sm text-gray-400">
                        No materials in this group.
                    </p>

                </div>

            @endif

        </div>

    </div>

@endforeach

    </div>

</div>



 {{-- MATERIAL GROUP DRAWER --}}
<div id="materialGroupDrawer" class="fixed inset-0 z-50 hidden">

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
        onclick="closeMaterialGroupDrawer()"
    ></div>

    {{-- DRAWER --}}
    <div
        id="materialGroupPanel"
        class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-2xl
               transform translate-x-full transition-transform duration-300
               flex flex-col min-h-0"
    >

        {{-- HEADER --}}
        <div class="shrink-0 px-6 py-5 border-b bg-gray-50">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Material Groups
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Manage groups used to organize materials.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeMaterialGroupDrawer()"
                    class="shrink-0 text-gray-400 hover:text-gray-700 transition text-xl leading-none"
                >
                    ✕
                </button>

            </div>

        </div>

        {{-- BODY --}}
        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">

            @if($groups->isNotEmpty())

                <div class="space-y-3">

                    @foreach($groups as $group)

                        @php
                            $groupTranslation = $group->translation();
                        @endphp

                        <div
                            class="border border-gray-200 rounded-xl p-4
                                   hover:border-gray-300 hover:shadow-sm transition"
                        >

                            {{-- GROUP HEADER --}}
                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <h4 class="text-sm font-semibold text-gray-900">
                                        {{ $groupTranslation?->name ?? $group->slug }}
                                    </h4>

                                    @if($group->brand)

                                        <p class="text-xs text-gray-500 mt-1">
                                            Brand: {{ $group->brand }}
                                        </p>

                                    @endif

                                </div>

                                {{-- MATERIAL COUNT --}}
                                <span
                                    class="shrink-0 inline-flex items-center px-2 py-1
                                           rounded-md bg-gray-100 text-gray-600 text-xs"
                                >
                                    {{ $group->materials->count() }}
                                    {{ $group->materials->count() === 1 ? 'material' : 'materials' }}
                                </span>

                            </div>

                            {{-- DESCRIPTION --}}
                            @if($groupTranslation?->description)

                                <p class="mt-2 text-xs leading-5 text-gray-500">
                                    {{ $groupTranslation->description }}
                                </p>

                            @endif

                            {{-- ACTIONS --}}
<div
    class="mt-4 pt-3 border-t border-gray-100
           flex items-center gap-2"
>

    @php
        $editGroupData = [
            'id' => $group->id,
            'slug' => $group->slug,
            'brand' => $group->brand,
            'update_url' => route(
                'supplier.material-groups.update',
                $group
            ),
            'translations' => $group->translations
                ->keyBy('locale')
                ->map(function ($translation) {
                    return [
                        'name' => $translation->name,
                        'description' => $translation->description,
                    ];
                })
                ->toArray(),
        ];
    @endphp

     <button
        type="button"
        onclick='openEditMaterialGroupDrawer(@json($editGroupData))'
        class="px-3 py-1.5 text-xs font-medium
               text-gray-700 bg-white
               border border-gray-200 rounded-md
               hover:bg-gray-50 hover:border-gray-300
               transition"
    >
        Edit
    </button>


     {{-- DELETE --}}
    <form
        id="deleteMaterialGroupForm{{ $group->id }}"
        method="POST"
        action="{{ route('supplier.material-groups.destroy', $group) }}"
    >
        @csrf
        @method('DELETE')

        <button
            type="button"
            onclick="confirmDeleteMaterialGroup({{ $group->id }}, @js($group->translatedName() ?? $group->slug))"
            class="px-3 py-1.5 text-xs font-medium
                   text-red-600 bg-white
                   border border-red-200 rounded-md
                   hover:bg-red-50 hover:border-red-300
                   transition"
        >
            Delete
        </button>

    </form>

    

</div>

                        </div>

                    @endforeach

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">

                    <div
                        class="w-12 h-12 rounded-xl bg-gray-100
                               flex items-center justify-center mb-4"
                    >
                        <svg
                            class="w-6 h-6 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                            />
                        </svg>
                    </div>

                    <h4 class="text-sm font-medium text-gray-900">
                        No material groups
                    </h4>

                    <p class="mt-1 text-xs text-gray-500 max-w-xs">
                        Create your first material group to start organizing materials.
                    </p>

                </div>

            @endif

        </div>

        {{-- FOOTER --}}
        <div
            class="shrink-0 border-t bg-white px-6 py-4
                   flex items-center justify-between gap-2"
        >

            <button
                type="button"
                onclick="closeMaterialGroupDrawer()"
                class="px-4 py-2 text-sm rounded-lg
                       border border-gray-200 text-gray-600
                       hover:bg-gray-50 transition"
            >
                Close
            </button>

            <button
                type="button"
                onclick="openAddMaterialGroupDrawer()"
                class="px-4 py-2 text-sm rounded-lg
                       bg-gray-900 text-white
                       hover:bg-gray-800 transition shadow-sm"
            >
                + Add Group
            </button>

        </div>

    </div>

</div>

{{-- DRAWER SCRIPT --}}
<script>
    function openMaterialGroupDrawer() {
        const drawer = document.getElementById('materialGroupDrawer');
        const panel = document.getElementById('materialGroupPanel');

        if (!drawer || !panel) return;

        drawer.classList.remove('hidden');

        requestAnimationFrame(() => {
            panel.classList.remove('translate-x-full');
        });
    }

    function closeMaterialGroupDrawer() {
        const drawer = document.getElementById('materialGroupDrawer');
        const panel = document.getElementById('materialGroupPanel');

        if (!drawer || !panel) return;

        panel.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
        }, 300);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMaterialGroupDrawer();
        }
    });
</script>


{{-- ADD MATERIAL GROUP DRAWER --}}
<div id="addMaterialGroupDrawer"
     class="fixed inset-0 z-[60] hidden">

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        onclick="closeAddMaterialGroupDrawer()"
    ></div>

    {{-- DRAWER --}}
    <div
        id="addMaterialGroupPanel"
        class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-2xl
               transform translate-x-full transition-transform duration-300
               flex flex-col min-h-0"
    >

        {{-- HEADER --}}
        <div class="shrink-0 px-6 py-5 border-b bg-gray-50">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Add Material Group
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Create a new group for organizing materials.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeAddMaterialGroupDrawer()"
                    class="shrink-0 text-gray-400 hover:text-gray-700 transition text-xl leading-none"
                >
                    ✕
                </button>

            </div>
        </div>

        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('supplier.material-groups.store') }}"
            class="flex flex-col flex-1 min-h-0"
        >

            @csrf

            {{-- BODY --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">

                <div class="space-y-5">

                    {{-- SLUG --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Slug
                        </label>

                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="e.g. metals"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                                   focus:outline-none focus:ring-1 focus:ring-gray-400
                                   focus:border-gray-400"
                        >

                        <p class="mt-1 text-xs text-gray-400">
                            Unique identifier used internally.
                        </p>
                    </div>


                    {{-- BRAND --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Brand
                        </label>

                        <input
                            type="text"
                            name="brand"
                            value="{{ old('brand') }}"
                            placeholder="Optional"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                                   focus:outline-none focus:ring-1 focus:ring-gray-400
                                   focus:border-gray-400"
                        >
                    </div>


                    {{-- TRANSLATIONS --}}
                    <div>

                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-gray-900">
                                Translations
                            </h4>

                            <p class="text-xs text-gray-500 mt-1">
                                Enter the group name and description for each language.
                            </p>
                        </div>

                        <div class="space-y-4">

    @foreach($languages as $language)

        <div class="border border-gray-200 rounded-xl p-4">

            <div class="flex items-center justify-between mb-3">

                <h5 class="text-sm font-medium text-gray-800">
                    {{ $language->name }}
                </h5>

                <span class="text-xs uppercase text-gray-400">
                    {{ $language->code }}
                </span>

            </div>

            {{-- NAME --}}
            <div class="mb-3">

                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Name
                </label>

                <input
                    type="text"
                    name="translations[{{ $language->code }}][name]"
                    value="{{ old("translations.{$language->code}.name") }}"
                    placeholder="Group name"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-gray-400
                           focus:border-gray-400"
                >

            </div>

            {{-- DESCRIPTION --}}
            <div>

                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Description
                </label>

                <textarea
                    name="translations[{{ $language->code }}][description]"
                    rows="3"
                    placeholder="Group description"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-gray-400
                           focus:border-gray-400 resize-none"
                >{{ old("translations.{$language->code}.description") }}</textarea>

            </div>

        </div>

    @endforeach

</div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="shrink-0 border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

                <button
                    type="button"
                    onclick="closeAddMaterialGroupDrawer()"
                    class="px-4 py-2 text-sm rounded-lg
                           border border-gray-200 text-gray-600
                           hover:bg-gray-50 transition"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 text-sm rounded-lg
                           bg-gray-900 text-white
                           hover:bg-gray-800 transition shadow-sm"
                >
                    Create Group
                </button>

            </div>

        </form>

    </div>
</div>


{{-- ADD GROUP DRAWER SCRIPT --}}
<script>
    function openAddMaterialGroupDrawer() {
        const drawer = document.getElementById('addMaterialGroupDrawer');
        const panel = document.getElementById('addMaterialGroupPanel');

        if (!drawer || !panel) return;

        drawer.classList.remove('hidden');

        requestAnimationFrame(() => {
            panel.classList.remove('translate-x-full');
        });
    }

    function closeAddMaterialGroupDrawer() {
        const drawer = document.getElementById('addMaterialGroupDrawer');
        const panel = document.getElementById('addMaterialGroupPanel');

        if (!drawer || !panel) return;

        panel.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
        }, 300);
    }
</script>


{{-- EDIT MATERIAL GROUP DRAWER --}}
<div id="editMaterialGroupDrawer"
     class="fixed inset-0 z-[70] hidden">

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        onclick="closeEditMaterialGroupDrawer()"
    ></div>

    {{-- DRAWER --}}
    <div
        id="editMaterialGroupPanel"
        class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-2xl
               transform translate-x-full transition-transform duration-300
               flex flex-col min-h-0"
    >

        {{-- HEADER --}}
        <div class="shrink-0 px-6 py-5 border-b bg-gray-50">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Edit Material Group
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Update group information and translations.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeEditMaterialGroupDrawer()"
                    class="shrink-0 text-gray-400 hover:text-gray-700
                           transition text-xl leading-none"
                >
                    ✕
                </button>

            </div>

        </div>


        {{-- FORM --}}
        <form
            id="editMaterialGroupForm"
            method="POST"
            class="flex flex-col flex-1 min-h-0"
        >

            @csrf
            @method('PUT')

            {{-- BODY --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">

                <div class="space-y-5">

                    {{-- SLUG --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Slug
                        </label>

                        <input
                            type="text"
                            id="editMaterialGroupSlug"
                            name="slug"
                            value=""
                            placeholder="e.g. metals"
                            class="w-full px-3 py-2 text-sm border border-gray-200
                                   rounded-lg focus:outline-none focus:ring-1
                                   focus:ring-gray-400 focus:border-gray-400"
                        >

                        <p class="mt-1 text-xs text-gray-400">
                            Unique identifier used internally.
                        </p>

                    </div>


                    {{-- BRAND --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Brand
                        </label>

                        <input
                            type="text"
                            id="editMaterialGroupBrand"
                            name="brand"
                            value=""
                            placeholder="Optional"
                            class="w-full px-3 py-2 text-sm border border-gray-200
                                   rounded-lg focus:outline-none focus:ring-1
                                   focus:ring-gray-400 focus:border-gray-400"
                        >

                    </div>


                    {{-- TRANSLATIONS --}}
                    <div>

                        <div class="mb-3">

                            <h4 class="text-sm font-semibold text-gray-900">
                                Translations
                            </h4>

                            <p class="text-xs text-gray-500 mt-1">
                                Update the group name and description for each language.
                            </p>

                        </div>


                       <div class="space-y-4">

    @foreach($languages as $language)

        <div class="border border-gray-200 rounded-xl p-4">

            <div class="flex items-center justify-between mb-3">

                <h5 class="text-sm font-medium text-gray-800">
                    {{ $language->name }}
                </h5>

                <span class="text-xs uppercase text-gray-400">
                    {{ $language->code }}
                </span>

            </div>


            {{-- NAME --}}
            <div class="mb-3">

                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Name
                </label>

                <input
                    type="text"
                    id="editMaterialGroupTranslationName{{ $language->code }}"
                    name="translations[{{ $language->code }}][name]"
                    value="{{ old("translations.{$language->code}.name") }}"
                    placeholder="Group name"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-gray-400
                           focus:border-gray-400"
                >

            </div>


            {{-- DESCRIPTION --}}
            <div>

                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Description
                </label>

                <textarea
                    id="editMaterialGroupTranslationDescription{{ $language->code }}"
                    name="translations[{{ $language->code }}][description]"
                    rows="3"
                    placeholder="Group description"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-gray-400
                           focus:border-gray-400 resize-none"
                >{{ old("translations.{$language->code}.description") }}</textarea>

            </div>

        </div>

    @endforeach

</div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div
                class="shrink-0 border-t bg-white px-6 py-4
                       flex items-center justify-between gap-2"
            >

                <button
                    type="button"
                    onclick="closeEditMaterialGroupDrawer()"
                    class="px-4 py-2 text-sm rounded-lg
                           border border-gray-200 text-gray-600
                           hover:bg-gray-50 transition"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 text-sm rounded-lg
                           bg-gray-900 text-white
                           hover:bg-gray-800 transition shadow-sm"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>


{{-- EDIT GROUP DRAWER SCRIPT --}}
<script>

    function openEditMaterialGroupDrawer(group) {

        const drawer = document.getElementById('editMaterialGroupDrawer');
        const panel = document.getElementById('editMaterialGroupPanel');
        const form = document.getElementById('editMaterialGroupForm');

        if (!drawer || !panel || !form || !group) return;


        /*
        |--------------------------------------------------------------------------
        | FORM ACTION
        |--------------------------------------------------------------------------
        */

        form.action = group.update_url;


        /*
        |--------------------------------------------------------------------------
        | BASIC FIELDS
        |--------------------------------------------------------------------------
        */

        document.getElementById('editMaterialGroupSlug').value =
            group.slug ?? '';

        document.getElementById('editMaterialGroupBrand').value =
            group.brand ?? '';


        /*
        |--------------------------------------------------------------------------
        | TRANSLATIONS
        |--------------------------------------------------------------------------
        */

        const translations = group.translations ?? {};

        @foreach($languages as $language)

            document.getElementById(
                'editMaterialGroupTranslationName{{ $language->code }}'
            ).value = translations['{{ $language->code }}']?.name ?? '';

            document.getElementById(
                'editMaterialGroupTranslationDescription{{ $language->code }}'
            ).value = translations['{{ $language->code }}']?.description ?? '';

        @endforeach


        /*
        |--------------------------------------------------------------------------
        | OPEN
        |--------------------------------------------------------------------------
        */

        drawer.classList.remove('hidden');

        requestAnimationFrame(() => {
            panel.classList.remove('translate-x-full');
        });
    }


    function closeEditMaterialGroupDrawer() {

        const drawer = document.getElementById('editMaterialGroupDrawer');
        const panel = document.getElementById('editMaterialGroupPanel');

        if (!drawer || !panel) return;

        panel.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
        }, 300);
    }

</script>

<script>

    function confirmDeleteMaterialGroup(groupId, groupName) {

        const form = document.getElementById(
            'deleteMaterialGroupForm' + groupId
        );

        if (!form || !window.confirmModal) {
            return;
        }

        window.confirmModal.open({

            type: 'danger',

            title: 'Delete Material Group',

            description: 'This action cannot be undone.',

            message:
                'Are you sure you want to delete "' +
                groupName +
                '"? All translations will also be deleted.',

            cancelText: 'Cancel',

            confirmText: 'Delete',

            onConfirm: function () {
                form.submit();
            }

        });

    }

</script>



@endsection