document.addEventListener('DOMContentLoaded', () => {

    /* ===========================
     * IMAGES UPLOAD + SORT
     * =========================== */
    const productImagesInput = document.getElementById('productImages');
    const imagesPreview = document.getElementById('imagesPreview');


    function updateMainLabel() {
        if (!imagesPreview) return;
        const wrappers = imagesPreview.querySelectorAll('.image-wrapper');
        wrappers.forEach((w, i) => {

            const label = w.querySelector('.main-label');
            const btn = w.querySelector('.main-img-btn');
            const input = w.querySelector('input.image-main');
            if (!label) return;
            label.style.display = input.value == '1' ? 'block' : 'none';
            btn.style.display = input.value != '1' ? 'block' : 'none';
        });
    }

    function addImagePreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            const wrapper = document.createElement('div');
            wrapper.className = 'image-wrapper relative w-24 h-24  pb-3 mb-3';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover rounded shadow';
            wrapper.appendChild(img);

            const mainLabel = document.createElement('div');
            mainLabel.className = 'main-label absolute bottom-5 left-0 bg-yellow-400 text-black text-xs px-1';
            mainLabel.innerText = 'MAIN';
            mainLabel.style.display = 'none';
            wrapper.appendChild(mainLabel);

            const delBtn = document.createElement('button');
            delBtn.className = 'absolute top-0 right-0 bg-red-600 text-white text-xs px-1 rounded';
            delBtn.innerText = '×';
            delBtn.type = 'button';

            delBtn.onclick = (e) => {
                const wrapper = e.target.closest('.image-wrapper');
                const inputVal = wrapper.querySelector('input.image-main').value;
                
                wrapper.remove();

                if (inputVal== '1') {
                    const wrappers = imagesPreview.querySelectorAll('.image-wrapper');
                    if (wrappers.length > 1) {
                        const firstWrapper = wrappers[0];
                    console.log(firstWrapper);

                        const firstMainInput = firstWrapper.querySelector('input.image-main');
                        if (firstMainInput) {
                            firstMainInput.value = '1';
                        }
                    }
                }
                updateOrders();
                updateMainLabel();

            };
            wrapper.appendChild(delBtn);

            wrapper.draggable = true;
            wrapper.addEventListener('dragstart', () => wrapper.classList.add('dragging'));
            wrapper.addEventListener('dragend', () => {
                wrapper.classList.remove('dragging');
                updateOrders();
            });

            const mainInput = document.createElement('input');
            mainInput.type = 'hidden';
            mainInput.name = `new_images_main[]`;
            mainInput.className = 'image-main';
            wrapper.appendChild(mainInput);

            const mainBtn = document.createElement('button');
            mainBtn.type = 'button';
            mainBtn.className = 'mt-1 px-3 w-full py-0 bg-yellow-300 text-sm text-black border border-black rounded main-img-btn';
            mainBtn.innerText = 'MakeMain';
            mainBtn.onclick = (e) => setMainImage(e);
            wrapper.appendChild(mainBtn);

            imagesPreview.appendChild(wrapper);
            updateOrders();
        };
        reader.readAsDataURL(file);
    }

    productImagesInput?.addEventListener('change', () => {
        Array.from(productImagesInput.files).forEach(addImagePreview);
    });

    function setMainImage(e) {

        const wrappers = imagesPreview.querySelectorAll('.image-wrapper');
        wrappers.forEach(w => {
            const input = w.querySelector('input.image-main');
            if (input) {
                input.value = '0';
            }
        });

        const wrapper = e.target.closest('.image-wrapper');
        const input = wrapper.querySelector('input.image-main');
        if (input) {
            input.value = '1';
        }
        updateMainLabel();
    }

    function updateOrders() {
        const wrappers = imagesPreview.querySelectorAll('.image-wrapper');
        wrappers.forEach((wrapper, index) => {
            wrapper.dataset.order = index;
        });
    }

    imagesPreview?.addEventListener('dragover', e => {
        e.preventDefault();
        const dragging = imagesPreview.querySelector('.dragging');
        const after = [...imagesPreview.children]
            .filter(el => el !== dragging)
            .find(el => e.clientX < el.getBoundingClientRect().right);

        after ? imagesPreview.insertBefore(dragging, after)
            : imagesPreview.appendChild(dragging);
        updateOrders();
    });


    /* ===========================
     * MATERIAL COLOR / TEXTURE
     * =========================== */
    let materialIndex = 0;

    window.initMaterial = function (index) {
        const preview = document.getElementById(`preview-${index}`);
        const colorInput = document.getElementById(`colorInput-${index}`);
        const fileInput = document.getElementById(`fileInput-${index}`);

        if (!preview || !colorInput || !fileInput || !window.Pickr) return;

        const pickr = Pickr.create({
            el: `#colorBtn-${index}`,
            theme: 'classic',
            default: '#ffffff',
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
    };

    initMaterial(0);

    document.getElementById('addMaterialBtn')?.addEventListener('click', () => {
        materialIndex++;
        const wrapper = document.getElementById('materials-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-4 mt-2 material-item" id="material-${materialIndex}">
                <div class="w-12 h-12 border rounded cursor-pointer"
                     id="preview-${materialIndex}" data-link=""></div>

                <input type="hidden" name="materials[${materialIndex}][color]" id="colorInput-${materialIndex}">
                
                <input type="file" name="materials[${materialIndex}][texture]" class="hidden" id="fileInput-${materialIndex}">

                <button type="button" id="colorBtn-${materialIndex}"
                        class="px-4 py-2 bg-blue-800 text-white rounded">Выбрать цвет</button>

                <button type="button"
        onclick="document.getElementById('fileInput-${materialIndex}').click()"
        class="px-4 py-2 bg-blue-800 text-white rounded">
    Выбрать файл
</button>

<span class="text-gray-500 px-2 flex items-center">🔗</span>

<input type="number"
       name="materials[${materialIndex}][linked_product_id]"
       placeholder="Product ID"
       class="w-32 px-2 py-1 border rounded text-sm"
       oninput="setMaterialLink(${materialIndex}, this.value)">

                <button type="button"
                        onclick="removeMaterial(${materialIndex})"
                        class="text-red-600 font-bold">✕</button>
            </div>
        `);

        initMaterial(materialIndex);
    });

    window.removeMaterial = index =>
        document.getElementById(`material-${index}`)?.remove();


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

});
