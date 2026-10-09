@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">


<x-alerts />

{{-- ============================================================
    HEADER
============================================================ --}}

<div>

    <div class="flex items-center gap-3">

        <a
            href="{{ route('admin.settings.return-policy-reasons.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
            title="Back"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
        </a>

        <div>

            <div class="flex items-center gap-3">

                <h1 class="text-2xl font-semibold text-gray-900">
                    Edit Return Reason
                </h1>

                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 border border-gray-200 text-xs font-medium text-gray-500">
                    #{{ $reason->id }}
                </span>

            </div>

            <p class="text-sm text-gray-500 mt-1">
                Update the return reason and its translations.
            </p>

        </div>

    </div>

</div>


{{-- ============================================================
    FORM
============================================================ --}}

<form
    method="POST"
    action="{{ route('admin.settings.return-policy-reasons.update', $reason->id) }}"
    class="flex flex-col gap-6"
>

    @csrf
    @method('PUT')


    {{-- ========================================================
        BASIC INFORMATION
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-200">

            <h2 class="text-base font-semibold text-gray-900">
                Basic Information
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Define the internal identifier and display order for this return reason.
            </p>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


                {{-- ====================================================
                    CODE
                ===================================================== --}}

                <div>

                    <label
                        for="code"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Code
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="code"
                        type="text"
                        name="code"
                        value="{{ old('code', $reason->code) }}"
                        placeholder="e.g. defective_product"
                        class="
                            mt-2
                            block
                            w-full
                            rounded-lg
                            border
                            @error('code')
                                border-red-300
                            @else
                                border-gray-300
                            @enderror
                            px-3
                            py-2.5
                            text-sm
                            text-gray-900
                            placeholder-gray-400
                            focus:border-gray-500
                            focus:ring-1
                            focus:ring-gray-500
                            outline-none
                        "
                    >

                    <p class="mt-1.5 text-xs text-gray-400">
                        Unique internal code used by the system.
                    </p>

                    @error('code')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ====================================================
                    SORT ORDER
                ===================================================== --}}

                <div>

                    <label
                        for="sort_order"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Sort Order
                    </label>

                    <input
                        id="sort_order"
                        type="number"
                        name="sort_order"
                        value="{{ old('sort_order', $reason->sort_order) }}"
                        min="0"
                        step="1"
                        placeholder="0"
                        class="
                            mt-2
                            block
                            w-full
                            rounded-lg
                            border
                            @error('sort_order')
                                border-red-300
                            @else
                                border-gray-300
                            @enderror
                            px-3
                            py-2.5
                            text-sm
                            text-gray-900
                            placeholder-gray-400
                            focus:border-gray-500
                            focus:ring-1
                            focus:ring-gray-500
                            outline-none
                        "
                    >

                    <p class="mt-1.5 text-xs text-gray-400">
                        Lower values appear first.
                    </p>

                    @error('sort_order')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ====================================================
                    STATUS
                ===================================================== --}}

                <div>

                    <label
                        for="is_active"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Status
                    </label>

                    <div class="mt-2">

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input
                                id="is_active"
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="sr-only peer"
                                @checked(old('is_active', $reason->is_active))
                            >

                            <div class="
                                w-11
                                h-6
                                bg-gray-200
                                rounded-full
                                peer
                                peer-focus:outline-none
                                peer-focus:ring-2
                                peer-focus:ring-gray-300
                                peer-checked:bg-gray-900
                                after:content-['']
                                after:absolute
                                after:top-[2px]
                                after:left-[2px]
                                after:bg-white
                                after:border-gray-300
                                after:border
                                after:rounded-full
                                after:h-5
                                after:w-5
                                after:transition-all
                                peer-checked:after:translate-x-full
                                peer-checked:after:border-white
                            "></div>

                            <span class="ml-3 text-sm text-gray-700">
                                Active
                            </span>

                        </label>

                    </div>

                    <p class="mt-1.5 text-xs text-gray-400">
                        Inactive reasons will not be available for new return requests.
                    </p>

                    @error('is_active')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================
        TRANSLATIONS
    ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-200">

            <h2 class="text-base font-semibold text-gray-900">
                Translations
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add the name and description for each available language.
            </p>

        </div>

        <div class="divide-y divide-gray-100">

            @foreach($languages as $language)

                @php
                    $locale = $language->code;

                    $translation = $reason->translations
                        ->firstWhere('locale', $locale);

                    $translationName = old(
                        "translations.$locale.name",
                        $translation?->name
                    );

                    $translationDescription = old(
                        "translations.$locale.description",
                        $translation?->description
                    );
                @endphp

                <div class="p-6">

                    {{-- ====================================================
                        LANGUAGE HEADER
                    ===================================================== --}}

                    <div class="flex items-center justify-between mb-5">

                        <div class="flex items-center gap-3">

                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-xs font-semibold text-gray-600 uppercase">
                                {{ strtoupper($language->code) }}
                            </div>

                            <div>

                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $language->name }}
                                </div>

                                @if($language->native_name && $language->native_name !== $language->name)

                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $language->native_name }}
                                    </div>

                                @endif

                            </div>

                        </div>

                        @if($language->is_default)

                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 border border-gray-200 text-xs font-medium text-gray-600">
                                Default
                            </span>

                        @endif

                    </div>


                    {{-- ====================================================
                        TRANSLATION FIELDS
                    ===================================================== --}}

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- NAME --}}

                        <div>

                            <label
                                for="translation_{{ $locale }}_name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Name
                                @if($language->is_default)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            <input
                                id="translation_{{ $locale }}_name"
                                type="text"
                                name="translations[{{ $locale }}][name]"
                                value="{{ $translationName }}"
                                placeholder="Return reason name"
                                class="
                                    mt-2
                                    block
                                    w-full
                                    rounded-lg
                                    border
                                    @error("translations.$locale.name")
                                        border-red-300
                                    @else
                                        border-gray-300
                                    @enderror
                                    px-3
                                    py-2.5
                                    text-sm
                                    text-gray-900
                                    placeholder-gray-400
                                    focus:border-gray-500
                                    focus:ring-1
                                    focus:ring-gray-500
                                    outline-none
                                "
                            >

                            @error("translations.$locale.name")
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- DESCRIPTION --}}

                        <div>

                            <label
                                for="translation_{{ $locale }}_description"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Description
                            </label>

                            <textarea
                                id="translation_{{ $locale }}_description"
                                name="translations[{{ $locale }}][description]"
                                rows="3"
                                placeholder="Optional description"
                                class="
                                    mt-2
                                    block
                                    w-full
                                    rounded-lg
                                    border
                                    @error("translations.$locale.description")
                                        border-red-300
                                    @else
                                        border-gray-300
                                    @enderror
                                    px-3
                                    py-2.5
                                    text-sm
                                    text-gray-900
                                    placeholder-gray-400
                                    focus:border-gray-500
                                    focus:ring-1
                                    focus:ring-gray-500
                                    outline-none
                                    resize-none
                                "
                            >{{ $translationDescription }}</textarea>

                            @error("translations.$locale.description")
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    {{-- ========================================================
        USAGE INFORMATION
    ========================================================= --}}

    @if($reason->policies()->exists())

        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">

            <div class="flex items-start gap-3">

                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 shrink-0">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m0 3h.008M10.29 3.86l-7.1 12.28A2 2 0 004.92 19h14.16a2 2 0 001.73-2.86L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>

                </div>

                <div>

                    <div class="text-sm font-semibold text-amber-900">
                        This return reason is currently in use
                    </div>

                    <p class="text-sm text-amber-800 mt-1">
                        This reason is assigned to one or more return policies.
                        Changes to this reason may affect how it is displayed in those policies.
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================
        FORM ACTIONS
    ========================================================= --}}

    <div class="flex items-center justify-end gap-3">

        <a
            href="{{ route('admin.settings.return-policy-reasons.index') }}"
            class="
                inline-flex
                items-center
                px-4
                py-2.5
                rounded-lg
                border
                border-gray-200
                bg-white
                text-gray-700
                text-sm
                font-medium
                hover:bg-gray-50
                transition
            "
        >
            Cancel
        </a>

        <button
            type="submit"
            class="
                inline-flex
                items-center
                px-5
                py-2.5
                rounded-lg
                bg-gray-900
                text-white
                text-sm
                font-medium
                hover:bg-gray-800
                transition
            "
        >
            Save Changes
        </button>

    </div>

</form>


</div>

@endsection
