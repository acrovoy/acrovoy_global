{{-- Показываем сообщение об успешном обновлении --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6" role="alert">
        {{ session('success') }}
    </div>
@endif

                       <div class="flex gap-8 items-start">
    
    {{-- Левая часть — описание --}}
    <div class="flex-1">
       

        <p class="bg-yellow-50 border border-yellow-200 text-yellow-900
          px-4 py-3 rounded-lg mb-2 text-sm">
    Listing card in catalog. Profile shows verified manufacturer information,
    including country of registration and short business description.
</p>

       
    </div>

    {{-- Правая часть — карточка --}}
    <div class="w-80">
       
    </div>

</div>


<form action="{{ route('manufacturer.company.update') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
    @csrf

    {{-- Company Name --}}
    <div>
        <label class="block font-medium mb-1">Company Name</label>
        <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}"
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
                    old('country_id', $company->country_id ?? null) == $country->id
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
        <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}"
               class="w-full border rounded-md p-2" required>
    </div>

    {{-- Phone --}}
    <div>
        <label class="block font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
               class="w-full border rounded-md p-2">
    </div>

    {{-- Address --}}
    <div>
        <label class="block font-medium mb-1">Address</label>
        <textarea name="address" class="w-full border rounded-md p-2">{{ old('address', $company->address ?? '') }}</textarea>
    </div>



  {{-- Сертификаты --}}
<div class="mt-6">
    <label class="block font-medium mb-2">Certificates</label>

    {{-- Dropzone --}}
    <div id="dropzone2"
         class="w-full border-2 border-dashed border-gray-300 
                h-40 flex flex-col items-center justify-center bg-gray-50 
                rounded-md cursor-pointer">
        <p class="text-gray-500 text-sm">Drag & drop files here or click to upload</p>
    </div>

    <input type="file" name="certificates[]" id="certificates-input2"
           multiple class="hidden"
           accept=".jpg,.jpeg,.png,.webp,.pdf">

    {{-- Существующие сертификаты --}}
    @if($company->certificates->count())
        <div class="certificate-list mt-4 flex flex-wrap gap-2">
    @foreach($company->certificates as $certificate)
        <div class="certificate-item border p-2 rounded flex items-center gap-2 relative" data-id="{{ $certificate->id }}">
            <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" class="text-blue-600 underline truncate max-w-[150px]">
                {{ $certificate->name }}
            </a>
            <button type="button" class="delete-certificate text-red-600 font-bold" data-url="{{ route('manufacturer.certificates.delete', $certificate->id) }}">
                &times;
            </button>
        </div>
    @endforeach
</div>
    @else
        <p class="text-sm text-gray-400 mt-2">No certificates uploaded yet</p>
    @endif
</div>




    {{-- Short Description --}}
    <div>
        <label class="block font-medium mb-1">Short Description</label>
        <input type="text"
            name="short_description"
            value="{{ old('short_description', $company->short_description ?? '') }}"
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
                class="w-full border rounded-md p-2">{{ old('description', $company->description ?? '') }}</textarea>
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
                     src="{{ $company->logo ? asset('storage/' . $company->logo) : asset('images/no-logo.png') }}" 
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
                     src="{{ $company->catalog_image ? asset('storage/' . $company->catalog_image) : asset('images/no-image.png') }}"
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
            <img src="{{ $company->catalog_image ? asset('storage/' . $company->catalog_image) : asset('images/no-logo.png') }}" 
                 class="w-full h-48 object-cover" 
                 alt="{{ $company->name }}">

            <div class="p-4 text-center">
                <h3 class="text-lg font-semibold">{{ $company->name }}</h3>
                <p class="text-gray-600 text-sm">
                    {{ $company->country->name ?? '' }} | {{ $company->short_description }}
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


{{-- JS Drag & Drop --}}
<script>
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('delete-certificate')) return;

    if (!confirm('Delete this certificate?')) return;

    const button = e.target;
    const item = button.closest('.certificate-item');
    const url = button.dataset.url;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // fade + scale animation
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            item.style.opacity = '0';
            item.style.transform = 'scale(0.95)';
            setTimeout(() => item.remove(), 400);

            // обновляем репутацию на странице
            const repBadge = document.querySelector('#reputation-score');
            if (repBadge) repBadge.textContent = data.reputation;
        } else {
            alert(data.message || 'Error deleting certificate');
        }
    })
    .catch(() => alert('Error deleting certificate'));
});
</script>


<script>
const dropzone2 = document.getElementById('dropzone2');

dropzone2.addEventListener('click', () => {
    const input = document.getElementById('certificates-input2');
    input.click();
});

const uploadCertificate = (file) => {
    const formData = new FormData();
    formData.append('certificate', file);

    fetch("{{ route('manufacturer.certificates.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // добавляем новый сертификат в список
            const container = document.querySelector('.certificate-list');
            const div = document.createElement('div');
            div.className = 'certificate-item border p-2 rounded flex items-center gap-2 relative';
            div.dataset.id = data.certificate.id;
            div.innerHTML = `
                <a href="${data.certificate.url}" target="_blank" class="text-blue-600 underline truncate max-w-[150px]">
                    ${data.certificate.name}
                </a>
                <button type="button" class="delete-certificate text-red-600 font-bold" data-url="/dashboard/manufacturer/certificates/${data.certificate.id}">
                    &times;
                </button>
            `;
            container.appendChild(div);

            // тут можно обновить бейджи репутации
            console.log('New reputation:', data.reputation);
        }
    })
    .catch(err => alert('Error uploading certificate'));
};

// обрабатываем drag & drop
dropzone2.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone2.classList.add('border-blue-400', 'bg-blue-50');
});
dropzone2.addEventListener('dragleave', () => {
    dropzone2.classList.remove('border-blue-400', 'bg-blue-50');
});
dropzone2.addEventListener('drop', e => {
    e.preventDefault();
    dropzone2.classList.remove('border-blue-400', 'bg-blue-50');
    for (const file of e.dataTransfer.files) uploadCertificate(file);
});

// обрабатываем выбор через input
document.getElementById('certificates-input2').addEventListener('change', e => {
    for (const file of e.target.files) uploadCertificate(file);
});
</script>