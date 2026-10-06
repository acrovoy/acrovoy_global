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
                Add Material
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Create a material and assign it to a material group.
            </p>
        </div>


        

    </div>


    {{-- =========================================================
        ALERTS
    ========================================================== --}}

    <x-alerts />


    {{-- =========================================================
        FORM
    ========================================================== --}}

    <form
        action="{{ route('supplier.materials.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf


        {{-- =====================================================
            MATERIAL INFORMATION
        ====================================================== --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="text-base font-semibold text-gray-900">
                    Material Information
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Define the material group and internal identifier.
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
                                {{ old('material_group_id') == $group->id ? 'selected' : '' }}
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
                        value="{{ old('slug') }}"
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
                    Enter the material name or code for each active language.
                </p>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    @foreach($languages as $language)

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
                                value="{{ old("name.{$language->code}") }}"
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
                    Upload a representative image of this material.
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

                            <template x-if="preview">

                                <img
                                    :src="preview"
                                    alt="Material preview"
                                    class="w-full h-full object-cover"
                                >

                            </template>

                            <template x-if="!preview">

                                <div class="flex flex-col items-center justify-center text-gray-400">

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

                            </template>

                        </div>

                    </div>


                    {{-- UPLOAD --}}
                    <div class="flex-1">

                        <label
                            for="photo"
                            class="block text-sm font-medium text-gray-800 mb-2"
                        >
                            Upload image
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
                                Click to upload an image
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
                        d="M12 5v14M5 12h14"
                    />
                </svg>

                Save Material

            </button>

        </div>

    </form>

</div>

@endsection