@extends('dashboard.admin.layout')

@section('dashboard-content')

<div x-data="certificateModal()" class="relative">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold">Sellers</h1>

        <form method="GET" class="flex flex-wrap gap-2 items-center">
            {{-- Фильтр по статусу --}}
            <select name="status"
        onchange="this.form.submit()"
        class="border border-gray-300 rounded-md px-3 py-1 pr-8 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none appearance-none bg-white relative">
    <option value="">All</option>
    <option value="pending" @selected($status === 'pending')>Pending</option>
    <option value="active" @selected($status === 'active')>Active</option>
    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
</select>

<style>
select {
    /* Позиционируем стрелку в правом углу */
    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
}
</style>

            {{-- Поиск по имени или email --}}
            <input type="text"
                   name="search"
                   value="{{ $searchFilter ?? '' }}"
                   placeholder="Search by name or email..."
                   class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none"
                   onkeyup="if(event.key === 'Enter') this.form.submit()">
        </form>
    </div>

    <div class="overflow-x-auto bg-white border rounded-lg shadow">
        <table class="w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">ID</th>
                 
                    <th class="px-4 py-2 text-left font-medium">Company Name</th>
                    <th class="px-4 py-2 text-left font-medium">Email</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                    <th class="px-4 py-2 text-left font-medium">Created</th>
                    <th class="px-4 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse($sellers as $seller)
                <tr>
                    <td class="px-4 py-2">{{ $seller->id }}</td>


                   
                    



                    <td class="px-4 py-2 font-medium flex items-center gap-2">

                    @if($seller->is_verified)
                            <!-- Иконка Verified (Shield Check) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 2c.132 0 .263.01.393.03a1 1 0 01.622.31l7 7a1 1 0 01.195 1.013l-4 10a1 1 0 01-.93.647H9.72a1 1 0 01-.93-.647l-4-10a1 1 0 01.195-1.013l7-7a1 1 0 01.622-.31A9.983 9.983 0 0112 2zm-1.707 9.707a1 1 0 011.414-1.414l2.586 2.586 4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-2.293-2.293z" clip-rule="evenodd" />
                            </svg>
                        @endif
                        @if($seller->is_trusted)
                            <!-- Иконка Trusted (Star) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        @endif

                        {{ $seller->name }}

                        <button 
                            @click.prevent="openUploadModal({{ $seller->id }}, '{{ addslashes($seller->name) }}')"
                            class="hover:text-blue-700"
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
                    <td class="px-4 py-2 text-right flex flex-wrap justify-end gap-2">

                        {{-- Open verification modal --}}
                        <button
                            @click.prevent="$dispatch('open-verify-modal', {
                                id: {{ $seller->id }},
                                name: '{{ addslashes($seller->name) }}',
                                isVerified: {{ $seller->is_verified }},
                                isTrusted: {{ $seller->is_trusted }}
                            })"
                            class="text-purple-600 hover:text-purple-800"
                            title="Verify / Trust Seller">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 2c.132 0 .263.01.393.03a1 1 0 01.622.31l7 7a1 1 0 01.195 1.013l-4 10a1 1 0 01-.93.647H9.72a1 1 0 01-.93-.647l-4-10a1 1 0 01.195-1.013l7-7a1 1 0 01.622-.31A9.983 9.983 0 0112 2zm-1.707 9.707a1 1 0 011.414-1.414l2.586 2.586 4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-2.293-2.293z" clip-rule="evenodd" />
                            </svg>
                        </button>

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
    </div>

    {{-- Легенда под таблицей --}}
<div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
    <div class="flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M12 2c.132 0 .263.01.393.03a1 1 0 01.622.31l7 7a1 1 0 01.195 1.013l-4 10a1 1 0 01-.93.647H9.72a1 1 0 01-.93-.647l-4-10a1 1 0 01.195-1.013l7-7a1 1 0 01.622-.31A9.983 9.983 0 0112 2zm-1.707 9.707a1 1 0 011.414-1.414l2.586 2.586 4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-2.293-2.293z" clip-rule="evenodd" />
        </svg>
        <span>Verified</span>
    </div>

    <div class="flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
        </svg>
        <span>Trusted</span>
    </div>
</div>


    <div class="mt-4">
        {{ $sellers->links() }}
    </div>

    {{-- Модалка загрузки сертификатов --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

        <div class="bg-white rounded-lg w-full max-w-md p-6 relative shadow-lg">
            <button @click="close()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-lg">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Upload Certificates for <span x-text="sellerName"></span></h2>

            <div id="modal-dropzone" class="w-full border-2 border-dashed border-gray-300 h-40 flex flex-col items-center justify-center rounded cursor-pointer bg-gray-50 mb-4 transition-colors duration-150">
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

{{-- Модалка Verify / Trust Seller --}}
<div 
    x-data="verifyModal()"
    x-cloak 
    x-show="open"
    x-on:open-verify-modal.window="openModal($event.detail.id, $event.detail.name, $event.detail.isVerified, $event.detail.isTrusted)"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
>
    <div class="bg-white rounded-lg w-full max-w-xs p-6 relative shadow-lg">
        <button @click="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-lg">&times;</button>
        <h2 class="text-lg font-semibold mb-4">Verify / Trust <span x-text="sellerName"></span></h2>

        <div class="flex flex-col gap-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="isVerified" class="h-4 w-4">
                <span>Verified</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="isTrusted" class="h-4 w-4">
                <span>Trusted</span>
            </label>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <button @click="save()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            <button @click="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('verifyModal', () => ({
        open: false,
        sellerId: null,
        sellerName: '',
        isVerified: 0,
        isTrusted: 0,

        openModal(id, name, verified, trusted) {
            this.open = true;
            this.sellerId = id;
            this.sellerName = name;
            this.isVerified = verified;
            this.isTrusted = trusted;
        },

        closeModal() {
            this.open = false;
            this.sellerId = null;
        },

        async save() {
            if (!this.sellerId) return;

            try {
                const res = await fetch(`/dashboard/admin/sellers/${this.sellerId}/verify-trust`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        is_verified: this.isVerified ? 1 : 0,
                        is_trusted: this.isTrusted ? 1 : 0
                    })
                });

                const data = await res.json();

                if (data.success) {
                    alert('Seller updated!');
                    this.closeModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Error updating seller');
                }
            } catch(err) {
                console.error(err);
                alert('Error updating seller');
            }
        }
    }));
});
</script>




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
