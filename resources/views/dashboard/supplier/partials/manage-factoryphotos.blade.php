<div
    x-data="factoryUploader()"
    class="space-y-6"
>

    {{-- Header --}}
<div class="px-8 py-6 bg-gradient-to-r from-slate-50 via-[#f4f1eb] to-[#ebe5dc] border-b border-gray-200">

    <div class="flex items-center justify-between">

        <div>

            <h2 class="text-xl font-semibold text-gray-900">
                Upload Factory Photos
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Add photos of your factory, production facilities and equipment.
            </p>

        </div>

       <button
                type="button"
                onclick="closeModal('factoryPhotosModal')"
                class="text-gray-500 hover:text-gray-900 transition text-xl leading-none">

                ✕

            </button>

    </div>

</div>

<div class="p-8 space-y-6">
    {{-- Dropzone --}}
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop($event)"
        @click="$refs.input.click()"

        class="group mx-auto flex flex-col items-center justify-center
               w-full max-w-lg rounded-2xl border-2 border-dashed
               px-6 py-8 cursor-pointer transition"

        :class="dragging
            ? 'bg-blue-50 border-blue-400'
            : 'bg-gradient-to-b from-gray-50 to-white border-gray-300 hover:border-gray-400'">

        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-white border border-gray-200 shadow-sm group-hover:shadow-md transition">

            <svg class="w-7 h-7 text-gray-500"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M12 12v8m0-8l-3 3m3-3l3 3"/>

            </svg>

        </div>

        <div class="mt-5 text-base font-semibold text-gray-900">
            Choose Photos
        </div>

        <div class="mt-1 text-sm text-gray-500">
            or drag & drop them here
        </div>

        <div class="mt-2 text-xs text-gray-400">
            JPG • PNG • WEBP
        </div>

        <input
            type="file"
            multiple
            accept="image/*"
            x-ref="input"
            class="hidden"
            @change="handleFiles($event)"
        >

    </div>

    {{-- Preview --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">

        <template x-for="(file,index) in previews" :key="index">

            <div class="relative group overflow-hidden rounded-xl border border-gray-200 bg-gray-50">

                <img
                    :src="file"
                    class="aspect-square w-full object-cover transition duration-300 group-hover:scale-[1.03]">

                <button
                    type="button"
                    @click="removePreview(index)"
                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition
                           px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700
                           text-white text-xs shadow">

                    Delete

                </button>

            </div>

        </template>

    </div>

    {{-- Progress --}}
    <div x-show="uploading" class="space-y-3">

        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">

            <div
                class="h-full bg-gray-900 transition-all duration-300"
                :style="`width:${progress}%`">
            </div>

        </div>

        <div class="text-sm text-gray-500">
            Uploading...
            <span class="font-medium" x-text="progress"></span>%
        </div>

    </div>

    {{-- Upload --}}
    <div class="flex justify-end">

        <button
            @click="uploadFiles"
            class="px-6 py-2.5 rounded-lg bg-gray-900 text-white font-medium hover:bg-black transition">

            Upload Photos

        </button>

    </div>

</div>
</div>
<script>
function factoryUploader() {
    return {

        dragging: false,
        files: [],
        previews: [],
        progress: 0,
        uploading: false,

        handleDrop(event) {
            this.dragging = false
            this.processFiles(event.dataTransfer.files)
        },

        handleFiles(event) {
            this.processFiles(event.target.files)
        },

        processFiles(fileList) {

            for (let file of fileList) {

                if (!file.type.startsWith('image/')) continue

                this.files.push(file)

                const reader = new FileReader()

                reader.onload = e => {
                    this.previews.push(e.target.result)
                }

                reader.readAsDataURL(file)
            }
        },

        removePreview(index) {
            this.files.splice(index, 1)
            this.previews.splice(index, 1)
        },

        async uploadFiles() {

            if (!this.files.length) return

            this.uploading = true

            const formData = new FormData()

            this.files.forEach(file => {
                formData.append('photos[]', file)
            })

            const token = document.querySelector('meta[name="csrf-token"]').content

            try {

                await axios.post(
                    "{{ route('manufacturer.factory.photos.upload') }}",
                    formData,
                    {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'multipart/form-data'
                        },

                        onUploadProgress: progressEvent => {

                            this.progress = Math.round(
                                (progressEvent.loaded * 100) /
                                progressEvent.total
                            )

                        }
                    }
                )

                this.files = []
                this.previews = []
                this.progress = 0
                this.uploading = false

                setTimeout(() => {
                    window.location.reload()
                }, 500)

            } catch (e) {
                console.error(e)
                alert('Upload failed')
            }

            this.uploading = false
        },

        async deletePhoto(id) {

            const token = document.querySelector('meta[name="csrf-token"]').content

            try {

                await axios.delete(`/factory/photos/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })

                setTimeout(() => {
                    window.location.reload()
                }, 300)

            } catch (e) {
                console.error(e)
                alert('Delete failed')
            }
        }

    }
}
</script>