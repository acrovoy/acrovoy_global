<div class="bg-white border rounded-xl p-4">

    <div class="mb-4 font-semibold text-gray-800">
        Categories
    </div>

    @if($categories->isEmpty())

        <div class="text-sm text-gray-500">
            No RFQ categories available
        </div>

    @else

        <div class="space-y-1">

            @foreach($categories as $category)

                <a href="{{ route('rfqs.workspace', [
                        'rfq' => $rfq->id,
                        'tab' => 'requirements',
                        'category_id' => $category->id
                    ]) }}"

                   class="block px-3 py-2 rounded-lg text-sm transition

                   @if(optional($selectedCategory)->id === $category->id)
                        bg-black text-white
                   @else
                        text-gray-700 hover:bg-gray-100
                   @endif
                   "

                >
                    {{ $category->name }}
                </a>

            @endforeach

        </div>

    @endif

</div>