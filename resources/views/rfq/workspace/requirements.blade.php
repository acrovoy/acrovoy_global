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

<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 mt-4">

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
                @disabled($rfq->customization)
                @unless($rfq->customization)
                    onchange="this.form.submit()"
                @endunless
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-gray-900
                    disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">

            <option value="">Select category</option>

            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(optional($selectedCategory)->id == $category->id)>
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
                    class="text-xs px-3 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap">
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
    class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl z-50
           transform translate-x-full transition-transform duration-300
           flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b bg-gray-50">

        <h3
            class="text-lg font-semibold text-gray-900"
            id="attribute-title">

            Create attribute

        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Create a custom attribute for this RFQ.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('rfqs.custom-attributes.store', $rfq->id) }}"
        class="flex flex-col flex-1">

        @csrf

        <input
            type="hidden"
            name="id"
            id="attr-id">


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

                    <option value="">
                        — No group —
                    </option>

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
            <div>

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Attribute Type
                </label>

                <select
                    id="attr-type"
                    name="type"
                    onchange="toggleDrawerOptions()"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10">

                    <option value="text">
                        Text
                    </option>

                    <option value="number">
                        Number
                    </option>

                    <option value="select">
                        Select
                    </option>

                    <option value="multiselect">
                        Multi Select
                    </option>

                </select>

            </div>


            {{-- KEY --}}
            <div>

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Name
                </label>

                <input
                    type="text"
                    id="attr-key"
                    name="key"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10">

            </div>


            {{-- OPTIONS --}}
            <div id="drawer-options" class="hidden">

                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    Options
                </label>

                <div
                    id="options-container"
                    class="space-y-2 mt-2">
                </div>

                <button
                    type="button"
                    onclick="addDrawerOption()"
                    class="mt-3 text-sm text-gray-600 hover:text-gray-900 transition">

                    + Add option

                </button>

            </div>

        </div>


        {{-- FOOTER --}}
        <div
            class="border-t bg-white px-6 py-4
                   flex items-center justify-between gap-2">

            <button
                type="button"
                onclick="closeAllDrawers()"
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
                    Select existing attributes to attach to this RFQ.
                </p>

            </div>


            <div class="flex items-center gap-2">

                <button
                    type="button"
                    onclick="openAttributeDrawer()"
                    class="inline-flex items-center gap-2 px-4 py-2
                           text-sm font-medium text-gray-700
                           bg-white border border-gray-200
                           rounded-lg
                           hover:bg-gray-50 hover:border-gray-300
                           transition shadow-sm">

                    <span class="text-lg leading-none">+</span>

                    <span>
                        Create attribute
                    </span>

                </button>

            </div>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('rfqs.custom-attributes.attach', $rfq->id) }}"
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
                    <button
                        type="button"
                        class="w-full flex justify-between items-center px-4 py-3 text-left
                               bg-gray-50 hover:bg-gray-100 transition"
                        onclick="toggleAttrGroup(this)">

                        <div class="flex items-center gap-2">

                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">
                                {{ $groupName }}
                            </div>

                            <div
                                class="text-[10px] px-2 py-[1px] rounded
                                       bg-white border border-gray-200 text-gray-600">

                                {{ count($attrs) }}

                            </div>

                        </div>


                        <span class="text-xs text-gray-500 arrow">
                            {{ $isGeneral ? '▲' : '▼' }}
                        </span>

                    </button>


                    {{-- GROUP BODY --}}
                    <div
                        class="{{ $isGeneral ? '' : 'hidden' }}
                               p-2 space-y-2 bg-white">


                        @foreach($attrs as $attr)

                            <div
                                class="flex items-start gap-2 p-2 rounded-lg
                                       hover:bg-gray-50 transition">


                                {{-- CHECKBOX --}}
                                <input
                                    class="mt-1 rounded border-gray-300
                                           text-gray-900
                                           focus:ring-gray-900/10"
                                    type="checkbox"
                                    name="attributes[]"
                                    value="{{ $attr->id }}"
                                    id="rfq-attribute-{{ $attr->id }}"
                                />


                                {{-- CONTENT --}}
                                <label
                                    for="rfq-attribute-{{ $attr->id }}"
                                    class="flex-1 cursor-pointer">

                                    <div class="text-sm font-medium text-gray-800">

                                        {{ $attr->name }}

                                    </div>


                                    <div
                                        class="text-xs text-gray-400 mt-0.5
                                               flex flex-wrap gap-1">


                                        @if(in_array($attr->type, ['select', 'multiselect']))

                                            @foreach($attr->options->take(5) as $option)

                                                <span
                                                    class="px-1.5 py-[1px] rounded
                                                           bg-gray-100
                                                           border border-gray-200">

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

                                </label>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>


        {{-- FOOTER --}}
        <div
            class="border-t bg-white px-6 py-4
                   flex items-center justify-between gap-2">


            {{-- LEFT --}}
            <div>

                <button
                    type="submit"
                    formaction="{{ route('rfqs.custom-attributes.bulk-archive', $rfq) }}"
                    formmethod="POST"
                    onclick="return confirm('Archive selected attributes?')"
                    class="px-4 py-2 text-sm rounded-lg
                           border border-red-200
                           text-red-600
                           hover:bg-red-50
                           transition">

                    Delete selected

                </button>

            </div>


            {{-- RIGHT --}}
            <div class="flex items-center gap-2">


                {{-- CANCEL --}}
                <button
                    type="button"
                    onclick="closeAllDrawers()"
                    class="px-4 py-2 text-sm rounded-lg
                           border border-gray-200
                           text-gray-600
                           hover:bg-gray-50
                           transition">

                    Cancel

                </button>


                {{-- PRIMARY --}}
                <button
                    type="submit"
                    class="px-4 py-2 text-sm rounded-lg
                           bg-gray-900 text-white
                           hover:bg-gray-800
                           transition shadow-sm">

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