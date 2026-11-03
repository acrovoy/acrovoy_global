document.addEventListener('DOMContentLoaded', () => {
    const drawer = document.getElementById('manage-products-drawer');

    if (!drawer) return;

    const panel = drawer.querySelector('[data-drawer-panel]');
    const backdrop = drawer.querySelector('[data-drawer-backdrop]');
    const list = document.getElementById('manage-products-list');
    const searchInput = document.getElementById('manage-products-search');
    const saveButton = document.getElementById('save-community-products');
    const pagination = document.getElementById('manage-products-pagination');
    const paginationInfo = document.getElementById('manage-products-pagination-info');

    const state = {
        postId: null,
        tab: 'all',
        query: '',
        page: 1,
        lastPage: 1,
        total: 0,
        selected: new Map(),
        initialIds: new Set(),
        requestController: null,
        searchTimer: null,
        loading: false,
        saving: false,
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const endpoint = (attribute) => {
        return drawer.dataset[attribute].replace('__POST_ID__', state.postId);
    };

    const escapeText = (value) => String(value ?? '');

    const setLoading = (message = 'Loading products...') => {
        list.replaceChildren();

        const wrapper = document.createElement('div');
        wrapper.className = 'py-16 text-center text-sm text-stone-400';
        wrapper.textContent = message;

        list.appendChild(wrapper);
    };

    const updateSelectedCounters = () => {
        const count = state.selected.size;

        document.getElementById('selected-products-tab-count').textContent = count;

        document.getElementById('manage-products-selected-count').textContent =
            `${count} ${count === 1 ? 'product' : 'products'} selected`;

        const changed =
            count !== state.initialIds.size ||
            [...state.selected.keys()].some(id => !state.initialIds.has(id));

        saveButton.disabled = state.saving || !changed;
    };

    const setTab = (tab) => {
        state.tab = tab;

        drawer.querySelectorAll('[data-products-tab]').forEach(button => {
            const active = button.dataset.productsTab === tab;

            button.className = active
                ? 'rounded-md bg-stone-900 px-3 py-1.5 text-xs font-medium text-white'
                : 'rounded-md border border-stone-200 bg-white px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50';
        });

        searchInput.disabled = tab === 'selected';
        searchInput.classList.toggle('opacity-50', tab === 'selected');

        if (tab === 'selected') {
            renderSelected();
        } else {
            state.page = 1;
            loadProducts();
        }
    };

    const createProductRow = (product) => {
        const selected = state.selected.has(String(product.id));

        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 border-b border-stone-100 py-3';

        const image = document.createElement('img');
        image.src = product.image || '/images/no-image.png';
        image.alt = '';
        image.loading = 'lazy';
        image.className = 'h-16 w-16 shrink-0 rounded-lg border border-stone-100 bg-stone-50 object-cover';

        image.onerror = () => {
            image.src = '/images/no-image.png';
        };

        const details = document.createElement('div');
        details.className = 'min-w-0 flex-1';

        const name = document.createElement('div');
        name.className = 'truncate text-sm font-medium text-stone-800';
        name.textContent = escapeText(product.name);

        const meta = document.createElement('div');
        meta.className = 'mt-1 truncate text-xs text-stone-500';

        const category = escapeText(product.category);
        const supplier = escapeText(product.supplier);

        meta.textContent = [category, supplier].filter(Boolean).join(' · ');

        details.append(name, meta);

        const label = document.createElement('label');
        label.className = 'flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = selected;
        checkbox.className = 'h-4 w-4 rounded border-stone-300 text-stone-800 focus:ring-stone-400';

        checkbox.addEventListener('change', () => {
            const id = String(product.id);

            if (checkbox.checked) {
                state.selected.set(id, product);
            } else {
                state.selected.delete(id);
            }

            updateSelectedCounters();

            if (state.tab === 'selected') {
                renderSelected();
            } else {
                renderCurrentPageSelection();
            }
        });

        label.appendChild(checkbox);
        row.append(image, details, label);

        return row;
    };

    const renderCurrentPageSelection = () => {
        list.querySelectorAll('[data-product-row]').forEach(row => {
            const id = row.dataset.productRow;
            const checkbox = row.querySelector('input[type="checkbox"]');

            if (checkbox) {
                checkbox.checked = state.selected.has(id);
            }
        });
    };

    const renderProducts = (products) => {
        list.replaceChildren();

        if (!products.length) {
            setLoading(
                state.query
                    ? 'No products match your search.'
                    : 'No approved products found.'
            );
            return;
        }

        products.forEach(product => {
            const row = createProductRow(product);
            row.dataset.productRow = String(product.id);
            list.appendChild(row);
        });
    };

    const renderSelected = () => {
        pagination.classList.add('hidden');
        pagination.classList.remove('flex');

        document.getElementById('manage-products-results-count').textContent =
            `${state.selected.size} selected`;

        renderProducts([...state.selected.values()]);
    };

    const loadProducts = async () => {
        if (state.tab !== 'all') return;

        state.requestController?.abort();

        const controller = new AbortController();
        state.requestController = controller;

        state.loading = true;
        setLoading();

        pagination.classList.add('hidden');
        pagination.classList.remove('flex');

        try {
            const url = new URL(endpoint('searchUrl'), window.location.origin);

            url.searchParams.set('q', state.query);
            url.searchParams.set('page', state.page);
            url.searchParams.set('per_page', 20);

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Unable to load products.');
            }

            const data = await response.json();

            if (controller.signal.aborted) return;

            state.lastPage = data.pagination.last_page;
            state.total = data.pagination.total;

            document.getElementById('manage-products-results-count').textContent =
                `${state.total.toLocaleString()} results`;

            renderProducts(data.products);

            if (state.lastPage > 1) {
                pagination.classList.remove('hidden');
                pagination.classList.add('flex');

                paginationInfo.textContent =
                    `Page ${state.page} of ${state.lastPage}`;

                pagination.querySelector('[data-products-page="prev"]').disabled =
                    state.page <= 1;

                pagination.querySelector('[data-products-page="next"]').disabled =
                    state.page >= state.lastPage;
            }
        } catch (error) {
            if (error.name === 'AbortError') return;

            setLoading('Something went wrong while loading products.');
            console.error(error);
        } finally {
            if (state.requestController === controller) {
                state.loading = false;
            }
        }
    };

    const loadSelectedProducts = async () => {
        const response = await fetch(endpoint('indexUrl'), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Unable to load selected products.');
        }

        const data = await response.json();

        state.selected.clear();

        data.products.forEach(product => {
            state.selected.set(String(product.id), product);
        });

        state.initialIds = new Set(state.selected.keys());

        updateSelectedCounters();
    };

    const openDrawer = async (button) => {
        state.postId = button.dataset.postId;
        state.query = '';
        state.page = 1;
        state.tab = 'all';
        state.selected.clear();
        state.initialIds.clear();

        searchInput.value = '';

        document.getElementById('manage-products-post-title').textContent =
            button.dataset.postTitle || '';

        drawer.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
        });

        setLoading();

        try {
            await loadSelectedProducts();
            setTab('all');
        } catch (error) {
            setLoading('Unable to load the publication products.');
            console.error(error);
        }
    };

    const closeDrawer = () => {
        state.requestController?.abort();

        backdrop.classList.add('opacity-0');
        panel.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    };

    const saveProducts = async () => {
        if (state.saving) return;

        state.saving = true;
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';

        try {
            const response = await fetch(endpoint('syncUrl'), {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    product_ids: [...state.selected.keys()],
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || 'Unable to save products.'
                );
            }

            const countElement = document.querySelector(
                `[data-post-products-count="${state.postId}"]`
            );

            if (countElement) {
                countElement.textContent = data.products_count;
            }

            state.initialIds = new Set(state.selected.keys());

            closeDrawer();

        } catch (error) {
            alert(error.message);
            console.error(error);
        } finally {
            state.saving = false;
            saveButton.textContent = 'Save changes';
            updateSelectedCounters();
        }
    };

    document.addEventListener('click', event => {
        const manageButton = event.target.closest('[data-manage-products]');

        if (manageButton) {
            openDrawer(manageButton);
            return;
        }

        if (
            event.target.closest('[data-close-products-drawer]') ||
            event.target === backdrop
        ) {
            closeDrawer();
        }
    });

    drawer.querySelectorAll('[data-products-tab]').forEach(button => {
        button.addEventListener('click', () => {
            setTab(button.dataset.productsTab);
        });
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(state.searchTimer);

        state.searchTimer = setTimeout(() => {
            state.query = searchInput.value.trim();
            state.page = 1;
            loadProducts();
        }, 350);
    });

    pagination.querySelector('[data-products-page="prev"]')
        .addEventListener('click', () => {
            if (state.page > 1) {
                state.page--;
                loadProducts();
            }
        });

    pagination.querySelector('[data-products-page="next"]')
        .addEventListener('click', () => {
            if (state.page < state.lastPage) {
                state.page++;
                loadProducts();
            }
        });

    saveButton.addEventListener('click', saveProducts);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !drawer.classList.contains('hidden')) {
            closeDrawer();
        }
    });
});