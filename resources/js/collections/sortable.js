import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {

    const list = document.getElementById('collection-products-list');

    if (!list) {
        return;
    }

    new Sortable(list, {

        animation: 180,

        ghostClass: 'bg-blue-50',

        handle: '.drag-handle',

        onEnd() {

            const collectionId =
                document.getElementById('collection-id').value;

            const products = [...list.querySelectorAll('[data-product-id]')]
                .map((item, index) => ({
                    id: item.dataset.productId,
                    sort_order: index
                }));

            fetch(`/dashboard/admin/collections/${collectionId}/reorder`, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },

                body: JSON.stringify({
                    products
                })

            });

        }

    });

});