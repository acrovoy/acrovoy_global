const initial = document.getElementById('initial-capabilities');

let selectedCapabilities = [];

if (initial) {

    try {

        selectedCapabilities = JSON.parse(initial.value);

    } catch {

        selectedCapabilities = [];

    }

}

export function initManufacturingDrawer() {

    const selectedContainer =
        document.getElementById('selected-capabilities');

    const options =
        document.querySelectorAll('.capability-option');

    const selectedInput =
        document.getElementById('capabilitiesSelectedInput');

    const search =
        document.getElementById('capabilitySearch');

    if (!selectedContainer) {
        return;
    }

    selectedCapabilities = [];

    const initial = document.getElementById('initial-capabilities');

    if (initial) {

        try {

            selectedCapabilities = JSON.parse(initial.value);

        } catch {

            selectedCapabilities = [];

        }

    }

    function updateInput() {

        selectedInput.value = selectedCapabilities
            .map(item => item.id)
            .join(',');

    }

    function render() {

        selectedContainer.innerHTML = '';

        selectedCapabilities.forEach((item, index) => {

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

            chip.querySelector('button')
                .addEventListener('click', () => {

                    selectedCapabilities.splice(index, 1);

                    updateInput();

                    render();

                });

            selectedContainer.appendChild(chip);

        });

    }

    options.forEach(button => {

        button.addEventListener('click', () => {

            const id = button.dataset.id;
            const name = button.dataset.name;

            const exists = selectedCapabilities.find(
                item => item.id == id
            );

            if (exists) {
                return;
            }

            selectedCapabilities.push({
                id,
                name
            });

            updateInput();

            render();

        });

    });

    if (search) {

        search.addEventListener('input', function () {

            const value = this.value.toLowerCase();

            options.forEach(button => {

                const visible = button.dataset.name
                    .toLowerCase()
                    .includes(value);

                button.style.display = visible ? '' : 'none';

            });

        });

    }

    render();

    updateInput();

}