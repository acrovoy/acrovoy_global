const input = document.getElementById('product-search');
const results = document.getElementById('product-search-results');

if (input && results) {

    let timer = null;

    input.addEventListener('input', () => {

        clearTimeout(timer);

        const query = input.value.trim();

        if (query.length < 2) {
            results.classList.add('hidden');
            results.innerHTML = '';
            return;
        }

        timer = setTimeout(async () => {

            try {

                const response = await fetch(
                    `${window.location.pathname}/search-products?q=${encodeURIComponent(query)}`,
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    }
                );

                const products = await response.json();

                results.innerHTML = '';

                if (!products.length) {

                    results.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500">
                            No products found.
                        </div>
                    `;

                    results.classList.remove('hidden');
                    return;
                }

                products.forEach(product => {

                    results.insertAdjacentHTML('beforeend', `
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 hover:bg-gray-50 product-result"
                            data-id="${product.id}">

                            <img
                                src="${product.image ?? '/images/no-image.png'}"
                                class="h-12 w-12 rounded-lg border object-cover">

                            <div class="flex-1 text-left">

                                <div class="font-medium text-gray-900">
                                    ${product.name}
                                </div>

                                <div class="text-xs text-gray-500">
                                    ${product.sku ?? ''}
                                </div>

                            </div>

                            <span class="text-sm text-blue-600">
                                Add
                            </span>

                        </button>
                    `);

                });

                results.classList.remove('hidden');

            } catch (e) {

                console.error(e);

            }

        }, 300);

    });

    document.addEventListener('click', e => {

        if (
            !results.contains(e.target) &&
            e.target !== input
        ) {
            results.classList.add('hidden');
        }

    });

}