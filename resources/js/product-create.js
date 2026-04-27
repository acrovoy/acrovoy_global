document.addEventListener('DOMContentLoaded', () => {


    /* ===========================
    * IMAGE
    * =========================== */


    const input = document.getElementById("productImages");
    const previewContainer = document.getElementById("imagesPreview");
    const dropZone = document.getElementById("productImagesDropZone");
    const MAX_FILES = 6;
    const metaContainer = document.getElementById("imagesMetaInputs");


    if (!input || !previewContainer || !dropZone) {
        console.warn("Uploader DOM not ready");
        return;
    }

    let filesState = [];
    // [{ file: File, hash: string }]

    function renderPreview() {

        previewContainer.innerHTML = "";

        filesState.forEach((item, index) => {

            const url = URL.createObjectURL(item.file);

            const wrapper = document.createElement("div");
            wrapper.className = "relative group w-28 flex flex-col items-center";

            const img = document.createElement("img");
            img.src = url;
            img.className = "w-28 h-28 object-cover rounded-lg shadow border";

            img.onload = () => URL.revokeObjectURL(url);

            const controls = document.createElement("div");
            controls.className = "flex items-center justify-center gap-3 mt-2 text-sm";

            controls.innerHTML = `
    <div class="flex items-center justify-center gap-3 mt-2 text-sm">

        <div class="flex items-center gap-3 px-2 py-1 bg-white rounded-xl
                    border border-gray-200 shadow-sm">

            <button type="button"
                data-action="left"
                data-index="${index}"
                class="w-6 h-6 flex items-center justify-center
                    text-gray-400 hover:text-gray-700
                    hover:bg-gray-50 rounded-lg transition">

                ‹
            </button>

            <span class="text-[14px] font-semibold text-gray-500 tracking-wide min-w-[36px] text-center">
                ${index === 0 ? "MAIN" : index + 1}
            </span>

            <button type="button"
                data-action="right"
                data-index="${index}"
                class="w-6 h-6 flex items-center justify-center
                    text-gray-400 hover:text-gray-700
                    hover:bg-gray-50 rounded-lg transition">

                ›
            </button>

            

        </div>
    </div>
`;

            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.dataset.action = "delete";
            deleteBtn.dataset.index = index;

            deleteBtn.className =
                "absolute -top-2 -right-2 w-6 h-6 flex items-center justify-center" +
                " rounded-lg border border-red-200 bg-white" +
                " text-red-400 hover:text-red-600 hover:bg-red-50" +
                " transition shadow-sm";

            deleteBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-3.5 h-3.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2">

                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"/>
            </svg>
        `;

            wrapper.appendChild(img);
            wrapper.appendChild(deleteBtn);
            wrapper.appendChild(controls);

            previewContainer.appendChild(wrapper);
        });
    }


    async function getFileHash(file) {
        const buffer = await file.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest("SHA-256", buffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, "0")).join("");
    }


    function syncMetaInputs() {

        metaContainer.innerHTML = "";

        filesState.forEach((file, index) => {

            const sortInput = document.createElement("input");
            sortInput.type = "hidden";
            sortInput.name = "sort_order[]";
            sortInput.value = index;

            const mainInput = document.createElement("input");
            mainInput.type = "hidden";
            mainInput.name = "is_main[]";
            mainInput.value = index === 0 ? 1 : 0;

            metaContainer.appendChild(sortInput);
            metaContainer.appendChild(mainInput);
        });
    }


    async function addFiles(newFiles) {

        for (const file of newFiles) {

            if (filesState.length >= MAX_FILES) {
                alert(`Maximum ${MAX_FILES} images allowed`);
                break;
            }

            if (!file.type.startsWith("image/")) continue;

            if (file.size > 5 * 1024 * 1024) {
                alert("Max file size 5MB");
                continue;
            }

            const newHash = await getFileHash(file);

            const exists = filesState.some(existing =>
                existing.hash === newHash
            );

            if (exists) continue;

            filesState.push({
                file: file,
                hash: newHash
            });
        }

        renderPreview();
        syncInputFiles();
    }



    function moveLeft(index) {
        if (index <= 0) return;

        [filesState[index - 1], filesState[index]] =
            [filesState[index], filesState[index - 1]];

        renderPreview();
        syncInputFiles();
    }

    function moveRight(index) {
        if (index >= filesState.length - 1) return;

        [filesState[index + 1], filesState[index]] =
            [filesState[index], filesState[index + 1]];

        renderPreview();
        syncInputFiles();
    }

    function deleteImage(index) {
        filesState.splice(index, 1);
        renderPreview();
        syncInputFiles();
    }


    function syncInputFiles() {

        const dataTransfer = new DataTransfer();
        filesState.forEach(item => dataTransfer.items.add(item.file));
        input.files = dataTransfer.files;

        syncMetaInputs();
    }

    // Events

    input.addEventListener("change", async e => {
        await addFiles(e.target.files);
        input.value = "";
    });

    previewContainer.addEventListener("click", e => {

        const btn = e.target.closest("button");
        if (!btn) return;

        e.preventDefault();

        const index = parseInt(btn.dataset.index);
        const action = btn.dataset.action;

        if (action === "left") moveLeft(index);
        if (action === "right") moveRight(index);
        if (action === "delete") deleteImage(index);
    });

    // Drag & Drop

    dropZone.addEventListener("dragover", e => {
        e.preventDefault();
        dropZone.classList.add("border-blue-600", "bg-blue-50");
    });

    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("border-blue-600", "bg-blue-50");
    });

    dropZone.addEventListener("drop", e => {
        e.preventDefault();

        dropZone.classList.remove("border-blue-600", "bg-blue-50");

        if (e.dataTransfer.files) {
            addFiles(e.dataTransfer.files);
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

    function renderSelectedMaterials() {
        selectedMaterialsContainer.innerHTML = '';
        selectedMaterials.forEach((m, i) => {
            const chip = document.createElement('div');
            chip.className = 'px-2 py-1 bg-blue-100 rounded-full text-xs flex gap-1';
            chip.innerHTML = `<span>${m.name}</span><button>&times;</button>`;
            chip.querySelector('button').onclick = () => {
                selectedMaterials.splice(i, 1);
                updateSelectedMaterialsInput();
                renderSelectedMaterials();
            };
            selectedMaterialsContainer.appendChild(chip);
        });
    }

    function updateSelectedMaterialsInput() {
        selectedMaterialsInput.value = selectedMaterials.map(m => m.id).join(',');
    }

    materialsOptions.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!selectedMaterials.find(m => m.id === btn.dataset.id)) {
                selectedMaterials.push({ id: btn.dataset.id, name: btn.dataset.name });
                updateSelectedMaterialsInput();
                renderSelectedMaterials();
            }
        });
    });

    materialSearch?.addEventListener('input', () => {
        const q = materialSearch.value.toLowerCase();
        materialsOptions.forEach(btn => {
            btn.style.display = btn.dataset.name.toLowerCase().includes(q)
                ? 'inline-flex'
                : 'none';
        });
    });


    /* ===========================
     * PRICE TIERS
     * =========================== */
    let priceTierIndex = 1;

    // ==========================
    // Функция для отображения уведомлений в блоке
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

    }

    function removeNotifications() {
        const container = document.getElementById('notification-block');
        if (!container) return;
        container.innerHTML = '';
    }

    function updateTierChain(startIndex) {
        const tiers = document.querySelectorAll('#price-tiers .grid');
        for (let i = startIndex; i < tiers.length; i++) {
            if (i === 0) continue; // первый tier не корректируем
            const prevTier = tiers[i - 1];
            const prevMaxInput = prevTier.querySelector('input[name$="[max_qty]"]');
            const currentTier = tiers[i];
            const currentMinInput = currentTier.querySelector('input[name$="[min_qty]"]');
            const currentMaxInput = currentTier.querySelector('input[name$="[max_qty]"]');
            if (prevMaxInput && currentMinInput) {
                const prevMax = parseInt(prevMaxInput.value) || 0;
                currentMinInput.value = prevMax + 1;
            }
            if (currentMinInput && currentMaxInput) {
                const minValue = currentMinInput.value.trim();
                const maxValue = currentMaxInput.value.trim();
                if (maxValue !== '') {
                    const min = parseInt(minValue, 10) || 0;
                    const max = parseInt(maxValue, 10);
                    if (!isNaN(max) && max <= min) {
                        currentMaxInput.value = min + 1;
                    }
                }
            }
        }
    }

    // ==========================
    // MOQ задаёт min первой строки
    // ==========================
    const moqInput = document.querySelector('input[name="moq"]');
    moqInput.addEventListener('input', () => {
        const firstMinInput = document.querySelector('input[name="price_tiers[0][min_qty]"]');
        if (firstMinInput) {
            if (moqInput.value.trim() === '') {
                firstMinInput.readOnly = false;
            } else {
                firstMinInput.value = moqInput.value;
                firstMinInput.readOnly = true;
            }
        }
    });

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
                if (maxValue !== '' && minValue !== '' && !isNaN(min) && !isNaN(max) && max < min) {
                    isError = true;
                    break;
                }
            }
        }
        removeNotifications();
        if (isError) {
            showNotification("Max Quantity не может быть меньше Min Quantity!", "error");
        }
    }


    moqInput.addEventListener('change', checkMaxMinValidation);
    const maxInput = document.querySelector(`input[name="price_tiers[0][max_qty]"]`);
    const minInput = document.querySelector(`input[name="price_tiers[0][min_qty]"]`);
    maxInput.addEventListener('change', () => {
        updateTierChain(1);
        checkMaxMinValidation();
    });
    minInput.addEventListener('change', checkMaxMinValidation);

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

    updatePriceTierRemoveButtons();


    // ==========================
    // Добавление новых прайс-уровней
    // ==========================
    window.addPriceTier = function () {
        const container = document.getElementById('price-tiers');
        if (!container) return;

        // Получаем max_qty предыдущей строки
        let prevMax = 0;
        if (priceTierIndex > 0) {
            const tiers = document.querySelectorAll('#price-tiers .grid');
            const lastTier = tiers[tiers.length - 1];
            const prevMaxInput = lastTier?.querySelector('.input-max-qtty');
            prevMax = prevMaxInput && prevMaxInput.value ? parseInt(prevMaxInput.value) : 0;
        }

        if (prevMax === 0) {
            return;
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

        // Защита max_qty новой строки
        const newMaxInput = document.querySelector(`input[name="price_tiers[${priceTierIndex - 1}][max_qty]"]`);
        const newMinInput = document.querySelector(`input[name="price_tiers[${priceTierIndex - 1}][min_qty]"]`);
        newMaxInput.addEventListener('change', () => {
            updateTierChain(priceTierIndex - 1);
            checkMaxMinValidation();
        });
        newMinInput.addEventListener('change', checkMaxMinValidation);

        priceTierIndex++;
        updatePriceTierRemoveButtons();
    };

    // ==========================
    // Удаление прайс-уровней
    // ==========================
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

    document.getElementById("productForm")?.addEventListener("submit", () => {
        syncInputFiles();
    });

});
