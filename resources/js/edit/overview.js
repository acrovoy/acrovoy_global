let selectedBusinessTypes = [];

export function initBusinessDrawer() {

    const selectedBusinessContainer = document.getElementById('selected-business-types');
    const businessOptions = document.querySelectorAll('.business-type-option');
    const businessInput = document.getElementById('businessTypesSelectedInput');
    const businessSearch = document.getElementById('businessTypeSearch');
    const initialBusiness = document.getElementById('initial-business-types');

    if (!selectedBusinessContainer) {
        return;
    }

    /* ===========================
     * Initialize from hidden input
     * =========================== */

    selectedBusinessTypes = [];

    if (initialBusiness && initialBusiness.value) {

        try {

            selectedBusinessTypes = JSON.parse(initialBusiness.value);

        } catch (e) {

            console.error('Failed to parse initial business types', e);

            selectedBusinessTypes = [];

        }

    }

    /* ===========================
     * Sync hidden input
     * =========================== */

    function updateBusinessInput() {

        businessInput.value = selectedBusinessTypes
            .map(item => item.id)
            .join(',');

    }

    /* ===========================
     * Render selected chips
     * =========================== */

    function renderBusinessTypes() {

        selectedBusinessContainer.innerHTML = '';

        if (!selectedBusinessTypes.length) {

            selectedBusinessContainer.innerHTML = `
                <span class="text-sm text-gray-400">
                    No business types selected.
                </span>
            `;

            return;

        }

        selectedBusinessTypes.forEach((item, index) => {

            const chip = document.createElement('div');

            chip.className =
                'inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700';

            chip.innerHTML = `
                <span>${item.name}</span>

                <button
                    type="button"
                    class="text-blue-500 hover:text-red-500 transition">

                    &times;

                </button>
            `;

            chip.querySelector('button').addEventListener('click', () => {

                selectedBusinessTypes.splice(index, 1);

                updateBusinessInput();

                renderBusinessTypes();

            });

            selectedBusinessContainer.appendChild(chip);

        });

    }

    /* ===========================
     * Add business type
     * =========================== */

    businessOptions.forEach(button => {

        button.addEventListener('click', () => {

            const id = button.dataset.id;
            const name = button.dataset.name;

            const exists = selectedBusinessTypes.find(item => item.id == id);

            if (exists) {
                return;
            }

            selectedBusinessTypes.push({
                id,
                name
            });

            updateBusinessInput();

            renderBusinessTypes();

        });

    });

    /* ===========================
     * Search
     * =========================== */

    if (businessSearch) {

        businessSearch.addEventListener('input', function () {

            const value = this.value.toLowerCase();

            businessOptions.forEach(button => {

                const visible = button.dataset.name
                    .toLowerCase()
                    .includes(value);

                button.style.display = visible ? '' : 'none';

            });

        });

    }

    renderBusinessTypes();

    updateBusinessInput();

}