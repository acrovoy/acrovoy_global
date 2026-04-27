<div 
    x-data="factoryUploader()"
    class="space-y-6"
>

    <label class="block font-medium">
        Factory Photos
    </label>

    <!-- Dropzone -->
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop($event)"

        @click="$refs.input.click()"

        class="border-2 border-dashed rounded-xl h-44 flex flex-col justify-center items-center cursor-pointer transition"

        :class="dragging ? 'bg-gray-100 border-blue-400' : 'bg-gray-50 border-gray-300'"
    >

        <p class="text-gray-500 text-sm">
            Drag & drop photos or click to upload
        </p>

        <input
            type="file"
            multiple
            accept="image/*"
            x-ref="input"
            class="hidden"
            @change="handleFiles($event)"
        >
    </div>

    <!-- Preview Grid -->
    <div class="grid grid-cols-4 gap-4">

        <template x-for="(file,index) in previews" :key="index">

            <div class="relative group">

                <img :src="file" class="w-full h-24 object-cover rounded-lg shadow">

                <button
                    type="button"
                    @click="removePreview(index)"
                    class="absolute top-1 right-1 bg-red-500 text-white text-xs px-2 rounded opacity-0 group-hover:opacity-100 transition"
                >
                    ×
                </button>

            </div>

        </template>

    </div>

    <!-- Progress -->
    <div x-show="uploading" class="space-y-2">

        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div
                class="h-full bg-blue-600 transition-all"
                :style="`width:${progress}%`"
            ></div>
        </div>

        <div class="text-xs text-gray-500">
            Uploading... <span x-text="progress"></span>%
        </div>

    </div>

    <button
        @click="uploadFiles"
        class="px-6 py-2 bg-blue-950 text-white rounded-lg hover:bg-blue-900 transition"
    >
        Upload Photos
    </button>

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

        // ✅ ДОБАВЛЕН МЕТОД УДАЛЕНИЯ
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