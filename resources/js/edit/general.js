let selectedExportMarkets = [];

export function initGeneralDrawer() {

    const selectedExportContainer = document.getElementById('selected-export-markets');
    const exportOptions = document.querySelectorAll('.export-market-option');
    const exportSelectedInput = document.getElementById('exportMarketsSelectedInput');
    const exportSearch = document.getElementById('exportMarketSearch');
    const initial = document.getElementById('initial-export-markets');

    if (!selectedExportContainer) {
        return;
    }

    /* ===========================
     * Initialize from hidden input
     * =========================== */

    selectedExportMarkets = [];

    if (initial && initial.value) {

        try {

            selectedExportMarkets = JSON.parse(initial.value);

        } catch (e) {

            console.error('Failed to parse initial export markets', e);

            selectedExportMarkets = [];

        }

    }

    /* ===========================
     * Sync hidden input
     * =========================== */

    function updateExportInput() {

        exportSelectedInput.value = selectedExportMarkets
            .map(item => item.id)
            .join(',');

    }

    /* ===========================
     * Render selected chips
     * =========================== */

    function renderExportMarkets() {

        selectedExportContainer.innerHTML = '';

        if (!selectedExportMarkets.length) {

            selectedExportContainer.innerHTML = `
                <span class="text-sm text-gray-400">
                    No export markets selected.
                </span>
            `;

            return;

        }

        selectedExportMarkets.forEach((item, index) => {

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

                selectedExportMarkets.splice(index, 1);

                updateExportInput();

                renderExportMarkets();

            });

            selectedExportContainer.appendChild(chip);

        });

    }

    /* ===========================
     * Add market
     * =========================== */

    exportOptions.forEach(button => {

        button.addEventListener('click', () => {

            const id = button.dataset.id;
            const name = button.dataset.name;

            const exists = selectedExportMarkets.find(item => item.id == id);

            if (exists) {
                return;
            }

            selectedExportMarkets.push({
                id,
                name
            });

            updateExportInput();

            renderExportMarkets();

        });

    });

    /* ===========================
     * Search
     * =========================== */

    if (exportSearch) {

        exportSearch.addEventListener('input', function () {

            const value = this.value.toLowerCase();

            exportOptions.forEach(button => {

                const visible = button.dataset.name
                    .toLowerCase()
                    .includes(value);

                button.style.display = visible ? '' : 'none';

            });

        });

    }

    renderExportMarkets();

    updateExportInput();

}