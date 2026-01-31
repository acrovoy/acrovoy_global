@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">

    {{-- Заголовок --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Request for Quote
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Create a request to receive price offers from manufacturers
        </p>
    </div>

    {{-- Карточка --}}
    <div class="bg-white shadow rounded-lg p-6">

        <form method="POST" action="{{ route('buyer.rfqs.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Title
                </label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
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
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea
                    name="description"
                    rows="5"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Describe materials, style, requirements, delivery expectations..."
                    required
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Category
                </label>
                <select
                    name="category_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">— Select category —</option>
                    @foreach($categories ?? [] as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected(old('category_id') == $category->id)
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantity
                    </label>
                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        min="1"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deadline for offers
                    </label>
                    <input
                        type="datetime-local"
                        name="deadline"
                        value="{{ old('deadline') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

            </div>



           
            {{-- File Upload with Preview --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Attach file
                </label>

                <div 
                    id="file-upload-wrapper" 
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 transition"
                    onclick="document.getElementById('attachment').click()"
                >
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png,.dwg,.dxf" class="hidden">
                    
                    <div id="file-preview" class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12v6m0 0v-6m0 6H8m4 0h4m-6-6V6m0 0V6m0 0V6m0 0V6m0 0V6m0 0V6" />
                        </svg>
                        <p class="text-gray-500 mt-2">Click or drag file here</p>
                    </div>
                </div>

                 {{-- Пояснение внизу --}}
                <p class="text-xs text-gray-500 mt-2">
                    Upload a drawing, specification sheet, or PDF. Allowed formats: PDF, JPG, PNG, DWG, DXF. Max size 10MB.
                </p>

                @error('attachment')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>



            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('main') }}"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
                >
                    Create RFQ
                </button>
            </div>

        </form>
    </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('attachment');
    const preview = document.getElementById('file-preview');
    const wrapper = document.getElementById('file-upload-wrapper');

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        let html = '';
        const fileName = file.name.toLowerCase();

        if(fileName.endsWith('.pdf')) {
            html = `<p class="text-gray-700 font-medium">📄 ${file.name}</p>`;
        } else if(fileName.match(/\.(jpg|jpeg|png)$/)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                html = `<img src="${e.target.result}" class="mx-auto rounded-md shadow-sm" style="max-height:150px;" /> 
                        <p class="mt-1 text-gray-700">${file.name}</p>`;
                preview.innerHTML = html;
            }
            reader.readAsDataURL(file);
            return;
        } else if(fileName.match(/\.(dwg|dxf)$/)) {
            html = `<p class="text-gray-700 font-medium">📐 ${file.name}</p>`;
        } else {
            html = `<p class="text-gray-700 font-medium">${file.name}</p>`;
        }

        preview.innerHTML = html;
    });

    // Drag & drop
    wrapper.addEventListener('dragover', (e) => {
        e.preventDefault();
        wrapper.classList.add('border-indigo-500', 'bg-indigo-50');
    });

    wrapper.addEventListener('dragleave', (e) => {
        e.preventDefault();
        wrapper.classList.remove('border-indigo-500', 'bg-indigo-50');
    });

    wrapper.addEventListener('drop', (e) => {
        e.preventDefault();
        wrapper.classList.remove('border-indigo-500', 'bg-indigo-50');
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    });
});
</script>
