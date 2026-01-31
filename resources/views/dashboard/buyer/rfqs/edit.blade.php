@extends('dashboard.layout')

@section('dashboard-content')
<div class="max-w-4xl mx-auto py-8">

    {{-- Заголовок --}}
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">
            Edit RFQ
        </h1>
        <span class="text-sm text-gray-500">You can edit your RFQ before receiving any offers</span>
    </div>

    <div class="bg-white shadow rounded-lg p-6">

        <form method="POST" action="{{ route('buyer.rfqs.update', $rfq->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Title --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $rfq->title) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g. Furniture for boutique hotel"
                    required
                >
                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    name="description"
                    rows="5"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Describe materials, style, requirements, delivery expectations..."
                    required
                >{{ old('description', $rfq->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select
                    name="category_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">— Select category —</option>
                    @foreach($categories ?? [] as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected(old('category_id', $rfq->category_id) == $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quantity + Deadline --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity', $rfq->quantity) }}"
                        min="1"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline for offers</label>
                    <input
                        type="datetime-local"
                        name="deadline"
                        value="{{ old('deadline', $rfq->deadline ? \Carbon\Carbon::parse($rfq->deadline)->format('Y-m-d\TH:i') : '') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            {{-- File Upload with Preview --}}
            <div class="mt-4" style="width: 240px;">
                <label class="block font-medium mb-2">Attachment</label>

                <div id="rfq-dropzone"
                     class="border-2 border-dashed border-gray-300 
                            h-40 flex items-center justify-center bg-gray-50 relative cursor-pointer overflow-hidden group">

                    <img id="rfq-preview" 
                         src="{{ $rfq->attachment_path ? Storage::url($rfq->attachment_path) : asset('images/no-image.png') }}" 
                         class="object-cover max-w-full max-h-full">

                    <div class="absolute inset-0 bg-black bg-opacity-30
                                flex items-center justify-center opacity-0
                                group-hover:opacity-100 transition">
                        <span class="text-white text-sm">Change</span>
                    </div>
                </div>

                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.dwg,.dxf" id="rfq-input" class="hidden">

                <p class="text-sm text-gray-500 mt-1">
                    Upload a drawing, specification sheet, or PDF. Allowed: PDF, JPG, PNG, DWG, DXF. Max 10MB.
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('buyer.rfqs.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Cancel
                </a>

                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Update RFQ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rfqInput = document.getElementById('rfq-input');
    const rfqPreview = document.getElementById('rfq-preview');
    const rfqDropzone = document.getElementById('rfq-dropzone');

    // Функция для определения превью
    const getFilePreview = (file) => {
        const ext = file.name.split('.').pop().toLowerCase();
        if (['jpg','jpeg','png'].includes(ext)) {
            return URL.createObjectURL(file);
        } else if (ext === 'pdf') {
            return "{{ asset('images/pdf-icon.png') }}"; // иконка PDF
        } else if (['dwg','dxf'].includes(ext)) {
            return "{{ asset('images/dwg-icon.png') }}"; // иконка DWG/DXF
        } else {
            return "{{ asset('images/file-placeholder.png') }}"; // остальные файлы
        }
    };

    // Клик открывает проводник
    rfqDropzone.addEventListener('click', () => rfqInput.click());

    // Изменение файла через input
    rfqInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;
        rfqPreview.src = getFilePreview(file);
    });

    // Drag&drop
    rfqDropzone.addEventListener('dragover', e => {
        e.preventDefault();
        rfqDropzone.classList.add('border-blue-400', 'bg-blue-50');
    });

    rfqDropzone.addEventListener('dragleave', () => {
        rfqDropzone.classList.remove('border-blue-400', 'bg-blue-50');
    });

    rfqDropzone.addEventListener('drop', e => {
        e.preventDefault();
        rfqDropzone.classList.remove('border-blue-400', 'bg-blue-50');

        const file = e.dataTransfer.files[0];
        if(!file) return;

        rfqInput.files = e.dataTransfer.files;
        rfqPreview.src = getFilePreview(file);
    });
});
</script>

@endpush
