<x-alerts />

<form method="POST"
    action="{{ route('manufacturer.products.store') }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    @php
    $languages = \App\Models\Language::where('is_active', true)->get();
    @endphp

    {{-- Шаги формы --}}
    <div class="form-step" data-step="1">

        @include('dashboard.supplier.sections.step1')

    </div>

    <div class="form-step hidden" data-step="2">

        @include('dashboard.supplier.sections.step2')

    </div>

    <div class="form-step hidden" data-step="3">

        @include('dashboard.supplier.sections.step3')


    </div>

    
    <div class="form-step hidden" data-step="4">

        @include('dashboard.supplier.sections.step4')


    </div>


    <div class="form-step hidden" data-step="5">

        @include('dashboard.supplier.sections.step5')


    </div>




    <div class="form-step hidden" data-step="6">

        @include('dashboard.supplier.sections.step6')







    </div>

    <div class="form-step hidden" data-step="7">

        @include('dashboard.supplier.sections.step7')







    </div>


    {{-- Навигация между шагами --}}
    <div class="flex mt-6">
        <button type="button" id="prevBtn" class="bg-gray-300 px-6 py-2 rounded hidden">Назад</button>
        <button type="button" id="nextBtn" class="ml-auto bg-blue-800 text-white px-6 py-2 rounded">Далее</button>
        <button type="submit" id="submitBtn" class="ml-auto bg-green-600 text-white px-6 py-2 rounded hidden">Сохранить</button>
    </div>
</form>



{{-- DRAWER --}}
<div id="attribute-drawer"
    class="fixed right-0 top-0 h-full w-[420px] bg-white shadow-xl z-50
            transform translate-x-full transition-transform duration-300 p-6">

    <h3 class="text-lg font-semibold mb-4" id="attribute-title">
        Create attribute
    </h3>

    <form method="POST" action="{{ route('custom-attributes.store')}}">
        @csrf

        <input type="hidden" name="id" id="attr-id">




        {{-- GROUP --}}
<div class="mb-3">

    <label class="text-xs text-gray-500">Group</label>

    <select
        id="attr-group-id"
        name="group_id"
        class="w-full border rounded-lg px-3 py-2 text-sm"
    >
        <option value="">— No group —</option>

        @foreach($groups ?? [] as $group)
            <option value="{{ $group->id }}">
                {{ $group->name ?? $group->code }}
            </option>
        @endforeach
    </select>

</div>

{{-- OR CREATE NEW GROUP --}}
<div class="mb-3">

    <label class="text-xs text-gray-500">Or create new group</label>

    <input
        type="text"
        id="attr-group-name"
        name="group_name"
        placeholder="New group name"
        class="w-full border rounded-lg px-3 py-2 text-sm"
    >

</div>



        {{-- TYPE --}}
        <div class="mb-3">
            <label class="text-xs text-gray-500">Type</label>
            <select id="attr-type" name="type"
                onchange="toggleDrawerOptions()"
                class="w-full border rounded-lg px-3 py-2 text-sm">

                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="select">Select</option>
                <option value="multiselect">Multi Select</option>

            </select>
        </div>


        {{-- KEY --}}
        <div class="mb-3">
            <label class="text-xs text-gray-500">Code</label>
            <input type="text" id="attr-key" name="key"
                class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>



        <!-- {{-- VALUE --}}
        <div id="drawer-value" class="mb-3">
            <label class="text-xs text-gray-500">Value</label>
            <input type="text" id="attr-value" name="value"
                class="w-full border rounded-lg px-3 py-2 text-sm">
        </div> -->

        {{-- OPTIONS --}}
        <div id="drawer-options" class="hidden">

            <label class="text-xs text-gray-500">Options</label>

            <div id="options-container" class="space-y-2 mt-1"></div>

            <button type="button"
                onclick="addDrawerOption()"
                class="text-xs text-gray-500 mt-2">
                + Add option
            </button>

        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-2 mt-6">

            <button type="button"
                onclick="closeAttributeDrawer()"
                class="px-4 py-2 bg-gray-200 rounded-lg">
                Cancel
            </button>

            <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg">
                Save
            </button>

        </div>

    </form>
</div>


<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>