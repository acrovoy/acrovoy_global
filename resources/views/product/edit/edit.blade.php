@extends('dashboard.layout')

@section('dashboard-content')


<div>
    <h2 class="text-2xl font-semibold text-gray-900">Edit Product</h2>
    <p class="text-sm text-gray-500 mb-4">
        Manage all your products, edit details, prices, and inventory.
    </p>
</div>

<x-alerts />

<div class="bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
    <div class="p-6">





        @php
        $languages = \App\Models\Language::where('is_active', true)->get();
        @endphp

        @if($steps == 1)
        <div>
            @include('product.edit.sections.step1')
        </div>
        @endif

        @if($steps == 2)
        <div>

            @include('product.edit.sections.step2')

        </div>
        @endif

        @if($steps == 3)
        <div>

            @include('product.edit.sections.step3')

        </div>
        @endif

        @if($steps == 4)
        <div>
            @include('product.edit.sections.step4')
        </div>

        @endif

        @if($steps == 5)
        <div>

            @include('product.edit.sections.step5')

        </div>
        @endif

        @if($steps == 6)
        <div>

            @include('product.edit.sections.step6')

        </div>
        @endif

        @if($steps == 7)
        <div>

            @include('product.edit.sections.step7')

        </div>
        @endif




    </div>
</div>



{{-- DRAWER --}}
<div id="attribute-drawer"
    class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl z-50
           transform translate-x-full transition-transform duration-300
           flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b bg-gray-50">

        <h3 class="text-lg font-semibold text-gray-900" id="attribute-title">
            Create attribute
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Create a reusable attribute that can be attached to your products.
        </p>

    </div>

    <form method="POST"
        action="{{ route('custom-attributes.store')}}"
        class="flex flex-col flex-1">

        @csrf

        <input type="hidden" name="id" id="attr-id">

        {{-- BODY --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

            {{-- GROUP --}}
            <div>

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Group
                </label>

                <select
                    id="attr-group-id"
                    name="group_id"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10">

                    <option value="">— No group —</option>

                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group->id }}">
                            {{ $group->name ?? $group->code }}
                        </option>
                    @endforeach

                </select>

            </div>

            {{-- OR CREATE NEW GROUP --}}
            <div>

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Or create new group
                </label>

                <input
                    type="text"
                    id="attr-group-name"
                    name="group_name"
                    placeholder="New group name"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10">

            </div>

            {{-- TYPE --}}
            <input type="hidden" name="type" value="text">

            {{-- KEY --}}
            <div>

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Attribute name
                </label>

                <input
                    type="text"
                    id="attr-key"
                    name="key"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10">

            </div>

            <!-- {{-- VALUE --}}
            <div id="drawer-value" class="mb-3">
                <label class="text-xs text-gray-500">Value</label>
                <input type="text" id="attr-value" name="value"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div> -->

            {{-- OPTIONS --}}
            <div id="drawer-options" class="hidden">

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Options
                </label>

                <div id="options-container" class="space-y-2 mt-2"></div>

                <button
                    type="button"
                    onclick="addDrawerOption()"
                    class="mt-3 text-sm text-gray-600 hover:text-gray-900 transition">

                    + Add option

                </button>

            </div>

        </div>

        {{-- FOOTER --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

            <button
                type="button"
                onclick="closeAttributeDrawer()"
                class="px-4 py-2 text-sm rounded-lg border border-gray-200
                       text-gray-600 hover:bg-gray-50 transition">

                Cancel

            </button>

            <button
                type="submit"
                class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                       hover:bg-gray-800 transition shadow-sm">

                Save

            </button>

        </div>

    </form>

</div>


{{-- DRAWER: attach existing attributes --}}
<div id="attribute-picker-drawer"
    class="fixed right-0 top-0 h-full w-[460px]
            bg-white shadow-2xl z-50
            transform translate-x-full transition-transform duration-300
            flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <div class="flex justify-between items-start gap-4">

            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Add attributes
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Select existing attributes to attach to this product.
                </p>
            </div>

            <div class="flex items-center gap-2">

                <button type="button"
                    onclick="openDrawer('attribute-drawer')"
                    class="inline-flex items-center gap-2 px-4 py-2
                           text-sm font-medium text-gray-700
                           bg-white border border-gray-200
                           rounded-lg
                           hover:bg-gray-50 hover:border-gray-300
                           transition shadow-sm">

                    <span class="text-lg leading-none">+</span>
                    <span>Create attribute</span>

                </button>

            </div>

        </div>
    </div>

    <!-- route('products.attributes.attach') -->
    <form method="POST"
        action="{{ route('products.attach-attributes', $product->id) }}"
        class="flex flex-col flex-1">

        @csrf

        {{-- BODY --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-3">

            @foreach($availableAttributesGrouped as $groupName => $attrs)

                @php
                    $isGeneral = strtolower($groupName) === 'general';
                @endphp

                <div class="border border-gray-200 rounded-lg overflow-hidden">

                    {{-- GROUP HEADER --}}
                    <button type="button"
                        class="w-full flex justify-between items-center px-4 py-3 text-left
                               bg-gray-50 hover:bg-gray-100 transition"
                        onclick="toggleAttrGroup(this)">

                        <div class="flex items-center gap-2">

                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">
                                {{ $groupName }}
                            </div>

                            <div class="text-[10px] px-2 py-[1px] rounded bg-white border border-gray-200 text-gray-600">
                                {{ count($attrs) }}
                            </div>

                        </div>

                        <span class="text-xs text-gray-500 arrow">
                            {{ $isGeneral ? '▲' : '▼' }}
                        </span>

                    </button>

                    {{-- GROUP BODY --}}
                    <div class="{{ $isGeneral ? '' : 'hidden' }} p-2 space-y-2 bg-white">

                        @foreach($attrs as $attr)

                            <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50">

                                <input
                                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/10"
                                    type="checkbox"
                                    name="attributes[]"
                                    value="{{ $attr->id }}"
                                    @if(in_array($attr->id, $attachedIds)) checked @endif
                                />

                                <div class="flex-1">

                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $attr->name }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-0.5 flex flex-wrap gap-1">

                                        @if(in_array($attr->type, ['select', 'multiselect']))

                                            @foreach($attr->options->take(5) as $option)

                                                <span class="px-1.5 py-[1px] rounded bg-gray-100 border border-gray-200">
                                                    {{ $option->translatedValue() }}
                                                </span>

                                            @endforeach

                                            @if($attr->options->count() > 5)

                                                <span class="text-gray-300">
                                                    +{{ $attr->options->count() - 5 }}
                                                </span>

                                            @endif

                                        @else



                                        @endif

                                    </div>

                                </div>

                            </label>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

        {{-- FOOTER --}}
<div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

    {{-- CANCEL --}}
    <button
        type="button"
        onclick="closeAllDrawers()"
        class="px-4 py-2 text-sm rounded-lg border border-gray-200
               text-gray-600 hover:bg-gray-50 transition">
        Cancel
    </button>

    {{-- RIGHT ACTIONS --}}
    <div class="flex items-center gap-2">

        {{-- DELETE --}}
        <button
            type="submit"
            formaction=""
            formmethod="POST"
            onclick="return confirm('Archive selected attributes?')"
            class="px-4 py-2 text-sm rounded-lg border border-red-200
                   text-red-600 hover:bg-red-50 transition">

            <span>Delete selected</span>
        </button>

        {{-- PRIMARY --}}
        <button
            type="submit"
            class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                   hover:bg-gray-800 transition shadow-sm">

            <span>Attach selected</span>
        </button>

    </div>

</div>

    </form>

</div>

<script>
    function openDrawer(id) {

        // закрываем все
        document.querySelectorAll('[id$="-drawer"]').forEach(el => {
            el.classList.add('translate-x-full');
        });

        // открываем нужный
        requestAnimationFrame(() => {
            document.getElementById(id)
                ?.classList.remove('translate-x-full');
        });
    }

    function closeDrawer(id) {
        document.getElementById(id)
            ?.classList.add('translate-x-full');
    }

    function closeAllDrawers() {
        document.querySelectorAll('[id$="-drawer"]').forEach(el => {
            el.classList.add('translate-x-full');
        });
    }
</script>

<script>
     function toggleAttrGroup(btn) {

         const body = btn.nextElementSibling;
         const arrow = btn.querySelector('.arrow');

         const isOpen = !body.classList.contains('hidden');

         if (isOpen) {
             body.classList.add('hidden');
             arrow.innerText = '▼';
         } else {
             body.classList.remove('hidden');
             arrow.innerText = '▲';
         }
     }
 </script>


<style>



</style>

<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>

@vite(['resources/js/product-edit.js'])

@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />

<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>