document.addEventListener('DOMContentLoaded', () => {

    

 /* ===========================
 * MATERIAL COLOR / TEXTURE
 * =========================== */
let materialIndex = document.querySelectorAll('.material-item').length - 1;

window.initMaterial = function (index) {

    const preview = document.getElementById(`preview-${index}`);
    const colorInput = document.getElementById(`colorInput-${index}`);
    const fileInput = document.getElementById(`fileInput-${index}`);
    const removeBtn = document.getElementById(`removeMaterialBtn-${index}`);

    if (!preview || !colorInput || !fileInput || !window.Pickr) return;

    const pickr = Pickr.create({
        el: `#colorBtn-${index}`,
        theme: 'classic',
        default: colorInput.value || '#ffffff',
        useAsButton: true,
        components: {
            preview: true,
            opacity: false,
            hue: true,
            interaction: { input: true, save: true }
        }
    });

    pickr.on('save', color => {
        const hex = color.toHEXA().toString();
        colorInput.value = hex;
        preview.style.backgroundColor = hex;
        preview.style.backgroundImage = '';
        pickr.hide();
    });

    fileInput.addEventListener('change', () => {
        
        if (!fileInput.files.length) return;
        colorInput.value = '';
        const reader = new FileReader();
        reader.onload = e => {
            preview.style.backgroundImage = `url('${e.target.result}')`;
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
            preview.style.backgroundColor = '#fff';
        };
        reader.readAsDataURL(fileInput.files[0]);
    });

    removeBtn.addEventListener('click', () => {
        document.getElementById(`material-${index}`).remove();
    });
};

// Инициализация всех существующих материалов
document.querySelectorAll('.material-item').forEach((_, i) => initMaterial(i));

// Добавление нового материала
document.getElementById('addMaterialBtn')?.addEventListener('click', () => {
    
    materialIndex++;
    const wrapper = document.getElementById('materials-wrapper');

    wrapper.insertAdjacentHTML('beforeend', `
        <div class="flex items-center gap-4 mt-2 material-item" id="material-${materialIndex}">
            <div class="w-12 h-12 border rounded cursor-pointer" id="preview-${materialIndex}" data-link=""></div>
            <input type="hidden" name="newMaterials[${materialIndex}][color]" id="colorInput-${materialIndex}">
            <input type="file" name="newMaterials[${materialIndex}][texture]" class="hidden" id="fileInput-${materialIndex}">
            <button type="button" id="colorBtn-${materialIndex}" class="px-4 py-2 bg-blue-800 text-white rounded">Выбрать цвет</button>
            <button type="button" onclick="document.getElementById('fileInput-${materialIndex}').click()" class="px-4 py-2 bg-blue-800 text-white rounded">Выбрать файл</button>
            <span class="text-gray-500 px-2 flex items-center">🔗</span>
            <input type="number" name="newMaterials[${materialIndex}][linked_product_id]" placeholder="Product ID" class="w-32 px-2 py-1 border rounded text-sm" oninput="setMaterialLink(${materialIndex}, this.value)">
            <button type="button" id="removeMaterialBtn-${materialIndex}" class="text-red-600 font-bold">✕</button>
        </div>
    `);

    initMaterial(materialIndex);
});

window.removeMaterial = index => document.getElementById(`material-${index}`)?.remove();

/* ===========================
 * MATERIAL LINK PREVIEW
 * =========================== */
window.setMaterialLink = function (index, productId) {
    const preview = document.getElementById(`preview-${index}`);
    if (preview) {
        preview.dataset.link = productId ? `/product/${productId}` : '';
    }
};

document.addEventListener('click', e => {
    const preview = e.target.closest('[id^="preview-"]');
    if (preview?.dataset.link) {
        window.location.href = preview.dataset.link;
    }
});





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
