@extends('dashboard.layout')

@section('dashboard-content')


<a href="{{ route('supplier.materials.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to Materials
        </a>



<div class="max-w-5xl mx-auto space-y-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="flex items-start justify-between gap-6">

        <div>

            

            <h2 class="text-2xl font-semibold text-gray-900">
                Edit Material
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Update material information, translations and photo.
            </p>

        </div>


        

    </div>


    {{-- =========================================================
        ALERTS
    ========================================================== --}}

    <x-alerts />


    {{-- =========================================================
        EDIT FORM
    ========================================================== --}}

    <form
        action="{{ route('supplier.materials.update', $material) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')


        {{-- =====================================================
            MATERIAL INFORMATION
        ====================================================== --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-base font-semibold text-gray-900">
                    Material Information
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Update the material group and internal identifier.
                </p>

            </div>


            <div class="p-6 space-y-6">

                {{-- MATERIAL GROUP --}}
                <div>

                    <label
                        for="material_group_id"
                        class="block text-sm font-medium text-gray-800 mb-2"
                    >
                        Material Group
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="material_group_id"
                        name="material_group_id"
                        required
                        class="w-full rounded-lg
                               border border-gray-300
                               bg-white
                               px-3 py-2.5
                               text-sm text-gray-900
                               shadow-sm
                               transition
                               focus:border-gray-900
                               focus:outline-none
                               focus:ring-1
                               focus:ring-gray-900"
                    >

                        <option value="">
                            Select material group
                        </option>

                        @foreach($groups as $group)

                            <option
                                value="{{ $group->id }}"
                                {{ old('material_group_id', $material->material_group_id) == $group->id ? 'selected' : '' }}
                            >
                                {{ $group->translatedName() ?? $group->slug }}
                            </option>

                        @endforeach

                    </select>

                    @error('material_group_id')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- SLUG --}}
                <div>

                    <label
                        for="slug"
                        class="block text-sm font-medium text-gray-800 mb-2"
                    >
                        Slug
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        value="{{ old('slug', $material->slug) }}"
                        placeholder="e.g. velvet-beige"
                        required
                        class="w-full rounded-lg
                               border border-gray-300
                               bg-white
                               px-3 py-2.5
                               text-sm text-gray-900
                               shadow-sm
                               placeholder:text-gray-400
                               transition
                               focus:border-gray-900
                               focus:outline-none
                               focus:ring-1
                               focus:ring-gray-900"
                    >

                    <p class="mt-2 text-xs text-gray-400">
                        Use a unique internal identifier for this material.
                    </p>

                    @error('slug')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- =====================================================
            TRANSLATIONS
        ====================================================== --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-base font-semibold text-gray-900">
                    Material Names
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Update the material name or code for each active language.
                </p>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    @foreach($languages as $language)

                        @php
                            $translation = $material->translate($language->code);
                        @endphp

                        <div>

                            <label
                                for="name_{{ $language->code }}"
                                class="flex items-center justify-between
                                       text-sm font-medium text-gray-800 mb-2"
                            >

                                <span>
                                    Material Name / Code
                                </span>

                                <span
                                    class="inline-flex items-center
                                           rounded-md
                                           bg-gray-100
                                           px-2 py-0.5
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-wide
                                           text-gray-600"
                                >
                                    {{ $language->code }}
                                </span>

                            </label>

                            <input
                                id="name_{{ $language->code }}"
                                type="text"
                                name="name[{{ $language->code }}]"
                                value="{{ old(
                                    "name.{$language->code}",
                                    $translation?->name ?? ''
                                ) }}"
                                placeholder="Enter material name"
                                required
                                class="w-full rounded-lg
                                       border border-gray-300
                                       bg-white
                                       px-3 py-2.5
                                       text-sm text-gray-900
                                       shadow-sm
                                       placeholder:text-gray-400
                                       transition
                                       focus:border-gray-900
                                       focus:outline-none
                                       focus:ring-1
                                       focus:ring-gray-900"
                            >

                            @error("name.{$language->code}")

                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    @endforeach

                </div>

            </div>

        </div>


        {{-- =====================================================
            MATERIAL PHOTO
        ====================================================== --}}

        <div
            x-data="{
                preview: null,
                fileName: '',
                handleFile(event) {
                    const file = event.target.files[0];

                    if (!file) {
                        this.preview = null;
                        this.fileName = '';
                        return;
                    }

                    this.fileName = file.name;

                    const reader = new FileReader();

                    reader.onload = (e) => {
                        this.preview = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            }"
            class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"
        >

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-base font-semibold text-gray-900">
                    Material Photo
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Update the representative image of this material.
                </p>

            </div>


            <div class="p-6">

                <div class="flex flex-col sm:flex-row gap-6">

                    {{-- PREVIEW --}}
                    <div class="shrink-0">

                        <div
                            class="w-32 h-32
                                   overflow-hidden
                                   rounded-xl
                                   border border-gray-200
                                   bg-gray-50
                                   flex items-center justify-center"
                        >

                            {{-- NEW PHOTO PREVIEW --}}
                            <template x-if="preview">

                                <img
                                    :src="preview"
                                    alt="New material preview"
                                    class="w-full h-full object-cover"
                                >

                            </template>


                            {{-- CURRENT PHOTO --}}
                            <template x-if="!preview">

                                @if($material->photo?->cdn_url)

                                    <img
                                        src="{{ $material->photo->cdn_url }}"
                                        alt="{{ $material->name ?: $material->slug }}"
                                        class="w-full h-full object-cover"
                                    >

                                @else

                                    <div
                                        class="flex flex-col items-center justify-center
                                               text-gray-400"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-8 h-8 mb-2"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>

                                        <span class="text-xs">
                                            No image
                                        </span>

                                    </div>

                                @endif

                            </template>

                        </div>

                    </div>


                    {{-- UPLOAD --}}
                    <div class="flex-1">

                        <label
                            for="photo"
                            class="block text-sm font-medium text-gray-800 mb-2"
                        >
                            Replace image
                        </label>

                        <label
                            for="photo"
                            class="flex flex-col items-center justify-center
                                   w-full min-h-[128px]
                                   rounded-xl
                                   border-2 border-dashed
                                   border-gray-300
                                   bg-gray-50
                                   cursor-pointer
                                   transition
                                   hover:border-gray-400
                                   hover:bg-gray-100"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-7 h-7 text-gray-400 mb-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"
                                />
                            </svg>

                            <span class="text-sm font-medium text-gray-700">
                                Click to replace the image
                            </span>

                            <span
                                class="mt-1 text-xs text-gray-400"
                                x-text="fileName || 'JPG, JPEG, PNG or WEBP · Maximum 4 MB'"
                            ></span>

                        </label>

                        <input
                            id="photo"
                            type="file"
                            name="photo"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="handleFile"
                        >

                        <p class="mt-2 text-xs text-gray-400">
                            If you select a new image, the current image will be replaced.
                        </p>

                        @error('photo')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            ACTIONS
        ====================================================== --}}

        <div class="flex items-center justify-between">

            <a
                href="{{ route('supplier.materials.index') }}"
                class="inline-flex items-center
                       px-4 py-2.5
                       text-sm font-medium
                       text-gray-700
                       rounded-lg
                       hover:bg-gray-100
                       transition"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center
                       gap-2
                       px-5 py-2.5
                       rounded-lg
                       bg-gray-900
                       text-sm font-semibold
                       text-white
                       shadow-sm
                       transition
                       hover:bg-gray-800
                       focus:outline-none
                       focus:ring-2
                       focus:ring-gray-900
                       focus:ring-offset-2"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

                Save Changes

            </button>

        </div>

    </form>


    {{-- =========================================================
        DANGER ZONE
    ========================================================== --}}

    <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-red-100">

            <h3 class="text-base font-semibold text-gray-900">
                Danger Zone
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Permanently remove this material.
            </p>

        </div>


        <div class="p-6">

            <div class="flex flex-col sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-5">

                <div>

                    <h4 class="text-sm font-semibold text-gray-900">
                        Delete Material
                    </h4>

                    <p class="mt-1 text-sm text-gray-500">
                        This material will be permanently removed from its material group.
                    </p>

                </div>


                <form
    id="deleteMaterialForm{{ $material->id }}"
    action="{{ route('supplier.materials.destroy', $material) }}"
    method="POST"
>
    @csrf
    @method('DELETE')

    <button
        type="button"
        onclick="confirmDeleteMaterial(
            {{ $material->id }},
            @js($material->translate(app()->getLocale())?->name ?? $material->slug)
        )"
        class="inline-flex items-center justify-center
               rounded-xl
               border border-red-200
               bg-white
               px-4 py-2.5
               text-sm font-semibold
               text-red-600
               transition
               hover:border-red-300
               hover:bg-red-50
               focus:outline-none
               focus:ring-2
               focus:ring-red-500
               focus:ring-offset-2"
    >
        Delete Material
    </button>
</form>

            </div>

        </div>

    </div>

</div>


<script>
    function confirmDeleteMaterial(materialId, materialName) {

        const form = document.getElementById(
            'deleteMaterialForm' + materialId
        );

        if (!form || !window.confirmModal) {
            return;
        }

        window.confirmModal.open({

            type: 'danger',

            title: 'Delete Material',

            description: 'This action cannot be undone.',

            message:
                'Are you sure you want to delete "' +
                materialName +
                '"? All translations and related data will also be deleted.',

            cancelText: 'Cancel',

            confirmText: 'Delete',

            onConfirm: function () {
                form.submit();
            }

        });

    }
</script>



@endsection