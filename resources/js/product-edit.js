document.addEventListener('DOMContentLoaded', () => {

    

   /* ===========================
 * MATERIALS FROM DB (CHIPS)
 * =========================== */

const selectedMaterialsContainer = document.getElementById('selected-materials');
const materialsOptions = document.querySelectorAll('.material-option');
const selectedMaterialsInput = document.getElementById('materialsSelectedInput');
const materialSearch = document.getElementById('materialSearch');

let selectedMaterials = [];

/**
 * 🔹 INIT FROM HIDDEN INPUT (EDIT MODE)
 */
function initSelectedMaterials() {
    if (!selectedMaterialsInput.value) return;

    const ids = selectedMaterialsInput.value.split(',');

    ids.forEach(id => {
        const btn = document.querySelector(`.material-option[data-id="${id}"]`);
        if (btn) {
            selectedMaterials.push({
                id: String(id),
                name: btn.dataset.name
            });
        }
    });

    renderSelectedMaterials();
}

/**
 * 🔹 Render chips
 */
function renderSelectedMaterials() {
    selectedMaterialsContainer.innerHTML = '';

    selectedMaterials.forEach((m, i) => {
        const chip = document.createElement('div');
        chip.className = 'px-2 py-1 bg-blue-100 rounded-full text-xs flex gap-1';

        chip.innerHTML = `
            <span>${m.name}</span>
            <button type="button">&times;</button>
        `;

        chip.querySelector('button').onclick = () => {
            selectedMaterials.splice(i, 1);
            updateSelectedMaterialsInput();
            renderSelectedMaterials();
        };

        selectedMaterialsContainer.appendChild(chip);
    });
}

/**
 * 🔹 Update hidden input
 */
function updateSelectedMaterialsInput() {
    selectedMaterialsInput.value = selectedMaterials.map(m => m.id).join(',');
}

/**
 * 🔹 Click on material
 */
materialsOptions.forEach(btn => {
    btn.addEventListener('click', () => {
        const id = String(btn.dataset.id);

        if (!selectedMaterials.find(m => m.id === id)) {
            selectedMaterials.push({
                id: id,
                name: btn.dataset.name
            });

            updateSelectedMaterialsInput();
            renderSelectedMaterials();
        }
    });
});

/**
 * 🔹 Search
 */
materialSearch?.addEventListener('input', () => {
    const q = materialSearch.value.toLowerCase();

    materialsOptions.forEach(btn => {
        btn.style.display = btn.dataset.name.toLowerCase().includes(q)
            ? 'inline-flex'
            : 'none';
    });
});

/**
 * 🔹 BOOTSTRAP
 */
initSelectedMaterials();





    /* ===========================
  * PRICE TIERS
  * =========================== */
    let priceTierIndex =
    document.querySelectorAll('[id^="price-tier-"]').length;

    window.addPriceTier = function () {
        const container = document.getElementById('price-tiers');
        if (!container) return;

        const html = `
        <div class="grid grid-cols-3 gap-4 items-center" id="price-tier-${priceTierIndex}">
            <input type="number"
                   name="price_tiers[${priceTierIndex}][min_qty]"
                   placeholder="Min Qty"
                   class="input">

            <input type="number"
                   name="price_tiers[${priceTierIndex}][max_qty]"
                   placeholder="Max Qty"
                   class="input">

            <div class="flex gap-2">
                <input type="number"
                       name="price_tiers[${priceTierIndex}][price]"
                       placeholder="Unit Price $"
                       class="input flex-1">

               <button type="button"
        onclick="removePriceTier(${priceTierIndex})"
        class="text-red-600 font-bold hover:text-red-800">
    ✕
</button>
            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', html);
        priceTierIndex++;
    };

    window.removePriceTier = index => {
        const el = document.getElementById(`price-tier-${index}`);
        if (el) el.remove();
    };





});
