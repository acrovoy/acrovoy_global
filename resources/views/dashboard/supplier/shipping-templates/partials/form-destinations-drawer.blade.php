{{-- FORM DELIVERY DESTINATIONS DRAWER --}}

@php
    $initialSelectedLocations = collect(
        old('locations', $selectedLocations ?? [])
    )->map(fn ($id) => (string) $id)->unique()->values();
@endphp

<div id="form-destinations-drawer-overlay"
     class="fixed inset-0 z-[70] hidden bg-black/40 backdrop-blur-sm transition-opacity">
</div>

<div id="form-destinations-drawer"
     class="fixed right-0 top-0 z-[70] flex h-full w-full max-w-[460px]
            translate-x-full flex-col bg-white shadow-2xl
            transition-transform duration-300"
     aria-hidden="true">

    {{-- HEADER --}}
    <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Delivery Destinations
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Select countries, regions and cities for this shipping template.
                </p>
            </div>

            <button type="button"
                    id="form-destinations-close"
                    aria-label="Close drawer"
                    class="shrink-0 text-xl leading-none text-gray-400 transition hover:text-gray-700">
                &times;
            </button>
        </div>
    </div>

    {{-- BODY --}}
    <div class="flex-1 overflow-y-auto px-6 py-5">
        <x-location-tree
            :locations="$countries"
            :selectedLocations="$initialSelectedLocations->all()"
        />
    </div>

    {{-- FOOTER --}}
    <div class="flex items-center justify-between gap-3 border-t border-gray-200 bg-white px-6 py-4">
        <button type="button"
                id="form-destinations-cancel"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
            Cancel
        </button>

        <button type="button"
                id="form-destinations-apply"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
            Apply destinations
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const drawer = document.getElementById('form-destinations-drawer');
    const overlay = document.getElementById('form-destinations-drawer-overlay');
    const openButton = document.getElementById('open-form-destinations-drawer');
    const closeButton = document.getElementById('form-destinations-close');
    const cancelButton = document.getElementById('form-destinations-cancel');
    const applyButton = document.getElementById('form-destinations-apply');
    const selectedInputs = document.getElementById('selected-destinations-inputs');
    const selectedCount = document.getElementById('selected-destinations-count');

    if (
        !drawer ||
        !overlay ||
        !openButton ||
        !closeButton ||
        !cancelButton ||
        !applyButton ||
        !selectedInputs
    ) {
        return;
    }

    const mainForm = document.getElementById('shippingTemplateForm');

    if (!mainForm) return;

    const locationCheckboxes = () =>
        drawer.querySelectorAll('input[name="locations[]"]');

    let savedSelection = [];

    function getSelectedIds() {
        return Array.from(locationCheckboxes())
            .filter(input => input.checked)
            .map(input => String(input.value));
    }

    function setSelectedIds(ids) {
        const selected = new Set(ids.map(String));

        locationCheckboxes().forEach(input => {
            input.checked = selected.has(String(input.value));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    function openDrawer() {
        savedSelection = getSelectedIds();

        overlay.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        window.setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300);
    }

    function updateMainForm(ids) {
    selectedInputs.replaceChildren();

    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'locations[]';
        input.value = id;
        selectedInputs.appendChild(input);
    });

    if (selectedCount) {
        selectedCount.textContent = `Selected locations: ${ids.length}`;
    }

    const destinationsList = document.getElementById('selected-destinations-list');
    if (!destinationsList) return;

    destinationsList.replaceChildren();

    if (ids.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'text-sm text-gray-400';
        empty.textContent = 'No destinations selected yet.';
        destinationsList.appendChild(empty);
        return;
    }

    const selectedIds = new Set(ids.map(String));
    const names = new Map();

    locationCheckboxes().forEach(input => {
        const id = String(input.value);
        if (!selectedIds.has(id)) return;

        const name = input.nextElementSibling?.textContent.trim();

        if (name) {
            names.set(id, {
                name,
                level: input.closest('.ml-2') ? 3 : 2
            });
        }
    });

    ids.forEach(id => {
        const location = names.get(String(id));
        if (!location) return;

        const tag = document.createElement('span');

        tag.className = location.level === 3
            ? 'px-2 py-1 text-xs rounded-full bg-green-200 text-green-800'
            : 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700';

        tag.textContent = location.name;
        destinationsList.appendChild(tag);
    });
}

    openButton.addEventListener('click', openDrawer);

    closeButton.addEventListener('click', () => {
        setSelectedIds(savedSelection);
        closeDrawer();
    });

    cancelButton.addEventListener('click', () => {
        setSelectedIds(savedSelection);
        closeDrawer();
    });

    overlay.addEventListener('click', () => {
        setSelectedIds(savedSelection);
        closeDrawer();
    });

    applyButton.addEventListener('click', () => {
        const ids = getSelectedIds();

        updateMainForm(ids);
        closeDrawer();
    });

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            !drawer.classList.contains('translate-x-full')
        ) {
            setSelectedIds(savedSelection);
            closeDrawer();
        }
    });

    // Keep the selected destinations synchronized after validation errors.
    updateMainForm(getSelectedIds());
});
</script>