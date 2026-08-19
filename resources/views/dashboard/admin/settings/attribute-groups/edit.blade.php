@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-3xl">

    <x-alerts />

    {{-- ============================================================
        HEADER
    ============================================================ --}}

    <div class="flex items-start justify-between gap-4">

        <div>

            <h1 class="text-xl font-semibold text-gray-900">
                Edit Attribute Group
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Edit attribute group name and translations.
            </p>

        </div>

        <a
            href="{{ route('admin.settings.attribute-groups.index') }}"
            class="
                px-4 py-2
                text-sm
                border border-gray-300
                rounded-lg
                text-gray-700
                hover:bg-gray-100
                transition
            "
        >
            Back
        </a>

    </div>


    {{-- ============================================================
        FORM CARD
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        {{-- ========================================================
            UPDATE FORM
        ======================================================== --}}

        <form action="{{ route('admin.settings.attribute-groups.update', ['group' => $group->id]) }}" 
        method="POST" class="p-6 flex flex-col gap-6">

            @csrf
            @method('PUT')


            {{-- ====================================================
                GROUP NAME
            ==================================================== --}}

            <div>

                <label
                    for="name"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Group Name
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Internal/default name of the attribute group.
                </p>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $group->name) }}"
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

                @error('name')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                STATUS
            ==================================================== --}}

            <div>

                <label
                    for="is_active"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Status
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Inactive groups will not be available for attributes.
                </p>

                <label class="inline-flex items-center gap-3 mt-3 cursor-pointer">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="
                            w-4
                            h-4
                            rounded
                            border-gray-300
                            text-gray-900
                            focus:ring-gray-200
                        "
                        @checked(old('is_active', $group->is_active))
                    >

                    <span class="text-sm text-gray-700">
                        Active
                    </span>

                </label>

                @error('is_active')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                TRANSLATIONS
            ==================================================== --}}

            <div class="pt-2 border-t border-gray-100">

                <div class="mb-4">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Translations
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Enter the group name for each available language.
                    </p>

                </div>


                <div class="flex flex-col gap-4">

                    @foreach($languages as $language)

                        @php

                            $locale = $language->code ?? $language->locale;

                            $translation = $group->translations
                                ->firstWhere('locale', $locale);

                            $translationValue = $translation?->name ?? '';

                            $oldValue = old(
                                "translations.$locale",
                                $translationValue
                            );

                        @endphp

                        <div>

                            <label
                                for="translation_{{ $locale }}"
                                class="
                                    block
                                    text-[13px]
                                    font-semibold
                                    text-gray-800
                                "
                            >

                                {{ $language->name }}

                                <span class="text-gray-400 font-normal">
                                    ({{ strtoupper($locale) }})
                                </span>

                            </label>

                            <input
                                type="text"
                                name="translations[{{ $locale }}]"
                                id="translation_{{ $locale }}"
                                value="{{ $oldValue }}"
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

                            @error("translations.$locale")
                                <span class="block mt-1.5 text-xs text-red-500">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- ====================================================
                META INFORMATION
            ==================================================== --}}

            <div class="pt-4 border-t border-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- ID --}}

                    <div>

                        <span
                            class="
                                block
                                text-[11px]
                                text-gray-400
                                uppercase
                                tracking-wide
                            "
                        >
                            ID
                        </span>

                        <span class="block mt-1 text-sm text-gray-700">
                            {{ $group->id }}
                        </span>

                    </div>


                    {{-- OWNER --}}

                    <div>

                        <span
                            class="
                                block
                                text-[11px]
                                text-gray-400
                                uppercase
                                tracking-wide
                            "
                        >
                            Owner
                        </span>

                        <span class="block mt-1 text-sm text-gray-700">

                            @if($group->owner_type)

                                {{ class_basename($group->owner_type) }}

                                @if($group->owner_id)
                                    #{{ $group->owner_id }}
                                @endif

                            @else

                                System

                            @endif

                        </span>

                    </div>

                </div>

            </div>


            {{-- ====================================================
                ACTIONS
            ==================================================== --}}

            <div
                class="
                    flex
                    items-center
                    justify-end
                    gap-3
                    pt-4
                    border-t
                    border-gray-100
                "
            >

                <a href="{{ route('admin.settings.attribute-groups.index') }}" 
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="
                        px-5
                        py-2
                        text-sm
                        font-medium
                        text-white
                        bg-gray-900
                        rounded-lg
                        hover:bg-gray-800
                        transition
                    "
                >
                    Save Changes
                </button>

            </div>

        </form>


        {{-- ========================================================
            DELETE FORM
        ======================================================== --}}

        <div class="px-6 pb-6">

            <div class="pt-4 border-t border-gray-100">

                <form action="{{ route('admin.settings.attribute-groups.destroy', ['group' => $group->id]) }}" 
                method="POST" onsubmit="return confirm('Delete this attribute group?');">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="
                            px-4
                            py-2
                            text-sm
                            text-red-600
                            border border-red-200
                            rounded-lg
                            hover:bg-red-50
                            transition
                        "
                    >
                        Delete
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection