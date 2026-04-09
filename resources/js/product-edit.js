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
 * PRICE TIERS (EDIT MODE)
 * =========================== */

// стартовый индекс = количество уже существующих строк
let priceTierIndex = document.querySelectorAll('[id^="price-tier-"]').length;


// ==========================
// уведомления в компонент
// ==========================
function showNotification(message, type = 'error') {
    const container = document.getElementById('notification-block');
    if (!container) return;

    const colors = {
        success: 'bg-green-100 border-green-300 text-green-800',
        error: 'bg-red-100 border-red-300 text-red-800'
    };

    const html = `
        <div class="mb-2 rounded-lg ${colors[type]} px-4 py-3">
            ${message}
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    setTimeout(() => {
        if (container.firstChild) container.firstChild.remove();
    }, 5000);
}

function removeNotifications() {
    const container = document.getElementById('notification-block');
    if (!container) return;
    container.innerHTML = '';
}

function checkMaxMinValidation() {
    const priceTiers = document.querySelectorAll('#price-tiers .grid');
    let isError = false;

    for (const tier of priceTiers) {
        const minInput = tier.querySelector('input[name$="[min_qty]"]');
        const maxInput = tier.querySelector('input[name$="[max_qty]"]');
        if (minInput && maxInput) {
            if (parseInt(maxInput.value) < parseInt(minInput.value)) {
                isError = true;
            }
        }
    }

    removeNotifications();
    if (isError) {
        showNotification("Max Quantity не может быть меньше Min Quantity!", "error");
    }
}

function updatePriceTierRemoveButtons() {
    const tiers = document.querySelectorAll('#price-tiers .grid');
    const lastIndex = tiers.length - 1;

    tiers.forEach((tier, idx) => {
        const removeBtn = tier.querySelector('button[onclick^="removePriceTier"]');
        if (!removeBtn) return;

        if (idx === lastIndex) {
            removeBtn.disabled = false;
            removeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            removeBtn.disabled = true;
            removeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
}


// ==========================
// MOQ управляет первой строкой
// ==========================
const moqInput = document.querySelector('input[name="moq"]');

if (moqInput) {

    const firstMinInput = document.querySelector('input[name="price_tiers[0][min_qty]"]');
    const firstMaxInput = document.querySelector('input[name="price_tiers[0][max_qty]"]');

    if (firstMinInput) {

        // применяем сразу при загрузке страницы
        if (moqInput.value) {
            firstMinInput.value = moqInput.value;
            firstMinInput.readOnly = true;
        }

        // применяем при изменении MOQ
        moqInput.addEventListener('input', () => {

            firstMinInput.value = moqInput.value;
            firstMinInput.readOnly = true;

            if (
                firstMaxInput &&
                parseInt(firstMaxInput.value) < parseInt(firstMinInput.value)
            ) {
                showNotification("Max Qty не может быть меньше Min Qty!", "error");
            }

        });

    }

}


// ==========================
// защита существующих строк
// ==========================
document.querySelectorAll('[id^="price-tier-"]').forEach((row, index) => {

    const minInput = row.querySelector(`input[name="price_tiers[${index}][min_qty]"]`);
    const maxInput = row.querySelector(`input[name="price_tiers[${index}][max_qty]"]`);

    if (!minInput || !maxInput) return;

    maxInput.addEventListener('change', checkMaxMinValidation);
    minInput.addEventListener('change', checkMaxMinValidation);

});

updatePriceTierRemoveButtons();


// ==========================
// добавление новой строки
// ==========================
window.addPriceTier = function () {

    const container = document.getElementById('price-tiers');
    if (!container) return;


    let prevMax = 0;
        if (priceTierIndex > 0) {
            const tiers = document.querySelectorAll('#price-tiers .grid');
            const lastTier = tiers[tiers.length - 1];
            const prevMaxInput = lastTier?.querySelector('.input-max-qtty');
            prevMax = prevMaxInput && prevMaxInput.value ? parseInt(prevMaxInput.value) : 0;
        }


    const html = `
    <div class="grid grid-cols-3 gap-4 items-center" id="price-tier-${priceTierIndex}">
        <input type="number"
               name="price_tiers[${priceTierIndex}][min_qty]"
               placeholder="Min Qty"
               class="input"
               value="${prevMax + 1}"
               readonly>

        <input type="number"
               name="price_tiers[${priceTierIndex}][max_qty]"
               placeholder="Max Qty"
               class="input input-max-qtty">

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


    const maxInput = document.querySelector(
        `input[name="price_tiers[${priceTierIndex}][max_qty]"]`
    );

    const minInput = document.querySelector(
        `input[name="price_tiers[${priceTierIndex}][min_qty]"]`
    );


    maxInput.addEventListener('change', checkMaxMinValidation);
    minInput.addEventListener('change', checkMaxMinValidation);

    priceTierIndex++;
    updatePriceTierRemoveButtons();
};

window.removePriceTier = index => {
    const priceTiers = document.querySelectorAll('#price-tiers .grid');
    const lastTier = priceTiers[priceTiers.length - 1];
    if (!lastTier || lastTier.id !== `price-tier-${index}`) {
        return;
    }

    const el = document.getElementById(`price-tier-${index}`);
    if (el) {
        el.remove();
        updatePriceTierRemoveButtons();
    }
};

});
