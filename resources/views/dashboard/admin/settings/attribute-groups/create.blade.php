@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6 max-w-3xl">

    <x-alerts />

    {{-- ============================================================
        HEADER
    ============================================================ --}}

    <div>

        <h1 class="text-2xl font-semibold text-gray-900">
            Add Attribute Group
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Create a new group for organizing attributes.
        </p>

    </div>


    {{-- ============================================================
        FORM
    ============================================================ --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <form
            action="{{ route('admin.settings.attribute-groups.store') }}"
            method="POST"
            class="p-6 flex flex-col gap-6"
        >

            @csrf


            {{-- ====================================================
                NAME
            ===================================================== --}}

            <div>

                <label
                    for="name"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Group Name
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Internal default name of the attribute group.
                </p>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
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
                        text-gray-900
                        outline-none
                        transition
                        focus:bg-white
                        focus:border-gray-400
                        focus:ring-2
                        focus:ring-gray-100
                    "
                    placeholder="e.g. Materials"
                >

                @error('name')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                CODE
            ===================================================== --}}

            <div>

                <label
                    for="code"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Code
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Unique internal code used to identify the group.
                </p>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code') }}"
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
                    placeholder="e.g. materials"
                >

                @error('code')
                    <span class="block mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- ====================================================
                STATUS
            ===================================================== --}}

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
                        @selected(old('is_active', '1') == '1')
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        @selected(old('is_active') === '0')
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


            {{-- ====================================================
                SORT ORDER
            ===================================================== --}}

            <div>

                <label
                    for="sort_order"
                    class="block text-[13px] font-semibold text-gray-800"
                >
                    Sort Order
                </label>

                <p class="mt-1 text-[11px] text-gray-400">
                    Defines the order in which groups are displayed.
                </p>

                <input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    value="{{ old('sort_order', 0) }}"
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


            {{-- ====================================================
                OWNER
            ===================================================== --}}

            <div class="border-t border-gray-100 pt-6">

                <div class="mb-4">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Ownership
                    </h3>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Leave empty to create a system attribute group.
                    </p>

                </div>


                {{-- OWNER TYPE --}}

                <div>

                    <label
                        for="owner_type"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Owner Type
                    </label>

                    <select
                        name="owner_type"
                        id="owner_type"
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
                            System Group
                        </option>

                        <option
                            value="App\Models\User"
                            @selected(old('owner_type') === 'App\Models\User')
                        >
                            User
                        </option>

                        <option
                            value="App\Models\Supplier"
                            @selected(old('owner_type') === 'App\Models\Supplier')
                        >
                            Supplier
                        </option>

                        <option
                            value="App\Models\Buyer"
                            @selected(old('owner_type') === 'App\Models\Buyer')
                        >
                            Buyer
                        </option>

                    </select>

                    @error('owner_type')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                {{-- OWNER ID --}}

                <div class="mt-4">

                    <label
                        for="owner_id"
                        class="block text-[13px] font-semibold text-gray-800"
                    >
                        Owner ID
                    </label>

                    <input
                        type="number"
                        name="owner_id"
                        id="owner_id"
                        value="{{ old('owner_id') }}"
                        min="1"
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
                        placeholder="Leave empty for system group"
                    >

                    @error('owner_id')
                        <span class="block mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>


            {{-- ====================================================
                ACTIONS
            ===================================================== --}}

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
                    href="{{ route('admin.settings.attribute-groups.index') }}"
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
                    Create Attribute Group
                </button>

            </div>

        </form>

    </div>

</div>

@endsection