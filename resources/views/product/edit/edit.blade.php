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
        <input type="hidden" name="type" value="text">


        {{-- KEY --}}
        <div class="mb-3">
            <label class="text-xs text-gray-500">Attribute name</label>
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
        class="inline-flex items-center gap-2 px-4 py-2
                        text-sm font-medium text-gray-700
                        bg-white border border-gray-200
                        rounded-lg
                        hover:bg-gray-50 hover:border-gray-300
                        active:scale-[0.98]
                        transition-all duration-150 shadow-sm">

        Cancel
    </button>

    <button type="submit"
        class="inline-flex items-center gap-2 px-4 py-2
               text-sm font-semibold text-white
               bg-gray-900 border border-gray-900
               rounded-lg
               hover:bg-gray-800 hover:border-gray-800
               active:scale-[0.98]
               transition-all duration-150 shadow-sm">

        Save
    </button>

</div>

    </form>
</div>


{{-- DRAWER: attach existing attributes --}}
 <div id="attribute-picker-drawer"
     class="fixed right-0 top-0 h-full w-[420px]
            bg-white shadow-xl z-50
            transform translate-x-full transition-transform duration-300 p-6">
     <div class="flex justify-between items-center">
         <h3 class="text-lg font-semibold mb-4">
             Add attributes
         </h3>
         <div class="flex items-center gap-2">

             <button type="button"
    onclick="openDrawer('attribute-drawer')"
    class="inline-flex items-center gap-2 px-4 py-2
           text-sm font-medium text-gray-700
           bg-white border border-gray-200
           rounded-lg
           hover:bg-gray-50 hover:border-gray-300
           active:scale-[0.98]
           transition-all duration-150 shadow-sm mb-4">

    <span class="text-lg leading-none">+</span>
    <span>Create attribute</span>

</button>

         </div>
     </div>

     <!-- route('products.attributes.attach') -->
     <form method="POST" action="{{ route('products.attach-attributes', $product->id) }}">
         @csrf

         <div class="space-y-2 max-h-[70vh] overflow-y-auto">

             @foreach($availableAttributesGrouped as $groupName => $attrs)

             @php
             $isGeneral = strtolower($groupName) === 'general';
             @endphp

             <div class="mb-3 border rounded-lg overflow-hidden">

                 {{-- GROUP HEADER --}}
                 <button type="button"
                     class="w-full flex justify-between items-center px-3 py-2 text-left
                   bg-gradient-to-r from-gray-100 to-gray-200
                   hover:from-[#F7F3EA] hover:via-[#EADCC5]/50 hover:to-orange-200
                   transition-colors duration-200"
                     onclick="toggleAttrGroup(this)">

                     <div class="flex items-center gap-2">

                         <div class="text-xs font-semibold text-gray-600 uppercase">
                             {{ $groupName }}
                         </div>

                         <div class="text-[10px] px-2 py-[1px] rounded bg-white text-gray-600">
                             {{ count($attrs) }}
                         </div>

                     </div>

                     <span class="text-xs text-white arrow">
                         {{ $isGeneral ? '▲' : '▼' }}
                     </span>

                 </button>

                 {{-- GROUP BODY --}}
                 <div class="{{ $isGeneral ? '' : 'hidden' }} p-2 space-y-2 bg-white">

                     @foreach($attrs as $attr)
                     <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50">

                         <input class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                             type="checkbox"
                             name="attributes[]"
                             value="{{ $attr->id }}"
                             @if(in_array($attr->id, $attachedIds)) checked @endif
    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
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

         <div class="mt-5 pt-4 flex items-center justify-between">

             {{-- LEFT ACTIONS --}}
             <div class="flex items-center gap-2">



             </div>

             {{-- RIGHT ACTIONS --}}
            <div class="flex items-center gap-2">

                {{-- DELETE --}}
                <button
                    type="submit"
                    formaction=""
                    formmethod="POST"
                    onclick="return confirm('Archive selected attributes?')"
                    class="inline-flex items-center gap-2 px-4 py-2
                        text-sm font-medium text-red-600
                        bg-red-50 border border-red-100
                        rounded-lg
                        hover:bg-red-100 hover:border-red-200
                        active:scale-[0.98]
                        transition-all duration-150 shadow-sm">

                    <span>Delete selected</span>
                </button>

                {{-- CANCEL --}}
                <button
                    type="button"
                    onclick="closeAllDrawers()"
                    class="inline-flex items-center gap-2 px-4 py-2
                        text-sm font-medium text-gray-700
                        bg-white border border-gray-200
                        rounded-lg
                        hover:bg-gray-50 hover:border-gray-300
                        active:scale-[0.98]
                        transition-all duration-150 shadow-sm">

                    <span>Cancel</span>
                </button>

                {{-- PRIMARY --}}
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2
                        text-sm font-semibold text-white
                        bg-black border border-black
                        rounded-lg
                        hover:bg-gray-800 hover:border-gray-800
                        active:scale-[0.98]
                        transition-all duration-150 shadow-sm">

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