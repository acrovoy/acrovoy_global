 {{-- BACK --}}
    <a href="
        @if($isBuyer ?? false)
           {{ route('buyer.rfqs.index') }} 
        @else
            {{ route('supplier.rfqs.index') }}
        @endif
    "
    class="text-sm text-gray-500 hover:text-gray-900 transition">
    
        ← Back to RFQs
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

                <div class="text-sm font-semibold text-gray-900">
                    {{ $selectedCategory->name }}
                </div>

                <div class="text-xs text-gray-500">
                    Fill in technical requirements for this category
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

            {{-- ACTIONS --}}
            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">

                <div class="text-xs text-gray-400">
                    Requirements define what suppliers must respond to
                </div>

                <button class="px-4 py-2 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800 transition">
                    Save Requirements
                </button>

            </div>

        </form>

    @endif

</div>