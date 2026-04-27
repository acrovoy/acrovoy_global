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

function updateTierChain(startIndex) {
    const tiers = document.querySelectorAll('#price-tiers .grid');
    for (let i = startIndex; i < tiers.length; i++) {
        const prevTier = tiers[i - 1];
        const prevMaxInput = prevTier.querySelector('input[name$="[max_qty]"]');
        const currentTier = tiers[i];
        const currentMinInput = currentTier.querySelector('input[name$="[min_qty]"]');
        const currentMaxInput = currentTier.querySelector('input[name$="[max_qty]"]');

        if (prevMaxInput && currentMinInput) {
            const prevMaxValue = prevMaxInput.value.trim();
            const prevMax = parseInt(prevMaxValue, 10);
            if (prevMaxValue !== '' && !isNaN(prevMax)) {
                currentMinInput.value = prevMax + 1;
            }
        }

        if (currentMinInput && currentMaxInput) {
            const minValue = currentMinInput.value.trim();
            const maxValue = currentMaxInput.value.trim();
            const min = parseInt(minValue, 10);
            const max = parseInt(maxValue, 10);
            if (maxValue !== '' && !isNaN(max) && !isNaN(min) && max <= min) {
                currentMaxInput.value = min + 1;
            }
        }
    }
}

function checkMaxMinValidation() {
    const priceTiers = document.querySelectorAll('#price-tiers .grid');
    let isError = false;

    for (const tier of priceTiers) {
        const minInput = tier.querySelector('input[name$="[min_qty]"]');
        const maxInput = tier.querySelector('input[name$="[max_qty]"]');
        if (minInput && maxInput) {
            const minValue = minInput.value.trim();
            const maxValue = maxInput.value.trim();
            const min = parseInt(minValue, 10);
            const max = parseInt(maxValue, 10);
            if (minValue !== '' && maxValue !== '' && !isNaN(min) && !isNaN(max) && max < min) {
                isError = true;
                break;
            }
        }
    }

    removeNotifications();
    if (isError) {
        showNotification("Max Quantity не может быть меньше Min Quantity!", "error");
    }
    return !isError;
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

        if (moqInput.value.trim() === '') {
            firstMinInput.readOnly = false;
        } else {
            firstMinInput.value = moqInput.value;
            firstMinInput.readOnly = true;
        }

        moqInput.addEventListener('input', () => {
            if (moqInput.value.trim() === '') {
                firstMinInput.readOnly = false;
            } else {
                firstMinInput.value = moqInput.value;
                firstMinInput.readOnly = true;
            }
        });

        moqInput.addEventListener('change', () => {
            if (
                firstMaxInput &&
                firstMaxInput.value.trim() !== '' &&
                parseInt(firstMaxInput.value, 10) < parseInt(firstMinInput.value, 10)
            ) {
                showNotification("Max Qty не может быть меньше Min Qty!", "error");
            }
            checkMaxMinValidation();
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

    maxInput.addEventListener('change', () => {
        updateTierChain(index + 1);
        checkMaxMinValidation();
    });
    minInput.addEventListener('change', checkMaxMinValidation);

});

updatePriceTierRemoveButtons();


// ==========================
// добавление новой строки
// ==========================
window.addPriceTier = function () {

    const container = document.getElementById('price-tiers');
    if (!container) return;

    let prevMaxValue = '';
    if (priceTierIndex > 0) {
        const tiers = document.querySelectorAll('#price-tiers .grid');
        const lastTier = tiers[tiers.length - 1];
        const prevMaxInput = lastTier?.querySelector('.input-max-qtty');
        prevMaxValue = prevMaxInput?.value.trim() ?? '';
    }

    if (priceTierIndex > 0 && (prevMaxValue === '' || isNaN(parseInt(prevMaxValue, 10)))) {
        return;
    }

    const prevMax = priceTierIndex > 0 ? parseInt(prevMaxValue, 10) : 0;
    const html = `
    <div class="grid grid-cols-3 gap-4 items-center" id="price-tier-${priceTierIndex}">
        <input type="number"
               name="price_tiers[${priceTierIndex}][min_qty]"
               placeholder="Min Qty"
               class="input"
               value="${priceTierIndex > 0 ? prevMax + 1 : ''}"
               ${priceTierIndex > 0 ? 'readonly' : ''}>

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

    const rowIndex = priceTierIndex;
    const maxInput = document.querySelector(
        `input[name="price_tiers[${rowIndex}][max_qty]"]`
    );

    const minInput = document.querySelector(
        `input[name="price_tiers[${rowIndex}][min_qty]"]`
    );

    maxInput.addEventListener('change', () => {
        updateTierChain(rowIndex + 1);
        checkMaxMinValidation();
    });
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
