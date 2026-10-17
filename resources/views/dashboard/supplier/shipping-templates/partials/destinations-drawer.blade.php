{{-- DELIVERY DESTINATIONS DRAWER --}}
<div id="destinations-drawer-overlay"
     class="fixed inset-0 z-[60] hidden bg-black/40 backdrop-blur-sm transition-opacity">
</div>

<div id="destinations-drawer"
     class="fixed right-0 top-0 z-[60] flex h-full w-full max-w-[460px]
            translate-x-full flex-col bg-white shadow-2xl
            transition-transform duration-300">

    {{-- HEADER --}}
    <div class="border-b bg-gray-50 px-6 py-5">
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
                    id="destinations-close"
                    aria-label="Close drawer"
                    class="shrink-0 text-xl leading-none text-gray-400 transition hover:text-gray-700">
                &times;
            </button>
        </div>
    </div>

    {{-- FORM --}}
    <form id="destinations-form"
          method="POST"
          action=""
          class="flex flex-1 flex-col overflow-hidden">
        @csrf

        <input type="hidden"
               name="template_id"
               id="destinations-template-id">

        {{-- BODY --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">
            {{-- Вставь сюда существующее дерево локаций из формы
                 create/edit Shipping Template.
                 Сохрани имена checkbox: locations[] --}}
            @include('components.location-tree', [
    'locations' => $countries,
    'selectedLocations' => [],
])
        </div>

        {{-- FOOTER --}}
        <div class="flex items-center justify-between gap-3 border-t bg-white px-6 py-4">
            <button type="button"
                    id="destinations-cancel"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50">
                Cancel
            </button>

            <button type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white shadow-sm transition hover:bg-gray-800">
                Save destinations
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const drawer = document.getElementById('destinations-drawer');
    const overlay = document.getElementById('destinations-drawer-overlay');
    const form = document.getElementById('destinations-form');
    const templateInput = document.getElementById('destinations-template-id');

    if (!drawer || !overlay || !form || !templateInput) return;

    const templateLocations = @json(
        $templates->mapWithKeys(fn ($template) => [
            $template->id => $template->locations->pluck('id')->values(),
        ])
    );

    const syncLocationsBaseUrl = @json(
        route('supplier.shipping-templates.sync-locations', ['template' => '__ID__'])
    );

    function openDestinationsDrawer(templateId) {
        const selectedIds = (templateLocations[templateId] || []).map(String);

        templateInput.value = templateId;
        form.action = syncLocationsBaseUrl.replace('__ID__', templateId);

        form.querySelectorAll('input[name="locations[]"]').forEach(input => {
            input.checked = selectedIds.includes(String(input.value));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        overlay.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
        document.body.classList.add('overflow-hidden');
    }

    function closeDestinationsDrawer() {
        drawer.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');

        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    document.addEventListener('click', event => {
        const button = event.target.closest('.open-destinations-drawer');
        if (!button) return;

        event.preventDefault();
        openDestinationsDrawer(button.dataset.templateId);
    });

    document.getElementById('destinations-close')
        ?.addEventListener('click', closeDestinationsDrawer);

    document.getElementById('destinations-cancel')
        ?.addEventListener('click', closeDestinationsDrawer);

    overlay.addEventListener('click', closeDestinationsDrawer);

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            !drawer.classList.contains('translate-x-full')
        ) {
            closeDestinationsDrawer();
        }
    });
});
</script>