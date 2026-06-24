@php
$rfqStatus = $rfq->status;
$isReadonly = $rfqStatus->isPublished() || $rfqStatus->isClosed();
@endphp


{{-- BACK --}}
    <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
    class="text-sm text-gray-500 hover:text-gray-900 transition">
    
        ← Back to RFQ Overview
    </a>

    
<x-alerts />

 <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">

     {{-- HEADER --}}
     <div class="mb-5">

         <div class="text-sm text-gray-500">
             RFQ Requirements
         </div>

         <div class="text-lg font-semibold text-gray-900">
             Configure category requirements
         </div>

         <div class="text-xs text-gray-500 mt-1">
             Select a category and define specifications for suppliers
         </div>

         @if($selectedCategory)
         <div class="mt-2 text-xs text-green-600">
             Requirements are saved per RFQ category
         </div>
         @endif

     </div>


     {{-- CATEGORY SELECT (TOP) --}}
     @if(!$isReadonly)
     <form method="GET" class="mb-6">

         <input type="hidden" name="tab" value="requirements">

         <select name="category_id"
             onchange="this.form.submit()"
             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-gray-900">

             <option value="">Select category</option>

             @foreach($categories as $category)

             <option value="{{ $category->id }}"
                 @selected(optional($selectedCategory)->id == $category->id)
                 >
                 {{ $category->name }}
             </option>

             @endforeach

         </select>

     </form>
@else

<div class="mb-6">

    <div class="text-xs uppercase tracking-wide text-gray-400 mb-1">
        Category
    </div>

    <div class="w-full border border-gray-200 bg-gray-100 rounded-md px-3 py-2 text-sm text-gray-700">
        {{ $selectedCategory?->name ?? 'Not selected' }}
    </div>

</div>

@endif

     {{-- EMPTY STATE --}}
     @if(!$selectedCategory)

     <div class="text-sm text-gray-500">
         Please select a category to load requirements
     </div>

     @else

     {{-- FORM --}}
     <form method="POST"
         action="{{ route('buyer.rfqs.requirements.store', $rfq->id) }}">

         @csrf

         <input type="hidden" name="rfq_id" value="{{ $rfq->id }}">
         <input type="hidden" name="category_id" value="{{ $selectedCategory->id }}">

        
        {{-- CATEGORY TITLE --}}
<div class="mb-4 p-3 bg-gray-50 rounded-lg border">

    <div class="flex items-start justify-between gap-3">

        <div>
            <div class="text-sm font-semibold text-gray-900">
                {{ $selectedCategory->name }}
            </div>

            <div class="text-xs text-gray-500">
                Fill in technical requirements for this category
            </div>
        </div>
@if($rfq->status->isDraft())
        <button
            type="button"
            id="restore-all-attributes"
            class="text-xs px-3 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"
        >
            Restore all hidden attributes
        </button>
@else
@endif
    </div>

</div>

         

         {{-- ATTRIBUTES --}}
         <div class="space-y-5">

             @foreach($attributes as $attribute)
             @include('rfq.workspace.components.attribute-field', [
             'attribute' => $attribute
             ])
             @endforeach

         </div>


         @include('rfq.workspace.components.custom-attributes')



          {{-- ATTACHMENTS --}}
            <div class="border rounded-lg p-4 mb-6">

                <div class="font-medium mb-2">Attachments</div>

                <div class="text-xs text-gray-500 mb-3">
                    Upload relevant files including technical drawings...
                </div>

                <div class="flex items-center gap-3">

                    

                    <div class="w-12 h-12 border-dashed border rounded flex items-center justify-center text-gray-400">
                        +
                    </div>

                </div>

            </div>




            

         {{-- ACTIONS --}}
         <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">

             <div class="text-xs text-gray-400">
                 Requirements define what suppliers must respond to
             </div>

             @if(!$isReadonly)

<button
    type="submit"
    class="px-4 py-2 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800 transition">
    Save Requirements
</button>

@else

<button
    type="button"
    disabled
    class="px-4 py-2 text-sm bg-gray-200 text-gray-400 rounded-md cursor-not-allowed">
    Requirements Locked
</button>

@endif

         </div>

     </form>

     @endif


    


 </div>

 {{-- DRAWER create new attribute --}}
 <div id="attribute-drawer"
     class="fixed right-0 top-0 h-full w-[420px]
            bg-white shadow-xl z-50
            transform translate-x-full transition-transform duration-300 p-6">

     <h3 class="text-lg font-semibold mb-4" id="attribute-title">
         Create attribute
     </h3>

     <form method="POST" action="{{ route('rfqs.custom-attributes.store', $rfq->id) }}">
         @csrf

         <input type="hidden" name="id" id="attr-id">


         {{-- GROUP --}}
         <div class="mb-3">

             <label class="text-xs text-gray-500">Group</label>

             <select
                 id="attr-group-id"
                 name="group_id"
                 class="w-full border rounded-lg px-3 py-2 text-sm ">
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
                 class="w-full border rounded-lg px-3 py-2 text-sm">

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
                 onclick="closeAllDrawers()"
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
                 onclick="openAttributeDrawer()"
                 class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition mb-4">

                 + Create attribute
             </button>

         </div>
     </div>

     <form method="POST" action="{{ route('rfqs.custom-attributes.attach', $rfq->id) }}">
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
                   bg-gradient-to-r from-[#F7F3EA] via-[#EADCC5]/80 to-orange-200
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
                             value="{{ $attr->id }} ">

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

        <span class="italic text-gray-300">
            {{ ucfirst($attr->type) }}
        </span>

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

                 {{-- archive --}}
                 <button
                     type="submit"
                     formaction="{{ route('rfqs.custom-attributes.bulk-archive', $rfq) }}"
                     formmethod="POST"
                     onclick="return confirm('Archive selected attributes?')"
                     class="px-4 py-2 text-sm text-red-600 border border-red-600 rounded-lg hover:bg-red-500 hover:text-white transition">

                     Delete selected
                 </button>

                 <button type="button"
                     onclick="closeAllDrawers()"
                     class="text-sm  px-4 py-2 bg-gray-200 rounded-lg hover:text-gray-700 transition">

                     Cancel
                 </button>

                 <button class="px-4 py-2 text-sm bg-black text-white rounded-lg hover:bg-gray-800 transition">

                     Attach selected
                 </button>

             </div>

         </div>



     </form>
 </div>

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

 <script>
document.getElementById('restore-all-attributes')
    .addEventListener('click', async function () {

        const url = "{{ route('buyer.rfqs.requirements.restoreAll', $rfq->id) }}";

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (data.success) {
            location.reload(); // проще всего для начала
        }
    });
</script>