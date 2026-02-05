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
        @php
    $ext = strtolower(pathinfo($certificate->file_path, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
@endphp

<div class="certificate-item border rounded p-2 w-32 flex flex-col items-center gap-2 relative">

    <a href="{{ asset('storage/' . $certificate->file_path) }}"
       target="_blank"
       class="block w-full text-center">

        @if($isImage)
            <img src="{{ asset('storage/' . $certificate->file_path) }}"
                 class="w-full h-24 object-contain rounded"
                 alt="{{ $certificate->name }}">
        @else
            <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded text-gray-500 text-sm">
                PDF
            </div>
        @endif

        <div class="mt-1 text-xs truncate">
            {{ $certificate->name }}
        </div>
    </a>

    <button type="button"
            class="delete-certificate absolute top-1 right-1 text-red-600 font-bold"
            data-url="{{ route('manufacturer.certificates.delete', $certificate->id) }}">
        &times;
    </button>
</div>

    @endforeach
</div>
    @else
        <p class="text-sm text-gray-400 mt-2">No certificates uploaded yet</p>
    @endif
</div>


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