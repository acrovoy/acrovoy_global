






@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex justify-between items-center mb-6">

{{-- Показываем сообщение об успешном обновлении --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6" role="alert">
        {{ session('success') }}
    </div>
@endif


    <h1 class="text-2xl font-bold">
        Edit Seller
    </h1>


   



    <a href="{{ route('admin.sellers.index') }}"
       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
        ← Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">


<form action="{{ route('admin.sellers.update', $seller->id) }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-4">
    @csrf
    @method('PUT')

     {{-- Status --}}
<div>
    <label class="block font-medium mb-1">Status</label>
    <select name="status" class="w-full border rounded-md p-2">
        <option value="active" @selected($seller->status === 'active')>Active</option>
        <option value="inactive" @selected($seller->status === 'inactive')>Inactive</option>
        <option value="blocked" @selected($seller->status === 'blocked')>Blocked</option>
    </select>
</div>

    {{-- Company Name --}}
    <div>
        <label class="block font-medium mb-1">Company Name</label>
        <input type="text" name="name" value="{{ old('name', $seller->name ?? '') }}"
               class="w-full border rounded-md p-2" required>
    </div>

    {{-- Registration Country --}}
<div>
    <label class="block font-medium mb-1">Registration Country</label>

    <select name="country_id"
            class="w-full border rounded-md p-2">
        <option value="">Select country</option>

        @foreach($countries as $country)
            <option value="{{ $country->id }}"
                @selected(
                    old('country_id', $seller->country_id ?? null) == $country->id
                )
            >
                {{ $country->name }}
            </option>
        @endforeach
    </select>
</div>


    {{-- Email --}}
    <div>
        <label class="block font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $seller->email ?? '') }}"
               class="w-full border rounded-md p-2" required>
    </div>

    {{-- Phone --}}
    <div>
        <label class="block font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $seller->phone ?? '') }}"
               class="w-full border rounded-md p-2">
    </div>

    {{-- Address --}}
    <div>
        <label class="block font-medium mb-1">Address</label>
        <textarea name="address" class="w-full border rounded-md p-2">{{ old('address', $seller->address ?? '') }}</textarea>
    </div>



  




    {{-- Short Description --}}
    <div>
        <label class="block font-medium mb-1">Short Description</label>
        <input type="text"
            name="short_description"
            value="{{ old('short_description', $seller->short_description ?? '') }}"
            class="w-full border rounded-md p-2"
            maxlength="255">
        <p class="text-xs text-gray-500 mt-1">
            Short text for cards and lists (max 255 chars)
        </p>
    </div>

    {{-- Company Description --}}
    <div>
        <label class="block font-medium mb-1">Company Description</label>
        <textarea name="description"
                rows="5"
                class="w-full border rounded-md p-2">{{ old('description', $seller->description ?? '') }}</textarea>
    </div>

    



   


<div class="flex flex-col lg:flex-row gap-8 items-start">
    
    {{-- Левая часть — описание --}}
    <div class="flex-1">
        {{-- Logo Drag & Drop --}}
        <div>
            <label class="block font-medium mb-2">Company Logo</label>
            <div id="logo-dropzone" 
                 class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center bg-gray-50 relative cursor-pointer overflow-hidden group">
                <img id="logo-preview" 
                     src="{{ $seller->logo ? asset('storage/' . $seller->logo) : asset('images/no-logo.png') }}" 
                     alt="Logo Preview" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-md">
                    <span class="text-white text-sm">Change</span>
                </div>
            </div>
            <input type="file" name="logo" accept="image/*" id="logo-input" class="hidden">
            <p class="text-sm text-gray-500 mt-1">Drag & drop or click to select a logo</p>
        </div>

        {{-- Catalog Image --}}
        <div class="mt-4" style="width: 240px;">
            <label class="block font-medium mb-2">Catalog Image</label>
            <div id="catalog-dropzone"
                 class="border-2 border-dashed border-gray-300 
                        h-40 flex items-center justify-center bg-gray-50 relative cursor-pointer overflow-hidden group">
                <img id="catalog-preview"
                     src="{{ $seller->catalog_image ? asset('storage/' . $seller->catalog_image) : asset('images/no-image.png') }}"
                     class="object-cover max-w-full max-h-full">
                <div class="absolute inset-0 bg-black bg-opacity-30
                            flex items-center justify-center opacity-0
                            group-hover:opacity-100 transition">
                    <span class="text-white text-sm">Change</span>
                </div>
            </div>
            <input type="file" name="catalog_image" accept="image/*" id="catalog-input" class="hidden">
            <p class="text-sm text-gray-500 mt-1">Used in supplier catalog cards</p>
        </div>
    </div>

    {{-- Правая часть — карточка --}}
    <div class="w-full lg:w-[340px] flex flex-col items-center mt-6 lg:mt-0">
        {{-- Текст над карточкой --}}
        <p class="text-gray-500 text-sm mb-2 mt-12">Example Company Card</p>

        <a href=""
           class="block bg-white rounded-xl shadow hover:shadow-2xl transition overflow-hidden supplier-card w-full max-w-[340px]">
            <img src="{{ $seller->catalog_image ? asset('storage/' . $seller->catalog_image) : asset('images/no-logo.png') }}" 
                 class="w-full h-48 object-cover" 
                 alt="{{ $seller->name }}">

            <div class="p-4 text-center">
                <h3 class="text-lg font-semibold">{{ $seller->name }}</h3>
                <p class="text-gray-600 text-sm">
                    {{ $seller->country->name ?? '' }} | {{ $seller->short_description }}
                </p>
            </div>
        </a>
    </div>




    {{-- еще Правее часть — карточка --}}
    <div class="w-full lg:w-[50px] flex flex-col items-center mt-6 lg:mt-0">
       
    </div>


</div>






    {{-- SUBMIT --}}
    <div class="pt-6 border-t">
        <button
            type="submit"
            class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4
                   rounded-xl text-lg font-semibold transition">
            Save Company Profile
        </button>
    </div>
</form>

</div>



{{-- JS для drag&drop превью --}}
<script>
const logoInput = document.getElementById('logo-input');
const logoPreview = document.getElementById('logo-preview');
const dropzone = document.getElementById('logo-dropzone');

// Клик по зоне открывает выбор файла
dropzone.addEventListener('click', () => logoInput.click());

// Обработка выбора файла
logoInput.addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
        logoPreview.src = URL.createObjectURL(file);
    }
});

// Drag & drop
dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('border-blue-400', 'bg-blue-50');
});

dropzone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');

    const file = e.dataTransfer.files[0];
    if (file) {
        logoInput.files = e.dataTransfer.files; // присвоить input
        logoPreview.src = URL.createObjectURL(file);
    }
});
</script>

<script>
const catalogInput = document.getElementById('catalog-input');
const catalogPreview = document.getElementById('catalog-preview');
const catalogDropzone = document.getElementById('catalog-dropzone');

catalogDropzone.addEventListener('click', () => catalogInput.click());

catalogInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) catalogPreview.src = URL.createObjectURL(file);
});

catalogDropzone.addEventListener('dragover', e => {
    e.preventDefault();
    catalogDropzone.classList.add('border-blue-400', 'bg-blue-50');
});

catalogDropzone.addEventListener('dragleave', () => {
    catalogDropzone.classList.remove('border-blue-400', 'bg-blue-50');
});

catalogDropzone.addEventListener('drop', e => {
    e.preventDefault();
    catalogDropzone.classList.remove('border-blue-400', 'bg-blue-50');

    const file = e.dataTransfer.files[0];
    if (file) {
        catalogInput.files = e.dataTransfer.files;
        catalogPreview.src = URL.createObjectURL(file);
    }
});
</script>




@endsection