@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-4xl">

    <x-alerts />

    {{-- ============================================================
        HEADER
    ============================================================= --}}

    <div class="flex items-center justify-between">

        <div>

            <h2 class="text-2xl font-semibold text-gray-900">
                Attribute Options
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage options for:
                <span class="font-medium text-gray-700">
                    {{ $attribute->name }}
                </span>

                <span class="text-gray-400">
                    · {{ $attribute->code }}
                </span>
            </p>

        </div>

        <a
            href="{{ route('admin.settings.attributes.index') }}"
            class="inline-flex items-center
                   px-4 py-2
                   text-sm font-medium
                   text-gray-700
                   bg-white
                   border border-gray-200
                   rounded-lg
                   hover:bg-gray-50
                   transition"
        >
            ← Attributes
        </a>

    </div>


    @php
        use App\Models\Language;

        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    @endphp



{{-- ============================================================
    SAVED OPTIONS
============================================================= --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">

        <div>

            <h3 class="font-semibold text-gray-800 text-sm">
                Saved Options
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                All saved values for this attribute.
            </p>

        </div>

        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-200 text-gray-600 text-xs">
            {{ $options->count() }} {{ $options->count() === 1 ? 'option' : 'options' }}
        </span>

    </div>


    @if($options->count())

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-white border-b border-gray-100">

                    <tr>

                        <th class="px-5 py-3 text-left font-medium text-gray-500">
                            #
                        </th>

                        @foreach($languages as $lang)

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                {{ strtoupper($lang->code) }}
                            </th>

                        @endforeach

                        <th class="px-5 py-3 text-left font-medium text-gray-500">
                            Sort
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @foreach($options as $option)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-5 py-3">

                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-xs font-semibold text-gray-600">
                                    {{ $option->id }}
                                </span>

                            </td>


                            @foreach($languages as $lang)

                                <td class="px-5 py-3 text-gray-800">

                                    {{ $option->translations
                                        ->where('locale', $lang->code)
                                        ->first()?->value ?? '—' }}

                                </td>

                            @endforeach


                            <td class="px-5 py-3 text-gray-600">
                                {{ $option->sort_order }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="px-5 py-8 text-center text-sm text-gray-400">
            No saved options yet.
        </div>

    @endif

</div>



    {{-- ============================================================
        ADD OPTION
    ============================================================= --}}

    <div
        class="bg-white
               border border-gray-200
               rounded-xl
               shadow-sm
               overflow-hidden"
    >

        {{-- HEADER --}}

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

            <h3 class="font-semibold text-gray-800 text-sm">
                Add Option
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Create a new option and provide its translations.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('admin.settings.attributes.options.store', $attribute->id) }}"
            class="p-5 space-y-5"
        >

            @csrf


            {{-- ====================================================
                TRANSLATIONS
            ===================================================== --}}

            <div
                class="border border-gray-200
                       rounded-xl
                       p-4
                       space-y-4"
            >

                <h3 class="font-medium text-gray-700 text-sm">
                    Translations
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    @foreach($languages as $lang)

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-1"
                            >
                                Value
                                <span class="text-gray-400">
                                    ({{ strtoupper($lang->code) }})
                                </span>
                            </label>

                            <input
                                type="text"
                                name="translations[{{ $lang->code }}]"
                                value="{{ old('translations.' . $lang->code) }}"
                                class="w-full h-10
                                       px-3
                                       rounded-lg
                                       border border-gray-200
                                       bg-white
                                       text-sm text-gray-800
                                       focus:border-gray-400
                                       focus:ring-2
                                       focus:ring-gray-100"
                                required
                            >

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- ====================================================
                SORT ORDER
            ===================================================== --}}

            <div class="max-w-xs">

                <label
                    class="block text-sm
                           text-gray-600
                           mb-1"
                >
                    Sort order
                </label>

                <input
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', 0) }}"
                    class="w-full h-10
                           px-3
                           rounded-lg
                           border border-gray-200
                           bg-white
                           text-sm text-gray-800
                           focus:border-gray-400
                           focus:ring-2
                           focus:ring-gray-100"
                >

            </div>


            {{-- ====================================================
                ACTIONS
            ===================================================== --}}

            <div
                class="flex justify-end
                       gap-3
                       pt-4
                       border-t border-gray-200"
            >

                <a
                    href="{{ route('admin.settings.attributes.index') }}"
                    class="px-4 py-2
                           text-sm
                           font-medium
                           text-gray-700
                           bg-white
                           border border-gray-200
                           rounded-lg
                           hover:bg-gray-50
                           transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-5 py-2
                           text-sm
                           font-medium
                           bg-gray-900
                           text-white
                           rounded-lg
                           hover:bg-gray-800
                           transition
                           shadow-sm"
                >
                    Add Option
                </button>

            </div>

        </form>

    </div>



    {{-- ============================================================
        EXISTING OPTIONS
    ============================================================= --}}

    <div class="space-y-4">

        <div class="flex items-center justify-between">

            <div>

                <h3 class="text-sm font-semibold text-gray-800">
                    Existing Options
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Edit translations and sort order of existing options.
                </p>

            </div>

            <span
                class="inline-flex items-center
                       px-2.5 py-1
                       rounded-md
                       bg-gray-100
                       text-gray-600
                       text-xs"
            >
                {{ $options->count() }}
                {{ $options->count() === 1 ? 'option' : 'options' }}
            </span>

        </div>


        @forelse($options as $option)

            {{-- ====================================================
                OPTION CARD
            ===================================================== --}}

            <div
                class="bg-white
                       border border-gray-200
                       rounded-xl
                       shadow-sm
                       overflow-hidden"
            >

                {{-- OPTION HEADER --}}

                <div
                    class="px-5 py-3
                           bg-gray-50
                           border-b border-gray-200
                           flex items-center
                           justify-between"
                >

                    <div class="flex items-center gap-3">

                        <span
                            class="inline-flex items-center
                                   justify-center
                                   w-7 h-7
                                   rounded-lg
                                   bg-gray-200
                                   text-xs
                                   font-semibold
                                   text-gray-600"
                        >
                            {{ $option->sort_order }}
                        </span>

                        <div>

                            <div class="text-sm font-semibold text-gray-800">
                                Option #{{ $option->id }}
                            </div>

                            <div class="text-xs text-gray-500">
                                Sort order: {{ $option->sort_order }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- OPTION FORM --}}

                <form
                    method="POST"
                    action="{{ route(
                        'admin.settings.attributes.options.update',
                        [$attribute->id, $option->id]
                    ) }}"
                    class="p-5"
                >

                    @csrf
                    @method('PUT')


                    {{-- TRANSLATIONS --}}

                    <div
                        class="border border-gray-200
                               rounded-xl
                               p-4
                               space-y-4"
                    >

                        <h4 class="font-medium text-gray-700 text-sm">
                            Translations
                        </h4>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            @foreach($languages as $lang)

                                <div>

                                    <label
                                        class="block text-sm
                                               text-gray-600
                                               mb-1"
                                    >
                                        Value
                                        <span class="text-gray-400">
                                            ({{ strtoupper($lang->code) }})
                                        </span>
                                    </label>

                                    <input
                                        type="text"
                                        name="translations[{{ $lang->code }}]"
                                        value="{{ $option->translations
                                            ->where('locale', $lang->code)
                                            ->first()?->value ?? '' }}"
                                        class="w-full h-10
                                               px-3
                                               rounded-lg
                                               border border-gray-200
                                               bg-white
                                               text-sm text-gray-800
                                               focus:border-gray-400
                                               focus:ring-2
                                               focus:ring-gray-100"
                                    >

                                </div>

                            @endforeach

                        </div>

                    </div>


                    {{-- SORT + ACTIONS --}}

                    <div
                        class="flex items-end
                               justify-between
                               gap-4
                               mt-5
                               pt-4
                               border-t border-gray-200"
                    >

                        <div class="w-32">

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-1"
                            >
                                Sort order
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                min="0"
                                value="{{ $option->sort_order }}"
                                class="w-full h-10
                                       px-3
                                       rounded-lg
                                       border border-gray-200
                                       bg-white
                                       text-sm text-gray-800
                                       focus:border-gray-400
                                       focus:ring-2
                                       focus:ring-gray-100"
                            >

                        </div>


                        <div class="flex items-center gap-2">

                            <button
                                type="submit"
                                class="px-4 py-2
                                       text-sm
                                       font-medium
                                       bg-gray-900
                                       text-white
                                       rounded-lg
                                       hover:bg-gray-800
                                       transition
                                       shadow-sm"
                            >
                                Save
                            </button>

                </form>


                            {{-- DELETE --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.settings.attributes.options.destroy',
                                    [$attribute->id, $option->id]
                                ) }}"
                                onsubmit="return confirm('Delete this option?')"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="px-4 py-2
                                           text-sm
                                           font-medium
                                           text-red-600
                                           bg-white
                                           border border-red-200
                                           rounded-lg
                                           hover:bg-red-50
                                           transition"
                                >
                                    Delete
                                </button>

                            </form>

                        </div>

                    </div>

            </div>

        @empty

            <div
                class="bg-white
                       border border-gray-200
                       rounded-xl
                       px-5 py-10
                       text-center"
            >

                <div class="text-sm font-medium text-gray-700">
                    No options yet
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Add the first option using the form above.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection