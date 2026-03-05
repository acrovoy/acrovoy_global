let filesState = [];

const bootstrapUploader = () => {

    console.log("Existing images:", window.existingImages);

    const input = document.getElementById("productImages");
    const previewContainer = document.getElementById("imagesPreview");
    const dropZone = document.getElementById("productImagesDropZone");
    const metaContainer = document.getElementById("imagesMetaInputs");

    if (!input || !previewContainer || !dropZone) return;

    const MAX_FILES = 6;

    /* =============================
     HYDRATE EXISTING IMAGES
    ============================== */

    if (Array.isArray(window.existingImages) && window.existingImages.length) {

        filesState = window.existingImages
            .sort((a, b) => a.sort_order - b.sort_order)
            .map(img => ({
                type: "existing",
                id: img.id,
                url: img.url,
                hash: null
            }));

    }

    /* =============================
     RENDER PREVIEW
    ============================== */

    function renderPreview() {

        previewContainer.innerHTML = "";

        filesState.forEach((item, index) => {

            const url = item.type === "new"
                ? URL.createObjectURL(item.file)
                : item.url;

            const wrapper = document.createElement("div");
            wrapper.className = "relative group w-28 flex flex-col items-center";

            /* IMAGE */

            const img = document.createElement("img");
            img.src = url;
            img.className = "w-28 h-28 object-cover rounded-lg shadow border";

            if (item.type === "new") {
                img.onload = () => URL.revokeObjectURL(url);
            }

            /* DELETE BUTTON (TOP RIGHT ON IMAGE) */

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

            /* CONTROLS */

            const controls = document.createElement("div");

            controls.innerHTML = `
        <div class="flex items-center gap-3 px-2 py-1 bg-white rounded-xl
                    border border-gray-200 shadow-sm mt-2">

            <button type="button"
                data-action="left"
                data-index="${index}"
                class="w-6 h-6 flex items-center justify-center
                    text-gray-400 hover:text-gray-700
                    hover:bg-gray-50 rounded-lg transition">

                ‹
            </button>

            <span class="text-[14px] font-semibold text-gray-500 tracking-wide
                        min-w-[36px] text-center">
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
        `;

            wrapper.appendChild(img);
            wrapper.appendChild(deleteBtn);
            wrapper.appendChild(controls);

            previewContainer.appendChild(wrapper);
        });
    }

    /* =============================
     SYNC FILE INPUT + META
    ============================== */

    function syncInputFiles() {

        const dataTransfer = new DataTransfer();

        filesState.forEach(item => {
            if (item.type === "new" && item.file) {
                dataTransfer.items.add(item.file);
            }
        });

        input.files = dataTransfer.files;

        metaContainer.innerHTML = "";

        filesState.forEach((item, index) => {

            if (item.type === "existing") {

                metaContainer.appendChild(
                    Object.assign(document.createElement("input"), {
                        type: "hidden",
                        name: "existing_ids[]",
                        value: item.id
                    })
                );
            }
            if (item.type === "existing") {
                metaContainer.appendChild(
                    Object.assign(document.createElement("input"), {
                        type: "hidden",
                        name: "existing_sort_order[]",
                        value: index
                    })
                );
            } else {
                metaContainer.appendChild(
                    Object.assign(document.createElement("input"), {
                        type: "hidden",
                        name: "sort_order[]",
                        value: index
                    })
                );
            }

            metaContainer.appendChild(
                Object.assign(document.createElement("input"), {
                    type: "hidden",
                    name: "is_main[]",
                    value: index === 0 ? "1" : "0"
                })
            );
        });
    }

    /* =============================
     FILE HASH
    ============================== */

    async function getFileHash(file) {

        const buffer = await file.arrayBuffer();

        const hashBuffer = await crypto.subtle.digest(
            "SHA-256",
            buffer
        );

        return Array.from(new Uint8Array(hashBuffer))
            .map(b => b.toString(16).padStart(2, "0"))
            .join("");
    }

    /* =============================
     ADD FILES
    ============================== */

    async function addFiles(newFiles) {
        
        const files = Array.from(newFiles);

        for (const file of files) {
            
            if (filesState.length >= MAX_FILES) break;

            if (!file.type.startsWith("image/")) continue;

            if (file.size > 5 * 1024 * 1024) continue;

            const hash = await getFileHash(file);

            if (filesState.some(x => x.hash === hash)) continue;

            filesState.push({
                type: "new",
                file,
                hash
            });
        }

        renderPreview();
        syncInputFiles();
    }

    /* =============================
     MOVE & DELETE
    ============================== */

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

    /* =============================
     EVENTS
    ============================== */

    input.addEventListener("change", e => {
        addFiles(e.target.files);
        input.value = "";
    });

    previewContainer.addEventListener("click", e => {

        const btn = e.target.closest("button");
        if (!btn) return;

        e.preventDefault();

        const index = Number(btn.dataset.index);
        const action = btn.dataset.action;

        if (action === "left") moveLeft(index);
        if (action === "right") moveRight(index);
        if (action === "delete") deleteImage(index);
    });

    dropZone.addEventListener("dragover", e => e.preventDefault());

    dropZone.addEventListener("drop", e => {

        e.preventDefault();

        if (e.dataTransfer.files) {
            addFiles(e.dataTransfer.files);
        }
    });

    renderPreview();
};

/* =============================
BOOTSTRAP SAFE INIT
============================= */

if (document.readyState !== "loading") {
    bootstrapUploader();
} else {
    document.addEventListener("DOMContentLoaded", bootstrapUploader);
}