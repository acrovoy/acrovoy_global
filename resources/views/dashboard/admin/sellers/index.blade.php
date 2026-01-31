@extends('dashboard.admin.layout')

@section('dashboard-content')

<div x-data="certificateModal()" class="relative">

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Sellers</h1>

    <form method="GET" class="flex gap-2 items-center">

        {{-- Фильтр по статусу --}}
        <select name="status"
                onchange="this.form.submit()"
                class="border rounded-md px-2 py-1 text-sm">
            <option value="">All</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
            <option value="approved" @selected($status === 'approved')>Approved</option>
            <option value="rejected" @selected($status === 'rejected')>Rejected</option>
        </select>

        {{-- Поиск по имени или email --}}
        <input type="text"
               name="search"
               value="{{ $searchFilter ?? '' }}"
               placeholder="Search by name or email..."
               class="border rounded-md px-2 py-1 text-sm"
               onkeyup="if(event.key === 'Enter') this.form.submit()">
    </form>
</div>

<table class="w-full border rounded text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Company Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($sellers as $seller)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $seller->id }}</td>
            <td class="px-4 py-2 font-medium">
                {{ $seller->name }}

               <button 
                    @click.prevent="openUploadModal({{ $seller->id }}, '{{ addslashes($seller->name) }}')"
                    class="ml-2 hover:text-blue-700"
                    title="Upload Certificates">

                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="h-5 w-5 inline {{ $seller->certificates->count() ? 'text-blue-600' : 'text-gray-300' }}" 
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V7.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0014.586 1H7a2 2 0 00-2 2v16a2 2 0 002 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 13h6M9 17h6"/>
                    </svg>
                </button>
            </td>
            <td class="px-4 py-2">{{ $seller->email ?? '—' }}</td>
            <td class="px-4 py-2">
                <span class="px-2 py-1 rounded text-xs
                    @if($seller->status === 'active') bg-green-100 text-green-700
                    @elseif($seller->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($seller->status === 'blocked') bg-red-100 text-red-700
                    @else bg-gray-200 text-gray-600 @endif">
                    {{ ucfirst($seller->status) }}
                </span>
            </td>
            <td class="px-4 py-2">{{ $seller->created_at->format('Y-m-d') }}</td>
            <td class="px-4 py-2 text-right space-x-2">
                <a href="{{ route('admin.sellers.show', $seller->id) }}" class="text-blue-600 hover:underline">View</a>
                <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="text-amber-600 hover:underline">Edit</a>
                <a href="#" class="text-red-600 hover:underline">Delete</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No sellers found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $sellers->links() }}
</div>

{{-- Модалка загрузки сертификатов --}}
<div x-show="open" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
     
    <div class="bg-white rounded-lg w-96 p-6 relative">
        <button @click="close()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h2 class="text-lg font-semibold mb-4">Upload Certificates for <span x-text="sellerName"></span></h2>

        <div id="modal-dropzone" class="w-full border-2 border-dashed border-gray-300 h-40 flex flex-col items-center justify-center rounded cursor-pointer bg-gray-50 mb-4">
            <p class="text-gray-500 text-sm">Drag & drop files here or click to select</p>
        </div>

        <input type="file" id="modal-certificates-input" multiple class="hidden" accept=".jpg,.jpeg,.png,.webp,.pdf">

        <div class="flex justify-end gap-2">
            <button @click="uploadFiles()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
            <button @click="close()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
        </div>

        <div class="certificate-list mt-4 flex flex-wrap gap-2"></div>
    </div>
</div>

</div> {{-- конец x-data --}}

<script>
function certificateModal() {
    return {
        open: false,
        sellerId: null,
        sellerName: '',
        files: [],

        openUploadModal(id, name) {
            this.open = true;
            this.sellerId = id;
            this.sellerName = name;
            this.files = [];
            this.$nextTick(() => {
                this.initDropzone();
                this.loadExistingCertificates();
            });
        },

        close() {
            this.open = false;
            this.files = [];
        },

        initDropzone() {
            const dropzone = document.getElementById('modal-dropzone');
            const input = document.getElementById('modal-certificates-input');

            dropzone.addEventListener('click', () => input.click());

            dropzone.addEventListener('dragover', e => {
                e.preventDefault();
                dropzone.classList.add('border-blue-400', 'bg-blue-50');
            });

            dropzone.addEventListener('dragleave', e => {
                dropzone.classList.remove('border-blue-400', 'bg-blue-50');
            });

            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-400', 'bg-blue-50');
                this.files.push(...Array.from(e.dataTransfer.files));
            });

            input.addEventListener('change', e => {
                this.files.push(...Array.from(e.target.files));
            });
        },

        async uploadFiles() {
            if (!this.files.length) return alert('No files selected');

            const formData = new FormData();
            this.files.forEach(f => formData.append('certificates[]', f));

            try {
                const res = await fetch(`/dashboard/admin/sellers/${this.sellerId}/certificates`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    this.files = [];
                    this.loadExistingCertificates();
                    alert('Certificates uploaded!');
                } else {
                    alert(data.message || 'Error uploading certificates');
                }
            } catch (err) {
                console.error(err);
                alert('Error uploading certificates');
            }
        },

        async loadExistingCertificates() {
            const res = await fetch(`/dashboard/admin/sellers/${this.sellerId}/certificates/list`);
            const data = await res.json();
            if (data.success) {
                const container = document.querySelector('.certificate-list');
                container.innerHTML = '';
                data.certificates.forEach(cert => {
                    const div = document.createElement('div');
                    div.className = 'certificate-item border p-2 rounded flex items-center gap-2 relative';
                    div.dataset.id = cert.id;
                    div.innerHTML = `
                        <a href="${cert.url}" target="_blank" class="text-blue-600 underline truncate max-w-[150px]">${cert.name}</a>
                        <button type="button" class="delete-certificate text-red-600 font-bold" data-id="${cert.id}">&times;</button>
                    `;
                    container.appendChild(div);
                });
            }
        }
    }
}

// Удаление сертификата
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('delete-certificate')) return;

    if (!confirm('Delete this certificate?')) return;

    const button = e.target;
    const item = button.closest('.certificate-item');
    const certId = button.dataset.id;

    fetch(`/dashboard/admin/sellers/certificates/${certId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            item.remove();
        } else {
            alert(data.message || 'Error deleting certificate');
        }
    })
    .catch(() => alert('Error deleting certificate'));
});
</script>

@endsection
