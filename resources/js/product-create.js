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




    

    document.getElementById("productForm")?.addEventListener("submit", () => {
    syncInputFiles();
});

});
