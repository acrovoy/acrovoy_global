


    {{-- Top Filter Bar --}}
    <form method="GET"
        action="{{ route('supplier.show', $supplier) }}"
        class="mb-8 rounded-2xl border border-gray-100 bg-white px-6 py-5 shadow-sm">

        <input type="hidden" name="tab" value="products">
        {{-- Сохраняем текущую категорию --}}
        @if(request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        @if(request('material'))
        @foreach((array) request('material') as $id)
        <input type="hidden" name="material[]" value="{{ $id }}">
        @endforeach
        @endif

        @if(request('origin'))
        @foreach((array) request('origin') as $id)
        <input type="hidden" name="origin[]" value="{{ $id }}">
        @endforeach
        @endif

        @if(request('moq'))
        <input type="hidden" name="moq" value="{{ request('moq') }}">
        @endif

      



        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            {{-- Left: Results count + Reset --}}
            <div class="flex items-center gap-4 text-sm text-gray-600 tracking-wide">
                <span>
                    Showing
                    <span class="font-semibold text-gray-900">
                        {{ $products->count() }}
                    </span> product(s)
                </span>
                <a href="{{ route('supplier.show', $supplier->slug) }}?tab=products"
                    class="text-sm text-orange-800 hover:text-gray-900 transition underline">
                    Reset all filters
                </a>
            </div>



            <div class="flex items-center gap-3">

                <label class="text-sm text-gray-500">
                    Sort by
                </label>

                <select
                    name="sort"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm">

                    <option value="featured" @selected(request('sort')=='featured' )>
                        Featured
                    </option>

                    <option value="newest" @selected(request('sort')=='newest' )>
                        Newest
                    </option>

                    <option value="price_asc" @selected(request('sort')=='price_asc' )>
                        Price: Low to High
                    </option>

                    <option value="price_desc" @selected(request('sort')=='price_desc' )>
                        Price: High to Low
                    </option>

                </select>

            </div>



        </div>


</form>


{{-- Product Grid --}}
<div class="grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3  xl:grid-cols-4 gap-8">
    @if($products->count())

    @include('supplier.sections.product-grid')

    @else

    <div class="col-span-full flex min-h-[400px] items-center justify-center">

        <div class="text-center">

            <h2 class="mb-2 text-2xl font-bold text-brown-900 md:text-3xl">
                No products found.
            </h2>

            <p class="mx-auto max-w-md text-gray-600">
                This supplier has not published any products yet. Please check back later.
            </p>
            

        </div>

    </div>

    @endif
</div>



</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.wishlist-toggle').forEach(button => {

        button.addEventListener('click', async function () {

            const productId = this.dataset.productId;

            try {

                const response = await fetch(
                    `/buyer/wishlist/toggle/${productId}`,
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,

                            'Accept': 'application/json'
                        }
                    }
                );

                const data = await response.json();


                if (!data.status) return;


                const icon = this.querySelector('.wishlist-icon');


                if (data.status === 'added') {

    icon.setAttribute('fill', 'currentColor');
    icon.classList.add('text-red-500');

} else {

    icon.setAttribute('fill', 'none');
    icon.classList.remove('text-red-500');
}


                // обновляем badge
                updateWishlistBadge();

            }

            catch (error) {

                console.error(error);

            }

        });

    });

});

</script>
<script>

async function updateWishlistBadge() {

    try {

        const response =
            await fetch('/buyer/wishlist/count');

        const data =
            await response.json();


        const badge =
            document.querySelector('#wishlist-count');


        if (!badge) return;


        if (data.count > 0) {

            badge.textContent =
                data.count;

            badge.classList.remove('hidden');

        }

        else {

            badge.classList.add('hidden');

        }

    }

    catch(error) {

        console.error(error);

    }

}

</script>


