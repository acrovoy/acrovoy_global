@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-4xl">

    <x-alerts />

    {{-- HEADER --}}

    <div>

        <h1 class="text-2xl font-semibold text-gray-900">
            Edit Payment Term
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Update the payment term and its translations.
        </p>

    </div>


    {{-- FORM --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form
            action="{{ route('admin.settings.payment-terms.update', $paymentTerm->id) }}"
            method="POST"
            class="p-6 flex flex-col gap-6"
        >

            @csrf
            @method('PUT')


            {{-- BASIC INFORMATION --}}

            <div>

                <h2 class="text-sm font-semibold text-gray-900">
                    Basic Information
                </h2>

                <p class="text-[11px] text-gray-400 mt-1">
                    Define the internal code and display order of this payment term.
                </p>

            </div>


            {{-- CODE --}}

            <div>

                <label
                    for="code"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Code
                </label>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code', $paymentTerm->code) }}"
                    required
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
                >

                @error('code')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- SORT ORDER --}}

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
                    value="{{ old('sort_order', $paymentTerm->sort_order) }}"
                    min="0"
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

                @error('sort_order')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- STATUS --}}

            <div>

                <label
                    for="is_active"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Status
                </label>

                <select
                    name="is_active"
                    id="is_active"
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

                    <option
                        value="1"
                        @selected(old(
                            'is_active',
                            $paymentTerm->is_active ? '1' : '0'
                        ) == '1')
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        @selected(old(
                            'is_active',
                            $paymentTerm->is_active ? '1' : '0'
                        ) == '0')
                    >
                        Inactive
                    </option>

                </select>

                @error('is_active')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- TRANSLATIONS --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-5">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Translations
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Update the name and description for each active language.
                    </p>

                </div>


                <div class="flex flex-col gap-5">

                    @foreach($languages as $language)

                        @php
                            $translation = $paymentTerm->translations
                                ->firstWhere('locale', $language->code);
                        @endphp

                        <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">

                            <div class="flex items-center justify-between gap-4 mb-4">

                                <div>

                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $language->name }}
                                    </div>

                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        {{ $language->native_name }}
                                    </div>

                                </div>

                                <span
                                    class="
                                        inline-flex
                                        items-center
                                        px-2
                                        py-1
                                        rounded-md
                                        bg-white
                                        border
                                        border-gray-200
                                        text-[11px]
                                        font-mono
                                        text-gray-500
                                    "
                                >
                                    {{ strtoupper($language->code) }}
                                </span>

                            </div>


                            {{-- NAME --}}

                            <div>

                                <label
                                    for="translation_name_{{ $language->code }}"
                                    class="block text-[13px] font-semibold text-gray-800"
                                >
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="translations[{{ $language->code }}][name]"
                                    id="translation_name_{{ $language->code }}"
                                    value="{{ old(
                                        "translations.{$language->code}.name",
                                        $translation?->name
                                    ) }}"
                                    class="
                                        mt-2
                                        w-full
                                        h-10
                                        px-3
                                        rounded-lg
                                        border border-gray-200
                                        bg-white
                                        text-sm
                                        text-gray-900
                                        outline-none
                                        transition
                                        focus:border-gray-400
                                        focus:ring-2
                                        focus:ring-gray-100
                                    "
                                    placeholder="Payment term name"
                                >

                                @error("translations.{$language->code}.name")
                                    <span class="block mt-1.5 text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- DESCRIPTION --}}

                            <div class="mt-4">

                                <label
                                    for="translation_description_{{ $language->code }}"
                                    class="block text-[13px] font-semibold text-gray-800"
                                >
                                    Description
                                </label>

                                <textarea
                                    name="translations[{{ $language->code }}][description]"
                                    id="translation_description_{{ $language->code }}"
                                    rows="3"
                                    class="
                                        mt-2
                                        w-full
                                        px-3
                                        py-2.5
                                        rounded-lg
                                        border border-gray-200
                                        bg-white
                                        text-sm
                                        text-gray-900
                                        outline-none
                                        transition
                                        resize-y
                                        focus:border-gray-400
                                        focus:ring-2
                                        focus:ring-gray-100
                                    "
                                    placeholder="Short description of this payment term"
                                >{{ old(
                                    "translations.{$language->code}.description",
                                    $translation?->description
                                ) }}</textarea>

                                @error("translations.{$language->code}.description")
                                    <span class="block mt-1.5 text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- ACTIONS --}}

            <div
                class="
                    flex
                    items-center
                    justify-end
                    gap-3
                    pt-6
                    border-t
                    border-gray-100
                "
            >

                <a
                    href="{{ route('admin.settings.payment-terms.index') }}"
                    class="
                        px-4
                        py-2
                        text-sm
                        border
                        border-gray-300
                        rounded-lg
                        text-gray-700
                        hover:bg-gray-100
                        transition
                    "
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="
                        px-4
                        py-2
                        text-sm
                        font-medium
                        rounded-lg
                        bg-gray-900
                        text-white
                        hover:bg-gray-800
                        transition
                    "
                >
                    Update Payment Term
                </button>

            </div>

        </form>

    </div>

</div>

@endsection