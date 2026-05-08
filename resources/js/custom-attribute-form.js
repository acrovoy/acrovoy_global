let optionIndex = 0;

/*
|--------------------------------------------------------------------------
| OPEN DRAWER (CREATE / EDIT)
|--------------------------------------------------------------------------
*/
function openAttributeDrawer(id = null, btn = null) {

    const overlay = document.getElementById('attribute-overlay');
    const drawer = document.getElementById('attribute-drawer');

    if (overlay) overlay.classList.remove('hidden');
    if (drawer) drawer.classList.remove('translate-x-full');

    const title = document.getElementById('attribute-title');

    // reset form
    document.getElementById('attr-id').value = '';
    document.getElementById('attr-key').value = '';
    document.getElementById('attr-value').value = '';
    document.getElementById('attr-type').value = 'text';

    document.getElementById('options-container').innerHTML = '';

    optionIndex = 0;

    /*
    |--------------------------------------------------------------------------
    | EDIT MODE
    |--------------------------------------------------------------------------
    */
    if (btn) {

        const row = btn.closest('.custom-row');

        const key = row.children[0]?.innerText?.trim() ?? '';
        const value = row.children[1]?.innerText?.trim() ?? '';
        const type = row.children[2]?.innerText?.trim() ?? 'text';

        document.getElementById('attr-key').value = key;
        document.getElementById('attr-value').value = value;
        document.getElementById('attr-type').value = type;

        title.innerText = 'Edit attribute';

    } else {

        title.innerText = 'Create attribute';
    }

    // ALWAYS ensure UI sync
    toggleDrawerOptions();
}

/*
|--------------------------------------------------------------------------
| CLOSE DRAWER
|--------------------------------------------------------------------------
*/
function closeAttributeDrawer() {

    const overlay = document.getElementById('attribute-overlay');
    const drawer = document.getElementById('attribute-drawer');

    if (overlay) overlay.classList.add('hidden');
    if (drawer) drawer.classList.add('translate-x-full');

}

/*
|--------------------------------------------------------------------------
| TOGGLE UI
|--------------------------------------------------------------------------
*/
function toggleDrawerOptions() {

    const typeEl = document.getElementById('attr-type');
    if (!typeEl) return;

    const type = typeEl.value;

    const options = document.getElementById('drawer-options');
    const value = document.getElementById('drawer-value');

    const isSelect = (type === 'select' || type === 'multiselect');

    if (options) options.classList.toggle('hidden', !isSelect);
    if (value) value.classList.toggle('hidden', isSelect);
}

/*
|--------------------------------------------------------------------------
| ADD OPTION
|--------------------------------------------------------------------------
*/
function addDrawerOption(val = '') {

    const container = document.getElementById('options-container');
    if (!container) return;

    const row = document.createElement('div');
    row.className = "flex items-center gap-2";

    row.innerHTML = `
        <input type="text"
               name="options[]"
               value="${val}"
               class="w-full border rounded px-2 py-1 text-xs">

        <button type="button"
                onclick="this.parentElement.remove()"
                class="text-red-500 text-xs">
            ✕
        </button>
    `;

    container.appendChild(row);
}

/*
|--------------------------------------------------------------------------
| INIT EVENTS
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', function () {

    const overlay = document.getElementById('attribute-overlay');

    if (overlay) {
        overlay.addEventListener('click', closeAttributeDrawer);
    }

});

/*
|--------------------------------------------------------------------------
| GLOBAL EXPORT
|--------------------------------------------------------------------------
*/
Object.assign(window, {
    openAttributeDrawer,
    closeAttributeDrawer,
    addDrawerOption,
    toggleDrawerOptions
});